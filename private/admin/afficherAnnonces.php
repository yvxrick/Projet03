<?php
    include_once $_SERVER['DOCUMENT_ROOT'] . "app/database/annonces.php";
    include_once __DIR__ . "/navigation.php";
    include_once $_SERVER['DOCUMENT_ROOT'] . "app/functions/session_manager.php";
    if (!is_admin()) {
        http_response_code(403);
        header("Location: https://projet03-wserveur.alwaysdata.net/private/forbidden.html");
        exit;
    }
    $con = DATABASE::Connect();
    $ads_images_path = "https://projet03-wserveur.alwaysdata.net/private/ads-images/";
    $tri = $_GET["tri"] ?? "Parution";    
    $ordre = $_GET["ordre"] ?? "ASC";
    $annonceParPage = $_GET["annonceParPage"] ?? 5;
    $page = $_GET["page"] ?? 1;
    $offset = ($page - 1) * $annonceParPage;

    $strChercher = $_GET["strChercher"] ?? "";
    $dateDebut = $_GET["dateDebut"] ?? "";
    $dateFin = $_GET["dateFin"] ?? "";
    $strChercherAuteur = $_GET["strChercherAuteur"] ?? "";
    $categorie = $_GET["categorie"] ?? "";

    $sql = "
        SELECT annonces.*, utilisateurs.Nom, utilisateurs.Prenom, categories.Description AS NomCategorie
        FROM annonces
        JOIN utilisateurs ON utilisateurs.NoUtilisateur = annonces.NoUtilisateur
        JOIN categories ON categories.NoCategorie = annonces.Categorie
        WHERE 1=1
    ";
    if($strChercher) $sql .= " AND (DescriptionAbregee LIKE '%$strChercher%' OR DescriptionComplete LIKE '%$strChercher%')";
    if($dateDebut && $dateFin) $sql .= " AND Parution BETWEEN '$dateDebut' AND '$dateFin'";
    if($strChercherAuteur) $sql .= " AND (
        Nom LIKE '%$strChercherAuteur%' 
        OR Prenom LIKE '%$strChercherAuteur%' 
        OR CONCAT(Prenom, ' ', Nom) LIKE '%$strChercherAuteur%'
        OR CONCAT(Nom, ' ', Prenom) LIKE '%$strChercherAuteur%'
    )";
    if($categorie) $sql .= " AND annonces.Categorie = '$categorie'";

    $sqlTotal = $sql;
    $sql .= " ORDER BY $tri $ordre LIMIT $annonceParPage OFFSET $offset";

    $stmt = $con->query($sql);
    $annonces = $stmt->fetch_all(MYSQLI_ASSOC);

    $stmtTotal = $con->query("SELECT COUNT(*) AS total FROM ($sqlTotal) AS t");
    $annoncesTotale = $stmtTotal->fetch_all(MYSQLI_ASSOC)[0]["total"];
    $totalPages = ceil($annoncesTotale / $annonceParPage);

    $stmt = $con->query("SELECT NoCategorie, Description FROM categories");
    $categories = $stmt->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Annonces</title>
