<?php
require_once "../functions/session_manager.php";
require_once "../database/annonces.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $ads_obj = new annonces();

    $image_extension;
    // image info
    $images_dir = $_SERVER['DOCUMENT_ROOT'] . "private/ads-images/";
    $file = $_FILES["ad-photo"] ?? null;
    $allowed_image_types = ["image/gif", "image/png", "image/jpeg"];
    $max_image_size = 5000000; // bytes (5MB max)

    // ad info
    $ad_id = intval($_POST["ad-id"]) ?? null;
    $ad_title = $_POST["ad-desc-abr"] ?? null;
    $ad_desc = $_POST["ad-desc-full"] ?? null;
    $ad_category = $_POST["ad-categorie"] ?? null;
    $ad_price = $_POST["ad-price"] ?? null;
    $ad_state = $_POST["ad-state"] ?? null;
    $ad_photo = $_POST["ad-photo"] ?? null;
    $noUtilisateur = $_SESSION["user_id"] ?? null;
    $keep_user_photo = $ad_photo === "keep";


    if ($noUtilisateur == null) {
        http_response_code(400);
        exit("You need to be logged in to add an ad.");
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


    if (trim($ad_title) === "") {
        http_response_code(400);
        exit("The ad title cannot be empty.");
    }

    if (!$keep_user_photo) {
        $image_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $ad_hash = strval(bin2hex(random_bytes(16)) . ".$image_extension"); // hash for the ad image
        $target_file = $images_dir . $ad_hash;

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

        if (strlen($file["name"]) > 50) {
            http_response_code(400);
            exit("Ad photo name is too large.");
        }

        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            $old_ad_photo_path = $images_dir . $ads_obj->get_ad($ad_id)["Photo"];
            if (is_file($old_ad_photo_path)) {
                unlink($old_ad_photo_path);
            }
            $ads_obj->modify_ad($ad_id, $ad_title, $ad_desc, $ad_category, $ad_price, $ad_hash, $ad_state, false);
            exit("OK");
        } else {
            exit("Internal server error.");
        }
    }
    
    $ads_obj->modify_ad($ad_id, $ad_title, $ad_desc, $ad_category, $ad_price, null, $ad_state, true);
    echo "OK";
    exit;
}
http_response_code(400);
echo "Only POST requests are allowed.";