<?php
require_once "../database/user.php";
require_once "../functions/session_manager.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email = $_POST["email"] ?? null;
    $passwd = $_POST["password"] ?? null;

    if ($email == null || $passwd == null) {
        header("HTTP/1.0 400");
        echo "Missing credentials.";
        die();
    }

    $user_obj = new user($email);
    if (!$user_obj->exists()) {
        echo "Invalid credentials.";
        die();
    }
    if (!($user_obj->get_passwd() === $passwd)) {
        echo "Invalid credentials.";
        die();
    }
    if (!($user_obj->is_authenticated())) {
        echo "Not authenticated.";
        die();
    }

    login_user($user_obj->get_id(), $user_obj->get_email(), $user_obj->get_statut());
    $user_obj->add_connection();

    echo "OK";


    die();
}
header("HTTP/1.0 400");
echo "Only POST requests are allowed.";