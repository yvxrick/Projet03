<?php
require "../app/auth/auth_email.php";
if (isset($_GET["token"])) {
    $token = $_GET["token"];
    switch (authenticate_email($token)) {
        case "OK":
            echo "Votre courriel à été confirmé. Vous pouvez maintenant vous connecté.";
            break;
        case "This email is already verified.":
            echo "Ce courriel à déjà été vérifié.";
            break;
        default:
            header("HTTP/1.0 400");
            echo "Invalid token.";
    }
    die();
}
header("HTTP/1.0 400");
echo "Token is missing.";
