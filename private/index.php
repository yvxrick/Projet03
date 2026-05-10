<?php
require_once "../app/functions/session_manager.php";
require_once "../app/database/user.php";
$user_obj = new user($_SESSION["email"]);
$statut = $user_obj->get_statut();
switch ($statut) {
    case 1:
        require "admin.php";
        break;
    default:
        require "user.php";
        break;
}
