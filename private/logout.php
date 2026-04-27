<?php
require_once "../app/functions/session_manager.php";
$user_obj = new user($_SESSION["email"]);
$user_obj->add_disconnection();
logout_user();
header("Location: https://projet03-wserveur.alwaysdata.net/");
echo "OK";