<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu principal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://projet03-wserveur.alwaysdata.net/private/css/style.css?v=2" rel="stylesheet">
</head>


<?php
$page = basename(__FILE__, ".php");
require_once "../app/functions/session_manager.php";
require_once "../app/database/user.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "app/database/annonces.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "app/functions/helper_functions.php";
if (!is_admin()) require "./navbars/navigation_signed_in.php";
require "../app/functions/pagination.php";

$page = intval($_GET["page"] ?? null);
if ($page <= 0 || !is_numeric($page)) {
    $page = 1;
}


$num_ads_page = intval($_GET["num_ads"] ?? null);
if (!is_int($num_ads_page) || $num_ads_page <= 0) {
    $num_ads_page = 5;
}
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

// Sorting section
$SORT = h_hsc($_GET["sort"] ?? "date_paru", ENT_QUOTES);
$ORDER = h_hsc($_GET["order"] ?? "desc", ENT_QUOTES);

// Search motor
$date_begin = h_hsc($_GET["date_begin"] ?? null, ENT_QUOTES);
$date_end = h_hsc($_GET["date_end"] ?? null, ENT_QUOTES);
$author_name = h_hsc($_GET["author_name"] ?? null, ENT_QUOTES);
$categorie = h_hsc($_GET["categorie"] ?? null, ENT_QUOTES);
$description = h_hsc($_GET["desc"] ?? null, ENT_QUOTES);

$ads = $ads_obj->set_ads_sort([$SORT, $ORDER], [$date_begin, $date_end], [$author_name], [$categorie], [$description], $num_ads_page, $offset);
$cards = $ads_obj->load_cards_ads_html($ads[0]);
$nb_pages = ceil($ads[1] / $num_ads_page);
?>



