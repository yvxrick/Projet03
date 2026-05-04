<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "app/functions/session_manager.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "app/database/annonces.php";
if (isset($_GET["id"]) && $_GET["delete"] == "true") {
    $ad_id = $_GET["id"];
    $ads_obj = new annonces();
    $ads_obj->remove_ad($ad_id);
    echo "OK";
    exit;
}
http_response_code(400);
echo "Bad request.";