<?php
session_start();

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // Troca o código pelo token de acesso
    $client_id = '1018697403337532';
    $client_secret = '7e03f4d64831da589b9781b64cb4ddfd';
    $redirect_uri = 'https://script.gestaotop.com/_insta_login.php';
    $token_url = 'https://api.instagram.com/oauth/access_token';

    $fields = [
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'grant_type' => 'authorization_code',
        'redirect_uri' => $redirect_uri,
        'code' => $code
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $token_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
    $response = curl_exec($ch);
    curl_close($ch);

    $token_info = json_decode($response, true);

    if (isset($token_info['access_token'])) {
        // Sucesso na obtenção do token
        $access_token = $token_info['access_token'];
        $user_id = $token_info['user_id'];
    } else {
        // Caso haja erro, usa um token fixo para teste
        $access_token = 'IGQWRNNzY5NDJZASDM5dXVCOWZAQQWQ4bjBqTDJTUDJHYk5tYlJrYnVDcFFiOXBFZAFlIQmIxVzhfN1dUNHU3dy1lYWpBd2g5MmFlQjJhZAzB2djg3MFNKWVpuRE5NdnVyQU9MMXJyN0NOUWplbndnMXB6czZAqeFA2QWcZD';
        $user_id = 'FIXED_USER_ID';  // ID do usuário para testes
    }

    // Armazena o token de acesso para uso posterior
    $_SESSION['access_token'] = $access_token;
    $_SESSION['user_id'] = $user_id;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Postar no Instagram</title>
</head>
<body>
    <h1>Postar Imagem no Instagram</h1>
    <form method="POST">
        <label for="image_url">COLE A URL DA IMAGEM QUE VOCÊ QUER POSTAR:</label><br>
        <input type="text" id="image_url" name="image_url" required><br><br>
        <button type="submit" name="post_image">POSTAR</button>
    </form>

    <?php
    if (isset($_POST['post_image'])) {
        $image_url = $_POST['image_url'];
        $access_token = $_SESSION['access_token'];
        $user_id = $_SESSION['user_id'];

        // URL da API para postagem de mídia no Instagram
        $post_url = "https://graph.instagram.com/$user_id/media";

        // Etapa 1: Cria a mídia no Instagram
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $post_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'image_url' => $image_url,
            'caption' => 'Postado via API',
            'access_token' => $access_token
        ]));
        $response = curl_exec($ch);
        curl_close($ch);

        $media = json_decode($response, true);
        if (isset($media['id'])) {
            $media_id = $media['id'];

            // Etapa 2: Publica a mídia no perfil
            $publish_url = "https://graph.instagram.com/$user_id/media_publish";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $publish_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                'creation_id' => $media_id,
                'access_token' => $access_token
            ]));
            $response = curl_exec($ch);
            curl_close($ch);

            echo "<p>Imagem postada com sucesso!</p>";
        } else {
            echo "<p>Erro ao criar mídia: " . json_encode($media) . "</p>";
        }
    }
    ?>
</body>
</html>
