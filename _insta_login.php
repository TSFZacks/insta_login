<?php
session_start();
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
        <label for="token">Token fixo (substitua pelo seu token, caso tenha):</label><br>
        <input type="text" id="token" name="token" value="" required><br><br>

        <label for="image_url">Cole a URL da imagem que você quer postar:</label><br>
        <input type="text" id="image_url" name="image_url" required><br><br>
        
        <button type="submit" name="post_image">Postar</button>
    </form>

    <?php
    if (isset($_POST['post_image'])) {
        $image_url = $_POST['image_url'];
        $access_token = $_POST['token'];  // Usa o token inserido pelo usuário
        $user_id = 'FIXED_USER_ID';  // ID fixo do usuário para teste

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
