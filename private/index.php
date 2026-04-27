<?php
require_once "../app/functions/session_manager.php";
$statut = $_SESSION["statut"];
switch ($statut) {
    case 1:
        require "admin.php";
        break;
    default:
        require "user.php";
        break;
}
