<?php
require_once "../functions/session_manager.php";
require_once "../database/annonces.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // image info
    $images_dir = $_SERVER['DOCUMENT_ROOT'] . "private/ads-images/";
    $file = $_FILES["ad-photo"];
    $allowed_image_types = ["image/gif", "image/png", "image/jpeg"];
    $max_image_size = 5000000; // bytes (5MB max)
    $target_file = $images_dir . basename($file["name"]);

    // ad info
    $ad_title = $_POST["ad-desc-abr"] ?? null;
    $ad_desc = $_POST["ad-desc-full"] ?? null;
    $ad_category = $_POST["ad-categorie"] ?? null;
    $ad_price = $_POST["ad-price"] ?? null;
    $ad_photo = basename($file["name"]);
    $ad_state = $_POST["ad-state"] ?? null;
    $noUtilisateur = $_SESSION["user_id"];

    if ($file["size"] > $max_image_size) { // Image too large
        http_response_code(400);
        exit("Image is too large.");
    }

    if (!(in_array($file["type"], $allowed_image_types))) { // File not an image
        http_response_code(400);
        exit("File is not an image.");
    }

    if (file_exists($target_file)) { // File already exists
        http_response_code(400);
        exit("This file already exists.");
    }

    if (!(is_dir($images_dir))) { // Ads folder dosen't exist
        http_response_code(500);
        exit("The ads folder dosen't exist.");
    }

    if ($ad_title == null || $ad_desc == null || $ad_category == null || $ad_price == null || $ad_photo == null || $ad_state == null) {
        http_response_code(400);
        exit("One or more parameters was not set.");
    }

    if (strlen($ad_title) > 50) {
        http_response_code(400);
        exit("Ad title is too large.");
    }

    if (strlen($ad_desc) > 250) {
        http_response_code(400);
        exit("Ad description is too large.");
    }

    if (strlen($file["name"]) > 50) {
        http_response_code(400);
        exit("Ad photo name is too large.");
    }

    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        $annonces_obj = new annonces();
        echo var_dump($annonces_obj->add_ad($ad_title, $ad_desc, $ad_category, $ad_price, $ad_photo, $ad_state, $noUtilisateur));
        echo "OK";
    } else {
        echo "File upload failed.";
    }
    exit();
}
http_response_code(400);
exit("Only POST request are allowed."); 