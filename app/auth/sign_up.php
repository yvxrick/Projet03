<?php
header("Access-Control-Allow-Origin: *");
require "../email/email.php";
require "../database/database.php";

$con = Database::Connect();
$email_to_validate = 0;


if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email = $_POST["email"] ?? null;
    $email_confirm = $_POST["email-confirm"] ?? null;
    $password = $_POST["password"] ?? null;

    $regex_email = "/^[\w\-\.]+@([\w-]+\.)+[\w-]{2,}$/";
    $regex_password = "/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{5,15}$/";

    $valid_email = preg_match($regex_email, $email);
    $valid_passwd = preg_match($regex_password, $password);
    
    // Request contains no information
    if ($email == null & $email_confirm == null && $password == null) {
        header("HTTP/1.0 400");
        echo "The request dosen't contain any information.";
        die();
    }
    // Both emails aren't the same
    if ($email !== $email_confirm) {
        header("HTTP/1.0 400");
        echo "Both emails do not match.";
        die();
    }

    if (!$valid_email) {
        header("HTTP/1.0 400");
        echo "The email does not match the expected format.";
        die();
    }

    if (!$valid_passwd) {
        header("HTTP/1.0 400");
        echo "The password does not match the expected format.";
        die();
    }

    // Verify if email is already taken
    $con->query("SELECT * FROM utilisateurs WHERE Courriel = '$email'");
    if ($con->affected_rows > 0) {
        header("HTTP/1.0 409");
        echo "This email is already taken.";
        die();
    }

    $user_hash = bin2hex(random_bytes(16));

    $query = "INSERT INTO utilisateurs(Courriel, MotDePasse, Statut, AutresInfos) VALUES (?, ?, ?, ?)";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ssis", $email, $password, $email_to_validate, $user_hash);
    $stmt->execute();

    $email_obj = new email();
    if ($email_obj->send_confirmation_email($email, $user_hash)) {
        echo "OK";
    } // Email failed to send
    else {
        echo "OK; Email failed to send";
    }
    die();
    

}
header("HTTP/1.0 400");
echo "Only POST requests are allowed.";