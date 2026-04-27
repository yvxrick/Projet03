<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu principal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://projet03-wserveur.alwaysdata.net/private/css/style.css?v=12" rel="stylesheet">
</head>


<?php
$page = basename(__FILE__, ".php");
require_once "../app/functions/session_manager.php";
require_once "../app/database/user.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "app/database/annonces.php";
require "./navbars/navigation_signed_in.php";
require "../app/functions/pagination.php";

$page = $_GET["page"] ?? null;
if ($page <= 0 || !is_numeric($page)) {
    $page = 1;
}


$num_ads_page = $_GET["num_ads"] ?? 5;
$offset = intval($page - 1) * intval($num_ads_page);

$user_email = $_SESSION["email"];
$user_id = $_SESSION["user_id"];

$user_obj = new user($user_email);
$ads_obj = new annonces();

logout_if_no_session();
redirect_if_no_profile($user_email);

$user_obj = new user($user_email);
$user_fname = $user_obj->get_prenom();
$user_lname = $user_obj->get_nom();

$ads = $ads_obj->get_all_cards_ads($num_ads_page, $offset);
$ads = $ads_obj->sortByDDP_DESC($num_ads_page, $offset);
$nb_pages = floor($ads_obj->get_number_of_ads_active() / $num_ads_page);
?>



<body style="background-color: rgba(0, 0, 0, 0.03);">
    <div class="container py-4">
        <div class="text-center mb-4">
            <h2 class="fw-bold">Annonces</h2>
            <p class="text-muted mb-0">Consultez les annonces disponibles</p>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div style="border-bottom: 1px solid black;">
                <strong>Arranger les annonces</strong>
            </div>
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    Nombre d'annonces
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="setNumAds(5)">5 annonces</a></li>
                    <li><a class="dropdown-item" href="#" onclick="setNumAds(10)">10 annonces</a></li>
                    <li><a class="dropdown-item" href="#" onclick="setNumAds(15)">15 annonces</a></li>
                    <li><a class="dropdown-item" href="#" onclick="setNumAds(20)">20 annonces</a></li>
                </ul>
            </div>
        </div>
        <div class="row g-3 gap-3">
            <?php
            if (empty($ads_obj->load_cards_ads_html($ads))) {
                echo '<div class="text-center text-muted py-5">Aucune annonces disponibles.</div>';
            } else {
                echo $ads_obj->load_cards_ads_html($ads);
            }
            ?>
        </div>
        <div class="d-flex justify-content-center mt-4">
            <div class="btn-group btn-secondary" role="group">
                <?php make_pagination_annonces($nb_pages); ?>
            </div>
        </div>
    </div>

    <script>
        let changed = false;
        let URLParams = new URLSearchParams(document.location.search);

        if (URLParams.get("page") == null) { URLParams.set("page", "1"); changed = true }
        if (URLParams.get("num_ads") == null) { URLParams.set("num_ads", "5"); changed = true }

        if (changed) location.search = URLParams

        function setNumAds(num) {
            URLParams.set("num_ads", num)
            URLParams.set("page", 1)
            location.search = URLParams
        }
    </script>

</body>

</html>