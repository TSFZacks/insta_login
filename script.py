
from flask import Flask, request, redirect
import requests
import json
import time

app = Flask(__name__)

app_id = '556346486573341'
app_secret = '7226a077fa336c63b4ebb042d66d1dcb'
redirect_uri = 'http://localhost:8000/callback'

@app.route('/')
def index():
    auth_url = f'https://www.facebook.com/v10.0/dialog/oauth?client_id={app_id}&redirect_uri={redirect_uri}&scope=instagram_basic,instagram_manage_insights,pages_show_list'
    return redirect(auth_url)

@app.route('/callback')
def callback():
    code = request.args.get('code')
    if not code:
        return 'Código de autorização não encontrado.', 400

    token_url = 'https://graph.facebook.com/v10.0/oauth/access_token'
    token_params = {
        'client_id': app_id,
        'redirect_uri': redirect_uri,
        'client_secret': app_secret,
        'code': code,
    }

    response = requests.get(token_url, params=token_params)

    time.sleep(3)

    access_token_info = json.loads(str(response.text))
    access_token = access_token_info.get('access_token')

    if not access_token:
        return 'Erro ao obter token de acesso.', 400
    
    print(access_token)

if __name__ == '__main__':
    app.run(port=8000)



