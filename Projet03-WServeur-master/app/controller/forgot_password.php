<?php
require "../database/user.php";
require "../email/email.php";

$email = $_GET["email"];
$user_obj = new user($email);
if (isset($_GET["email"])) {
    if (!$user_obj->exists()) {
        echo "Email was not found.";
        die();
    }
    $email_obj = new email();

    $user_email = $user_obj->get_email();
    $user_passwd = $user_obj->get_passwd();

    if ($email_obj->send_forgot_passwd_email($user_email, $user_passwd)) {
        echo "OK";
    } else {
        header("HTTP/1.0 500");
        echo "The email was not sent due to an internal server error.";
    }



    die();
}
header("HTTP/1.0 400");
echo "Email was not specified.";