</head>
<body>
    <div class="contenu">
        <h1>Affichage de toutes les annonces</h1>

        <form class="filtreOrdre" method="GET">
            <input type="hidden" name="annonceParPage" value="<?= $annonceParPage ?>">
            <input type="hidden" name="page" value="1">
            <select name="tri">
                <option value="Parution"  <?= $tri === "Parution"  ? "selected" : "" ?>>Date de parution</option>
                <option value="Nom"       <?= $tri === "Nom"       ? "selected" : "" ?>>Nom et prénom</option>
                <option value="Categorie" <?= $tri === "Categorie" ? "selected" : "" ?>>Catégorie</option>
            </select>
            <select name="ordre">
                <option value="ASC"  <?= $ordre === "ASC"  ? "selected" : "" ?>>Croissant</option>
                <option value="DESC" <?= $ordre === "DESC" ? "selected" : "" ?>>Décroissant</option>
            </select>
            <button type="submit">Trier</button>
        </form>

        <form class="annonceParPage" method="GET">
            <input type="hidden" name="tri" value="<?= $tri ?>">
            <input type="hidden" name="ordre" value="<?= $ordre ?>">
            <input type="hidden" name="page" value="1">
            <label>Annonces par page: </label>
            <select name="annonceParPage" onchange="this.form.submit()">
                <option value="5"  <?= $annonceParPage == 5  ? "selected" : "" ?>>5</option>
                <option value="10" <?= $annonceParPage == 10 ? "selected" : "" ?>>10</option>
                <option value="15" <?= $annonceParPage == 15 ? "selected" : "" ?>>15</option>
                <option value="20" <?= $annonceParPage == 20 ? "selected" : "" ?>>20</option>
            </select>
        </form>

        <form class="filtre" method="GET">
            <input type="hidden" name="tri" value="<?= $tri ?>">
            <input type="hidden" name="ordre" value="<?= $ordre ?>">
            <input type="hidden" name="annonceParPage" value="<?= $annonceParPage ?>">
            <input type="hidden" name="page" value="1">
            <input name="strChercher" type="text" placeholder="Chercher..." value="<?= $strChercher ?>">
            <input name="dateDebut" type="date" value="<?= $dateDebut ?>"> à 
            <input name="dateFin" type="date" value="<?= $dateFin ?>">
            <input name="strChercherAuteur" type="text" placeholder="Nom de l'auteur" value="<?= $strChercherAuteur ?>">
            <select name="categorie">
                <option value="">Toutes les catégories</option>
                <?php foreach($categories as $c): ?>
                    <option value="<?= $c['NoCategorie'] ?>" <?= $categorie == $c['NoCategorie'] ? "selected" : "" ?>>
                        <?= $c['Description'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Chercher</button>
        </form>

        <div class="annoncesTotales">
            Nombre total d'annonces : <strong><?= $annoncesTotale ?></strong>
        </div>

        <div class="pagination">
            <a href="?page=1&tri=<?= $tri ?>&ordre=<?= $ordre ?>&annonceParPage=<?= $annonceParPage ?>" class="fleche">«</a>
            <a href="?page=<?= max(1, $page-1) ?>&tri=<?= $tri ?>&ordre=<?= $ordre ?>&annonceParPage=<?= $annonceParPage ?>" class="fleche">‹</a>
            <?php for($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>&tri=<?= $tri ?>&ordre=<?= $ordre ?>&annonceParPage=<?= $annonceParPage ?>"
                   <?= $page == $i ? "class='active'" : "" ?>>
                    <?= $i ?>
                </a>
            <?php endfor; ?>
            <a href="?page=<?= min($totalPages, $page+1) ?>&tri=<?= $tri ?>&ordre=<?= $ordre ?>&annonceParPage=<?= $annonceParPage ?>" class="fleche">›</a>
            <a href="?page=<?= $totalPages ?>&tri=<?= $tri ?>&ordre=<?= $ordre ?>&annonceParPage=<?= $annonceParPage ?>" class="fleche">»</a>
        </div>
        
        <div class="wrap-annonces">
            <?php
                $noSequentiel = ($page - 1) * $annonceParPage;
                foreach($annonces as $a):
                    $noSequentiel++;
            ?>
            <div class="annonce">
                <div class="noSequentiel">#<?= $noSequentiel ?></div>
                <div class="noAnnonce">Annonce #<?= $a["NoAnnonce"] ?></div>
                <div class="dateParution"><?= $a["Parution"] ?></div>
                <div class="nomComplet"><?= $a["Prenom"] . " " . $a["Nom"] ?></div>
                <div class="categorie"><?= $a["NomCategorie"] ?></div>
                <div class="descAbregee"><strong><?= $a["DescriptionAbregee"] ?></strong></div>
                <div class="descComplete"><?= $a["DescriptionComplete"] ?></div>
                <div class="prix"><?= $a["Prix"] == 0 ? 'À donner' : $a["Prix"] . ' $' ?></div>
                <div class="photo">
                    <?php if($a["Photo"]): ?>
                        <img src="<?=$ads_images_path?><?= $a["Photo"] ?>" alt="photo annonce">
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>

<style>
    body {
        font-family: Arial, Helvetica, sans-serif;
        display: flex;
        height: 100vh;
        margin: 0;
        overflow: hidden;
    }

    nav {
        height: 100vh;
        overflow-y: auto;
    }

    .contenu {
        padding: 20px;
        height: 100vh;
        overflow-y: auto;
        flex: 1;
    }

    .wrap-annonces {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 10px;
    }

    .annonce {
        border: 1px solid black;
        border-radius: 7px;
        padding: 20px;
        min-height: 150px;
    }

    .photo img {
        width: 100%;
        height: 150px;
        object-fit: cover;
        border-radius: 5px;
    }

    .pagination {
        display: flex;
        align-items: center;
        gap: 5px;
        margin: 20px 0;
    }

    .pagination a {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 35px;
        height: 35px;
        border: 1px solid #ddd;
        border-radius: 5px;
        text-decoration: none;
        color: #333;
        font-size: 14px;
        transition: all 0.2s;
    }

    .pagination a:hover {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }

    .pagination a.active {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
        font-weight: bold;
    }

    .pagination a.fleche {
        font-size: 18px;
        color: #007bff;
    }
</style>