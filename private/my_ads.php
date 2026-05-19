<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes annonces</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://projet03-wserveur.alwaysdata.net/private/css/style.css?v=22" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../favicons/user.ico">
</head>
<?php
$page = basename(__FILE__, ".php");
require_once "../app/functions/session_manager.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "app/functions/helper_functions.php";
require_once "../app/database/user.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "app/database/annonces.php";
require "./navbars/navigation_signed_in.php";
require "../app/functions/pagination.php";
$user_email = $_SESSION["email"];
redirect_if_no_profile($user_email);
logout_if_no_session();

$page = $_GET["page"] ?? 1;
$num_ads_page = 10; // always 10 ads
if ($page == "NaN" || !is_numeric($page)) {$page = 1;}
$offset = ($page - 1) * $num_ads_page;

$user_email = $_SESSION["email"];
$user_obj = new user($user_email);
$user_id = $user_obj->get_id();
$ads_obj = new annonces();

// Sorting

$SORT = h_hsc($_GET["sort"] ?? null, ENT_QUOTES);
$ORDER = h_hsc($_GET["order"] ?? null, ENT_QUOTES);


$users_ads = $ads_obj->set_ads_sort_user($user_id, [$SORT, $ORDER], $offset);
$nb_pages = ceil($users_ads[1] / $num_ads_page);

?>

<body style="background-color: rgba(0, 0, 0, 0.03);">
    <div id="contents">
        <h3 style="text-align: center;">Gestion de mes annonces</h3>
    <div style="padding: 10px; width: fit-content; border-right: 1px solid black;text-align: center; display: flex; flex-direction: column; gap: 5px; margin-right: 5%;">
        <p style="right: 25px;">Trier mes annonces</p>
                <div class="dropdown">
                    <button style="min-width: 200px;" class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Trier par
                    </button> 
                    <ul class="dropdown-menu">
                        <li><a id="date_paru" class="dropdown-item" href="#" onclick="setSortBy_DDP()">Par date de parution</a></li>
                        <li><a id="description" class="dropdown-item" href="#" onclick="setSortBy_DESCRIPTION()">Par description abrégée</a></li>
                        <li><a id="etat" class="dropdown-item" href="#" onclick="setSortBy_STATE()">Par état</a></li>
                        <li><a id="categorie" class="dropdown-item" href="#" onclick="setSortBy_Categorie()">Par catégorie</a></li>
                    </ul>
                    </ul>
                </div>
    </div>

    <div class="container">
        <div class="row g-3 gap-3">
            <?php
            if (empty($users_ads[0])) {
                echo "<p style='text-align: center;'> Vous n'avez aucune annonce. </p>";
            } else {
                echo $ads_obj->load_all_cards_users_ads($users_ads[0]);
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

    <script>
        let changed = false;
        let date_paru_tag = document.getElementById("date_paru")
        let description_tag = document.getElementById("description")
        let etat_tag = document.getElementById("etat")
        let categorie_tag = document.getElementById("categorie")

        const SORT = URLParams.get("sort")
        const ORDER = URLParams.get("order")
        const SORTINGS = {
            BY_DDP: "date_paru",
            BY_DESCRIPTION: "desc",
            BY_ETAT: "etat",
            BY_CATEGORIE: "categorie"
        }

        switch (SORT) {
            case SORTINGS.BY_DDP:
                ORDER == "asc" ? date_paru_tag.innerText = "Par date de parution ↑" : date_paru_tag.innerText = "Par date de parution ↓"
                break;
            case SORTINGS.BY_ETAT:
                ORDER == "asc" ? etat_tag.innerText = "Par état ↑" : etat_tag.innerText = "Par état ↓"
                break;
            case SORTINGS.BY_DESCRIPTION:
                ORDER == "asc" ? description_tag.innerText = "Par description abrégée ↑" : description_tag.innerText = "Par description abrégée ↓"
                break;
            case SORTINGS.BY_CATEGORIE:
                ORDER == "asc" ? categorie_tag.innerText = "Par catégorie ↑" : categorie_tag.innerText = "Par catégorie ↓"
                break;
        }

        if (URLParams.get("page") == null) { URLParams.set("page", "1"); changed = true }

        if (changed) location.search = URLParams

        function setSortBy_DDP() {
            if (URLParams.get("sort") == "date_paru") {
                URLParams.get("order") == "asc" ? URLParams.set("order", "desc") : URLParams.set("order", "asc")
            } else {
                URLParams.set("sort", "date_paru")
                URLParams.set("order", "asc")
            }
            location.search = URLParams
        }

        function setSortBy_DESCRIPTION() {
            if (URLParams.get("sort") == "desc") {
                URLParams.get("order") == "asc" ? URLParams.set("order", "desc") : URLParams.set("order", "asc")
            } else {
                URLParams.set("sort", "desc")
                URLParams.set("order", "asc")
            }
            location.search = URLParams
        }

        function setSortBy_STATE() {
            if (URLParams.get("sort") == "etat") {
                URLParams.get("order") == "asc" ? URLParams.set("order", "desc") : URLParams.set("order", "asc")
            } else {
                URLParams.set("sort", "etat")
                URLParams.set("order", "asc")
            }
            location.search = URLParams
        }

        function setSortBy_Categorie() {
            if (URLParams.get("sort") == "categorie") {
                URLParams.get("order") == "asc" ? URLParams.set("order", "desc") :URLParams.set("order", "asc")
            } else {
                URLParams.set("sort", "categorie")
                URLParams.set("order", "asc")
            }
            location.search = URLParams
        }
    </script>
</body>

</html>