<?php
require $_SERVER['DOCUMENT_ROOT'] . "/Projet03/app/database/database.php";
$con = Database::Connect();

function authenticate_email($token) {
    global $con;

    // Check if passed email needs to be verified
    $info = $con->query("SELECT NoUtilisateur, Statut FROM utilisateurs WHERE AutresInfos = '$token'")->fetch_assoc();
    // Token passed is non existing
    if ($con->affected_rows < 1) {
        return "Invalid token.";
    }
    $user_id = $info["NoUtilisateur"];
    $user_status = intval($info["Statut"]);

    // Email is already verified
    if ($user_status === 1) {
        return "This email is already verified.";
    }
    $query = "UPDATE utilisateurs SET statut = 1 WHERE NoUtilisateur = '$user_id'";
    $con->query($query);
    return "OK";    
}