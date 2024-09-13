<?php
session_start();

$CLIENT_ID = '477575051691928';
$CLIENT_SECRET = '9b690735a1df9c3bfdcbbb0097427228';
$REDIRECT_URI = 'https://www.google.com/';

$AUTH_URL = 'https://api.instagram.com/oauth/authorize';
$TOKEN_URL = 'https://api.instagram.com/oauth/access_token';

function redirectToInstagramAuth() {
    global $CLIENT_ID, $REDIRECT_URI, $AUTH_URL;
    $auth_redirect_url = $AUTH_URL . "?client_id=$CLIENT_ID&redirect_uri=$REDIRECT_URI&scope=user_profile,user_media&response_type=code";
    header('Location: ' . $auth_redirect_url);
    exit();
}

function exchangeCodeForToken($code) {
    global $CLIENT_ID, $CLIENT_SECRET, $REDIRECT_URI, $TOKEN_URL;

    $data = array(
        'client_id' => $CLIENT_ID,
        'client_secret' => $CLIENT_SECRET,
        'grant_type' => 'authorization_code',
        'redirect_uri' => $REDIRECT_URI,
        'code' => $code
    );

    $ch = curl_init($TOKEN_URL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode != 200) {
        return array('error' => 'Erro ao trocar código por token: ' . $response);
    }

    return json_decode($response, true);
}

if ($_SERVER['REQUEST_URI'] == '/') {
    redirectToInstagramAuth();
}

if ($_SERVER['REQUEST_URI'] == '/callback') {
    if (!isset($_GET['code'])) {
        http_response_code(400);
        echo "Erro: Código de autorização não foi retornado!";
        exit();
    }

    $code = $_GET['code'];
    $token_response = exchangeCodeForToken($code);

    if (isset($token_response['error'])) {
        http_response_code(400);
        echo $token_response['error'];
        exit();
    }

    $access_token = $token_response['access_token'];
    $user_id = $token_response['user_id'];

    $_SESSION['access_token'] = $access_token;
    $_SESSION['user_id'] = $user_id;

    echo "Autenticação bem-sucedida! Token de Acesso: $access_token (User ID: $user_id)";
}
