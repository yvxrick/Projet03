<?php
    include_once 'menuPrincipal.php';
    include_once __DIR__ . '/../app/database/database.php';

    $con = Database::Connect();

    //$stmt = $con->query("DELETE FROM annonces WHERE noUtilisateur = 3;");

    // PAGINATION
    $parPage = isset($_GET['parPage']) ? (int)$_GET['parPage'] : 5;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $parPage;

    // TRI
    $triColonne = $_GET['tri'] ?? 'Parution';
    $triOrdre = $_GET['ordre'] ?? 'DESC';

    $colonnesValides = ['Parution', 'Nom', 'Categorie'];
    $ordresValides = ['ASC', 'DESC'];
    if(!in_array($triColonne, $colonnesValides)) $triColonne = 'Parution';
    if(!in_array($triOrdre, $ordresValides)) $triOrdre = 'DESC';

    // RECHERCHE
    $conditions = [];
    $params = [];
    $types = "";

    if(!empty($_GET['articleRecherche'])) {
        $conditions[] = "DescriptionAbregee LIKE ?";
        $params[] = "%" . $_GET['articleRecherche'] . "%";
        $types .= "s";
    }
    if(!empty($_GET['categorie'])) {
        $conditions[] = "annonces.Categorie = ?";
        $params[] = $_GET['categorie'];
        $types .= "i";
    }
    if(!empty($_GET['auteur'])) {
        $conditions[] = "(Nom LIKE ? OR Prenom LIKE ?)";
        $params[] = "%" . $_GET['auteur'] . "%";
        $params[] = "%" . $_GET['auteur'] . "%";
        $types .= "ss";
    }
    if(!empty($_GET['semaine'])) {
        $conditions[] = "Parution BETWEEN ? AND DATE_ADD(?, INTERVAL 6 DAY)";
        $params[] = $_GET['semaine'];
        $params[] = $_GET['semaine'];
        $types .= "ss";
    }

    $where = !empty($conditions) ? " WHERE " . implode(" AND ", $conditions) : "";

    $sqlBase = "FROM annonces JOIN utilisateurs ON annonces.NoUtilisateur = utilisateurs.NoUtilisateur" . $where;

    // Compter le total
    $stmtCount = $con->prepare("SELECT COUNT(*) as total " . $sqlBase);
    if(!empty($params)) $stmtCount->bind_param($types, ...$params);
    $stmtCount->execute();
    $total = $stmtCount->get_result()->fetch_assoc()['total'];
    $totalPages = ceil($total / $parPage);

    // Requete principale
    $sql = "SELECT annonces.*, utilisateurs.Nom, utilisateurs.Prenom " . $sqlBase . " ORDER BY $triColonne $triOrdre LIMIT ? OFFSET ?";
    $paramsPage = array_merge($params, [$parPage, $offset]);
    $typesPage = $types . "ii";
    $stmt = $con->prepare($sql);
    $stmt->bind_param($typesPage, ...$paramsPage);
    $stmt->execute();
    $annonces = $stmt->get_result();

    // Garder les GET params pour les liens
    $getParams = $_GET;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Annonces</title>
    <link rel="stylesheet" href="profile.css">
</head>

