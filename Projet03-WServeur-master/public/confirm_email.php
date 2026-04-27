<?php
require "../app/auth/auth_email.php";
if (isset($_GET["token"])) {
    $token = $_GET["token"];
    switch (authenticate_email($token)) {
        case "OK":
            require "../public/email_confirmed.php";
            break;
        case "This email is already verified.":
            require "../public/email_already_confirmed.php";
            break;
        default:
            header("HTTP/1.0 400");
            echo "Invalid token.";
    }
    die();
}
header("HTTP/1.0 400");
echo "Token is missing.";
