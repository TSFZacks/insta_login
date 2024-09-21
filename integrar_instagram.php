<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Integração com Instagram</title>
</head>
<body>
    <form action="https://api.instagram.com/oauth/authorize">
        <input type="hidden" name="client_id" value="1018697403337532">
        <input type="hidden" name="redirect_uri" value="https://script.gestaotop.com/_insta_login.php">
        <input type="hidden" name="scope" value="user_profile,user_media">
        <input type="hidden" name="response_type" value="code">
        <button type="submit">INTEGRAR COM INSTAGRAM</button>
    </form>
</body>
</html>
