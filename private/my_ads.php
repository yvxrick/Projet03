<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes annonces</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://projet03-wserveur.alwaysdata.net/private/css/style.css?v=22" rel="stylesheet">
</head>
<?php
$page = basename(__FILE__, ".php");
require_once "../app/functions/session_manager.php";
require_once "../app/database/user.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "app/database/annonces.php";
require "./navbars/navigation_signed_in.php";
require "../app/functions/pagination.php";
$user_email = $_SESSION["email"];
redirect_if_no_profile($user_email);
logout_if_no_session();
// Always 10 ads per page
$page = $_GET["page"] ?? 1;
if ($page == "NaN") {$page = 1;}
$offset = ($page - 1) * 10;

$user_email = $_SESSION["email"];
$user_obj = new user($user_email);
$user_id = $user_obj->get_id();
$ads_obj = new annonces();

$users_ads = $ads_obj->get_all_users_add($user_id, $offset);
$nb_pages = ceil($ads_obj->get_number_all_ads_users($user_id) / 10);

?>

<body>
    <div id="contents">
        <h3 style="text-align: center;">Gestion de mes annonces</h3>
        <div class="container">
            <div class="row g-3 gap-3">
                <?php
                if (empty($users_ads)) {
                    echo "<p style='text-align: center;'> Aucune annonces. </p>";
                } else {
                    echo $ads_obj->load_all_cards_users_ads($users_ads);
                }
                ?>
            </div>
        </div>
        <div class="d-flex justify-content-center mt-4">
            <div class="btn-group btn-secondary" role="group">
                <?php make_pagination_annonces($nb_pages); ?>
            </div>
        </div>
    </div>
    <div hidden id="overlay" class="overlay">
        <label style="text-decoration: underline">Annonce #<span id="ad-number"></span></label>
        <h3>Que voulez-vous faire ?</h3>
        <div>
            <input type="button" id="modifier_annonce" class="btn btn-light" value="Modifier l'annonce">
            <input type="button" id="supprimer_annonce" class="btn btn-dark" value="Supprimer l'annonce">
        </div>
        <p>Vous serez redirigé vers une autre page</p>
    </div>
    <script>
        let body = document.getElementById("contents")
        let ad_number_tag = document.getElementById("ad-number")
        let URLParams = new URLSearchParams(document.location.search)
        let btn_modif_ad = document.getElementById("modifier_annonce")
        let btn_suppr_ad = document.getElementById("supprimer_annonce")
        let overlay = document.getElementById("overlay")
        const urlMatch = /id=(.+)`/

        document.addEventListener('click', (e) => {
            if (e.target.parentElement.className == "ad-card") {
                let ad_info = e.target.parentElement.getAttribute("href")
                let ad_id = ad_info.match(urlMatch)[1]
                ad_number_tag.innerText = ad_id
                btn_modif_ad.setAttribute("onclick", `window.location.href = 'https://projet03-wserveur.alwaysdata.net/private/modify_ad.php?id=${ad_id}'`)
                btn_suppr_ad.setAttribute("onclick", `window.location.href = 'https://projet03-wserveur.alwaysdata.net/private/remove_ad.php?id=${ad_id}'`)
                overlay.hidden = false
                body.style.opacity = 0.5
            } else if (e.target.parentElement.parentElement.className != "overlay" && e.target.parentElement.className != "overlay") {
                body.style.opacity = 1
                overlay.hidden = true
            }
        })
    </script>
</body>

</html>