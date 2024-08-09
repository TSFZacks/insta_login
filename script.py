from flask import Flask, redirect, request, session, url_for
import requests
import os

app = Flask(__name__)  # Corrigido aqui
app.secret_key = r"b'9\xfa\xd1\xcdT\x83\t\xee*\x00\xc7Q@ +W\x8d\r;\xe7\xef\xd9\xc1\xfa'"

# Substitua pelos valores do seu app Meta
CLIENT_ID = os.getenv('APP_ID')
CLIENT_SECRET = os.getenv('APP_SECRET')
REDIRECT_URI = 'http://localhost:5000/callback'  # O mesmo URI que você configurou no Meta

# URL de autorização do Instagram
AUTH_URL = 'https://api.instagram.com/oauth/authorize'
TOKEN_URL = 'https://api.instagram.com/oauth/access_token'


@app.route('/')
def home():
    # Redireciona o usuário para a página de autenticação do Instagram
    auth_redirect_url = (
        f"{AUTH_URL}?client_id={CLIENT_ID}&redirect_uri={REDIRECT_URI}"
        "&scope=user_profile,user_media&response_type=code"
    )
    return redirect(auth_redirect_url)


@app.route('/callback')
def callback():
    # Recebe o código de autorização do Instagram
    code = request.args.get('code')
    
    if not code:
        return "Erro: Código de autorização não foi retornado!", 400

    # Troca o código de autorização por um token de acesso
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

    # Salvar o token de acesso na sessão ou em um banco de dados
    session['access_token'] = access_token
    session['user_id'] = user_id
    
    return f"Autenticação bem-sucedida! Token de Acesso: {access_token} (User ID: {user_id})"


if __name__ == "__main__":  # Corrigido aqui
    app.run(debug=True)
