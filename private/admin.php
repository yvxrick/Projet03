<?php
require_once "../app/functions/session_manager.php";

if (!is_admin()) { // Redirige un utilisateur qui n'est pas admin
    header("HTTP/1.0 403");
    require "./forbidden.html";
    die(403);
}
echo "Admin page";