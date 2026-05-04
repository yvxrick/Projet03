<?php
$page = basename(__FILE__, ".php");
require_once "../app/functions/session_manager.php";
require_once "../app/database/user.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "app/database/annonces.php";
require "./navbars/navigation_signed_in.php";
require "../app/functions/pagination.php";
$user_email = $_SESSION["email"];
$user_id = $_SESSION["user_id"];
$ad_id = $_GET["id"] ?? null;
$user_obj = new user($user_email);
$ads_obj = new annonces();
if (!$ads_obj->is_users_ad($user_id, $ad_id)) {
    header("Location: forbidden.html");
    exit();
}
$ad = $ads_obj->get_ad($ad_id); 
echo print_r($ad);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon annonce</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://projet03-wserveur.alwaysdata.net/private/css/style.css?v=2" rel="stylesheet">
</head>

<body>
    <h3 style="text-align: center;">Modifier mon annonce</h3>
    <div id="container">
        <p>Titre de l'annonce (Description abrégée): </p>
        <input class="form-control" type="text" value="<?php echo $ad['DescriptionAbregee']?>"/>
    </div>
</body>
</html>