<body style="background-color: rgba(0, 0, 0, 0.03);">
    <div class="container py-4">
        <div class="text-center mb-4">
            <h3 style="position: absolute"> Bonjour, <?php echo $user_obj->get_prenom();?> !</h3>
            <h2 class="fw-bold">Annonces</h2>
            <p class="text-muted mb-0">Consultez les annonces disponibles</p>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div style="border-bottom: 1px solid black;">
                <strong>Arranger les annonces</strong>
            </div>

            <!--SECTION TRI D'ANNONCES -->
            <div class="sorting-div">
                <p style="text-align: center;">Trier les annonces</p>
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
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Trier par
                    </button> 
                    <ul class="dropdown-menu">
                        <li><a id="date_paru" class="dropdown-item" href="#" onclick="setSortBy_DDP()">Par date de parution</a></li>
                        <li><a id="lname" class="dropdown-item" href="#" onclick="setSortBy_LNAME()">Par nom de famille</a></li>
                        <li><a id="fname" class="dropdown-item" href="#" onclick="setSortBy_FNAME()">Par prénom</a></li>
                        <li><a id="categorie" class="dropdown-item" href="#" onclick="setSortBy_Categorie()">Par catégorie</a></li>
                    </ul>
                    </ul>
                </div>
                <p style="text-align: center;">Moteur de recherche</p>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Date de parution
                    </button>
                    <ul class="dropdown-menu">
                        <input id="date_begin" type="date"/>
                        <label for="date_begin">Du</label>
                        <input id="date_end" type="date"/>
                        <label for="date_begin">À</label>
                        <div style="display: flex; gap: 5px; margin-top: 5%">
                            <input onclick="setSortBy_TimePeriod()" type="button" class="btn btn-secondary" value="Go">
                            <input onclick="setSortBy_TimePeriod(true)" type="button" class="btn btn-secondary" value="Retirer">
                        </div>
                    </ul> 
                </div>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Auteur
                    </button>
                    <ul class="dropdown-menu">
                        <input onchange="setSortBy_AuthorName()" id="author_name" type="text" placeholder="Nom de l'auteur">
                        <input onclick="setSortBy_AuthorName(true)" type="button" class="btn btn-secondary" value="Retirer" style="margin-top: 5%;">
                    </ul>
                </div>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Catégorie
                    </button>
                    <ul class="dropdown-menu">
                        <input onchange="setSortBy_Categorie_MOTOR()" id="categorie_name" type="text" placeholder="Catégorie">
                        <input onclick="setSortBy_Categorie_MOTOR(true)" type="button" class="btn btn-secondary" value="Retirer" style="margin-top: 5%;">
                    </ul>
                </div>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Description
                    </button>
                    <ul class="dropdown-menu">
                        <textarea id="description" placeholder="Description (max: 50 charactères)" maxlength="50" style="max-height: 100px; min-height: 70px;"></textarea>
                        <div style="display: flex; gap: 5px; margin-top: 5%">
                            <input class="btn btn-secondary" onclick="setSortBy_Description()" type="button" value="Go">
                            <input onclick="setSortBy_Description(true)" type="button" class="btn btn-secondary" value="Retirer">
                        </div>
                    </ul>
                </div>
            </div>

            <!--FIN TRI D'ANNONCES -->

        </div>
        <div class="row g-3 gap-3">
            <?php
                if (empty($cards)) {echo '<div class="text-center text-muted py-5">Aucune annonces disponibles.</div>';}
                else {echo $cards;}
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
        let date_paru_tag = document.getElementById("date_paru")
        let fname_tag = document.getElementById("fname")
        let lname_tag = document.getElementById("lname")
        let categorie_tag = document.getElementById("categorie")

        const SORT = URLParams.get("sort")
        const ORDER = URLParams.get("order")
        const SORTINGS = {
            BY_DDP: "date_paru",
            BY_LNAME: "lname",
            BY_FNAME: "fname",
            BY_CATEGORIE: "categorie"
        }

        switch (SORT) {
            case SORTINGS.BY_DDP:
                ORDER == "asc" ? date_paru_tag.innerText = "Par date de parution ↑" : date_paru_tag.innerText = "Par date de parution ↓"
                break;
            case SORTINGS.BY_FNAME:
                ORDER == "asc" ? fname_tag.innerText = "Par prénom ↑" : fname_tag.innerText = "Par prénom ↓"
                break;
            case SORTINGS.BY_LNAME:
                ORDER == "asc" ? lname_tag.innerText = "Par nom de famille ↑" : lname_tag.innerText = "Par nom de famille ↓"
                break;
            case SORTINGS.BY_CATEGORIE:
                ORDER == "asc" ? categorie_tag.innerText = "Par catégorie ↑" : categorie_tag.innerText = "Par catégorie ↓"
                break;
        }

        if (URLParams.get("page") == null) { URLParams.set("page", "1"); changed = true }
        if (URLParams.get("num_ads") == null) { URLParams.set("num_ads", "5"); changed = true }

        if (changed) location.search = URLParams

        function setNumAds(num) {
            URLParams.set("num_ads", num)
            URLParams.set("page", 1)
            location.search = URLParams
        }

        function setSortBy_DDP() {
            if (URLParams.get("sort") == "date_paru") {
                URLParams.get("order") == "asc" ? URLParams.set("order", "desc") : URLParams.set("order", "asc")
            } else {
                URLParams.set("sort", "date_paru")
                URLParams.set("order", "asc")
            }
            location.search = URLParams
        }

        function setSortBy_LNAME() {
            if (URLParams.get("sort") == "lname") {
                URLParams.get("order") == "asc" ? URLParams.set("order", "desc") : URLParams.set("order", "asc")
            } else {
                URLParams.set("sort", "lname")
                URLParams.set("order", "asc")
            }
            location.search = URLParams
        }

        function setSortBy_FNAME() {
            if (URLParams.get("sort") == "fname") {
                URLParams.get("order") == "asc" ? URLParams.set("order", "desc") : URLParams.set("order", "asc")
            } else {
                URLParams.set("sort", "fname")
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

        function setSortBy_TimePeriod(removeSort) {
            if (removeSort) {
                URLParams.delete("date_begin")
                URLParams.delete("date_end")
                location.search = URLParams
                return;
            }
            let date_begin = document.getElementById("date_begin").value
            let date_end = document.getElementById("date_end").value
            if (date_begin == "" || date_end == "") return;
            URLParams.set("date_begin", date_begin)
            URLParams.set("date_end", date_end)
            location.search = URLParams
        }

        function setSortBy_AuthorName(removeSort) {
            let author_name = document.getElementById("author_name").value
            if (removeSort) {
                URLParams.delete("author_name")
                location.search = URLParams
                return;
            }
            if (author_name.trim() == "") return;
            URLParams.set("author_name", author_name)
            location.search = URLParams
        }

        function setSortBy_Categorie_MOTOR(removeSort) {
            let categorie = document.getElementById("categorie_name").value
            if (removeSort) {
                URLParams.delete("categorie")
                location.search = URLParams
                return;
            }
            if (categorie.trim() == "") return;
            URLParams.set("categorie", categorie)
            location.search = URLParams
        }

        function setSortBy_Description(removeSort) {
            let description = document.getElementById("description").value
            if (removeSort) {
                URLParams.delete("desc")
                location.search = URLParams
                return;
            }
            if (description.trim() == "") return;
            URLParams.set("desc", description)
            location.search = URLParams
        }

    </script>

</body>

</html>