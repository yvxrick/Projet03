<?php
require_once "../functions/session_manager.php";
require_once "../database/annonces.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    if (empty($_FILES)) { // No images sent
        http_response_code(400);
        exit("No images were sent.");
    }
    
    // image info
    $images_dir = $_SERVER['DOCUMENT_ROOT'] . "private/ads-images/";
    $file = $_FILES["ad-photo"];
    $image_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $ad_hash = strval(bin2hex(random_bytes(16)) . ".$image_extension"); // hash for the ad image
    $allowed_image_types = ["image/gif", "image/png", "image/jpeg"];
    $max_image_size = 5000000; // bytes (5MB max)
    $target_file = $images_dir . $ad_hash;

    // ad info
    $ad_title = $_POST["ad-desc-abr"] ?? null;
    $ad_desc = $_POST["ad-desc-full"] ?? null;
    $ad_category = $_POST["ad-categorie"] ?? null;
    $ad_price = $_POST["ad-price"] ?? null;
    $ad_state = $_POST["ad-state"] ?? null;
    $noUtilisateur = $_SESSION["user_id"] ?? null;

    if ($noUtilisateur == null) {
        http_response_code(400);
        exit("You need to be logged in to add an ad.");
    }

    if ($file["size"] > $max_image_size) { // Image too large
        http_response_code(400);
        exit("Image is too large.");
    }

    if (!(in_array($file["type"], $allowed_image_types))) { // File not an image
        http_response_code(400);
        exit("File is not an image.");
    }

    if (file_exists($target_file)) {
        http_response_code(400);
        exit("This file already exists");
    }

    if (!(is_dir($images_dir))) { // Ads folder dosen't exist
        http_response_code(500);
        exit("The ads folder dosen't exist.");
    }

    if ($ad_title == null || $ad_desc == null || $ad_category == null || $ad_price == null || $ad_state == null) {
        http_response_code(400);
        exit("One or more fields was not set.");
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

    if (trim($ad_title) === "") {
        http_response_code(400);
        exit("The ad title cannot be empty.");
    }


    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        $annonces_obj = new annonces();
        $annonces_obj->add_ad(trim($ad_title), trim($ad_desc), $ad_category, $ad_price, $ad_hash, $ad_state, $noUtilisateur);
        echo "OK";
    } else {
        echo "File upload failed.";
    }
    exit();
}
http_response_code(400);
exit("Only POST request are allowed."); 