<body>
<div class="contenu">
    <h2>Les annonces</h2>
    <form method="GET" action="affichageAnnonces.php">
        <input type="text" name="articleRecherche" placeholder="Rechercher..." value="<?= $_GET['articleRecherche'] ?? '' ?>">
        <input type="text" name="auteur" placeholder="Auteur" value="<?= $_GET['auteur'] ?? '' ?>">

        <select name="categorie">
            <option value="">Catégorie</option>
            <?php $cats = $con->query("SELECT * FROM categories"); while($cat = $cats->fetch_assoc()): ?>
                <option value="<?= $cat['NoCategorie'] ?>" <?= ($_GET['categorie'] ?? '') == $cat['NoCategorie'] ? 'selected' : '' ?>>
                    <?= $cat['Description'] ?>
                </option>
            <?php endwhile; ?>
        </select>

        <select name="semaine">
            <option value="">Semaine</option>
            <option value="2026-04-20">20 au 26 avril 2026</option>
            <option value="2026-04-27">27 avril au 3 mai 2026</option>
        </select>

        <!-- TRI -->
        <select name="tri">
            <option value="Parution" <?= ($_GET['tri'] ?? '') == 'Parution' ? 'selected' : '' ?>>Date</option>
            <option value="Nom" <?= ($_GET['tri'] ?? '') == 'Nom' ? 'selected' : '' ?>>Auteur</option>
            <option value="Categorie" <?= ($_GET['tri'] ?? '') == 'Categorie' ? 'selected' : '' ?>>Catégorie</option>
        </select>
        <select name="ordre">
            <option value="DESC" <?= ($_GET['ordre'] ?? '') == 'DESC' ? 'selected' : '' ?>>Décroissant</option>
            <option value="ASC" <?= ($_GET['ordre'] ?? '') == 'ASC' ? 'selected' : '' ?>>Croissant</option>
        </select>

        <!-- PAR PAGE -->
        <select name="parPage">
            <?php foreach([5,10,15,20] as $n): ?>
                <option value="<?= $n ?>" <?= $parPage == $n ? 'selected' : '' ?>><?= $n ?> par page</option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Chercher</button>
    </form>

    <!-- ANNONCES TOTAL -->
    <p>Total: <?= $total ?> annonces</p>


      
    <!-- PAGINATION -->
    <?php
    echo "<div class='pagination'>";
        $getParams['page'] = 1;
        echo "<a href='?" . http_build_query($getParams) . "'>«</a> ";

        $getParams['page'] = max(1, $page - 1);
        echo "<a href='?" . http_build_query($getParams) . "'>‹</a> ";

        for($i = 1; $i <= $totalPages; $i++) {
            $getParams['page'] = $i;
            $actif = $i == $page ? "style='font-weight:bold'" : "";
            echo "<a href='?" . http_build_query($getParams) . "' $actif>$i</a> ";
        }

        $getParams['page'] = min($totalPages, $page + 1);
        echo "<a href='?" . http_build_query($getParams) . "'>›</a> ";

        $getParams['page'] = $totalPages;
        echo "<a href='?" . http_build_query($getParams) . "'>»</a>";
        
    echo "</div>";
    ?>


    <!-- ANNONCES -->
    <div class="listeAnnonce">
        <?php while($annonce = $annonces->fetch_assoc()): ?>
        <div class="annonce">
            <p><?= $annonce['NoAnnonce'] ?>#</p>
            <p>Apparu le <?= $annonce['Parution'] ?></p>
            <p><?= $annonce['Nom'] ?? 'N/A' ?> <?= $annonce['Prenom'] ?></p>
            <img src="../<?= $annonce['Photo'] ?>" alt="">
            <p><?= $annonce['DescriptionAbregee'] ?></p>
            <p><?= $annonce['Prix'] ?> $</p>
        </div>
        <?php endwhile; ?>
    </div>

      <!-- PAGINATION -->
    <?php
    echo "<div class='pagination'>";
        $getParams['page'] = 1;
        echo "<a href='?" . http_build_query($getParams) . "'>«</a> ";

        $getParams['page'] = max(1, $page - 1);
        echo "<a href='?" . http_build_query($getParams) . "'>‹</a> ";

        for($i = 1; $i <= $totalPages; $i++) {
            $getParams['page'] = $i;
            $actif = $i == $page ? "style='font-weight:bold'" : "";
            echo "<a href='?" . http_build_query($getParams) . "' $actif>$i</a> ";
        }

        $getParams['page'] = min($totalPages, $page + 1);
        echo "<a href='?" . http_build_query($getParams) . "'>›</a> ";

        $getParams['page'] = $totalPages;
        echo "<a href='?" . http_build_query($getParams) . "'>»</a>";
        
    echo "</div>";
    ?>
    
</div>
</body>
</html>