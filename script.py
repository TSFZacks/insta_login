from flask import Flask, redirect, request, session, url_for
import requests
import os

app = Flask(__name__)
app.secret_key = r"b'9\xfa\xd1\xcdT\x83\t\xee*\x00\xc7Q@ +W\x8d\r;\xe7\xef\xd9\xc1\xfa'"

CLIENT_ID = '477575051691928'
CLIENT_SECRET = '7d3a2d963e5c5e9e7ffdb6a76b90854b'
REDIRECT_URI = 'https://www.google.com/'

AUTH_URL = 'https://api.instagram.com/oauth/authorize'
TOKEN_URL = 'https://api.instagram.com/oauth/access_token'


@app.route('/')
def home():

    auth_redirect_url = (
        f"{AUTH_URL}?client_id={CLIENT_ID}&redirect_uri={REDIRECT_URI}"
        "&scope=user_profile,user_media&response_type=code"
    )
    return redirect(auth_redirect_url)

@app.route('/callback')
def callback():

    code = request.args.get('code')
    
    if not code:
        return "Erro: Código de autorização não foi retornado!", 400

    data = {
        'client_id': CLIENT_ID,
        'client_secret': CLIENT_SECRET,
        'grant_type': 'authorization_code',
        'redirect_uri': REDIRECT_URI,
        'code': code
    }
    
    response = requests.post(TOKEN_URL, data=data)
    
    if response.status_code != 200:
        return f"Erro ao trocar código por token: {response.text}", 400
    
    access_token_info = response.json()
    access_token = access_token_info.get('access_token')
    user_id = access_token_info.get('user_id')

    session['access_token'] = access_token
    session['user_id'] = user_id
    
    return f"Autenticação bem-sucedida! Token de Acesso: {access_token} (User ID: {user_id})"


if __name__ == "__main__":
    app.run(debug=True, port=4998)
