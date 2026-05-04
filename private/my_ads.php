<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes annonces</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://projet03-wserveur.alwaysdata.net/private/css/style.css?v=2" rel="stylesheet">
</head>
<?php
$page = basename(__FILE__, ".php");
require_once "../app/functions/session_manager.php";
require_once "../app/database/user.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "app/database/annonces.php";
require "./navbars/navigation_signed_in.php";
require "../app/functions/pagination.php";

// Always 10 ads per page
$page = $_GET["page"] ?? 1;
$offset = ($page - 1) * 10;

$user_email = $_SESSION["email"];
$user_obj = new user($user_email);
$user_id = $user_obj->get_id();
$ads_obj = new annonces();

$users_ads = $ads_obj->get_all_users_add($user_id, $offset);
$nb_pages = ceil($ads_obj->get_number_all_ads_users($user_id) / 10);

?>
<body>
    <h3 style="text-align: center;">Gestion des annonces</h3>
    <div class="container">
        <div class="row g-3 gap-3">
            <?php echo $ads_obj->load_all_cards_users_ads($users_ads);?>
        </div>
    </div>
    <div class="d-flex justify-content-center mt-4">
            <div class="btn-group btn-secondary" role="group">
                <?php make_pagination_annonces($nb_pages); ?>
            </div>
        </div>
    <script>
        let URLParams = new URLSearchParams(document.location.search)
    </script>
</body>
</html>
