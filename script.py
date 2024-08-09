from flask import Flask, request, redirect, jsonify
import requests
import os
from dotenv import load_dotenv
import threading
from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options import Options
import time
import webbrowser

# Configuração do Flask
load_dotenv()

app = Flask(__name__)

app_id = os.getenv('APP_ID')
app_secret = os.getenv('APP_SECRET')
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
    
    if response.status_code != 200:
        return f'Erro ao obter token de acesso: {response.text}', response.status_code

    access_token_info = response.json()
    access_token = access_token_info.get('access_token')

    if not access_token:
        return 'Erro ao obter token de acesso.', 400
    
    # Armazenar o token de acesso em um arquivo
    with open('insta_login/token.txt', 'w') as token_file:
        token_file.write(access_token)
    
    # Retornar uma resposta informando que o token foi salvo
    return jsonify(message='Token de acesso salvo com sucesso em token.txt')

def run_flask():
    app.run(port=8000)

def open_browser():

    time.sleep(4)
    webbrowser.open('http://127.0.0.1:8000')

if __name__ == '__main__':
    # Criar e iniciar as threads
    flask_thread = threading.Thread(target=run_flask)
    selenium_thread = threading.Thread(target=open_browser)
    
    flask_thread.start()
    selenium_thread.start()
    
    # Esperar que ambas as threads terminem
    flask_thread.join()
    selenium_thread.join()
