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

    function getTables(){
        global $con;
        $stmt = $con->query("SHOW TABLES");
        return $stmt->fetch_all(MYSQLI_ASSOC);
    }

    function getNombreUtilisateursAujourdhui(){
        global $con;
        $stmt = $con->query("SELECT COUNT(*) as total FROM utilisateurs WHERE DATE(Creation) = CURDATE()");
        $row = $stmt->fetch_assoc();
        return $row['total'];
    }

    function getNombreUtilisateursSemaine(){
        global $con;
        $stmt = $con->query("SELECT COUNT(*) as total FROM utilisateurs WHERE Creation >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
        $row = $stmt->fetch_assoc();
        return $row['total'];
    }

    function getNombreAnnoncesActives(){
        global $con;
        $stmt = $con->query("SELECT COUNT(*) as total FROM annonces WHERE Etat = 1");
        $row = $stmt->fetch_assoc();
        return $row['total'];
    }

    function getNombreAnnoncesInactives(){
        global $con;
        $stmt = $con->query("SELECT COUNT(*) as total FROM annonces WHERE Etat = 2");
        $row = $stmt->fetch_assoc();
        return $row['total'];
    }

    function getNombreAnnoncesRetirees(){
        global $con;
        $stmt = $con->query("SELECT COUNT(*) as total FROM annonces WHERE Etat = 3");
        $row = $stmt->fetch_assoc();
        return $row['total'];
    }

    function getNombreUtilisateursNonConfirmes(){
        global $con;
        // Statut = 0 signifie "En attente"
        $stmt = $con->query("SELECT COUNT(*) as total FROM utilisateurs WHERE Statut = 0 AND Creation < DATE_SUB(NOW(), INTERVAL 1 MONTH)");
        $row = $stmt->fetch_assoc();
        return $row['total'];
    }

    function getNombreTotalUtilisateurs(){
        global $con;
        $stmt = $con->query("SELECT COUNT(*) as total FROM utilisateurs");
        $row = $stmt->fetch_assoc();
        return $row['total'];
    }

    $nomTables             = getTables();
    $usersAujourdhui       = getNombreUtilisateursAujourdhui();
    $usersSemaine          = getNombreUtilisateursSemaine();
    $annoncesActives       = getNombreAnnoncesActives();
    $annoncesInactives     = getNombreAnnoncesInactives();
    $annoncesRetirees      = getNombreAnnoncesRetirees();
    $usersNonConfirmes     = getNombreUtilisateursNonConfirmes();
    $totalUtilisateurs     = getNombreTotalUtilisateurs();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aperçu</title>
    <link rel="icon" type="image/x-icon" href="https://projet03-wserveur.alwaysdata.net/favicons/admin.ico">
</head>
<body>
    <div class="contenu">
        <h1>Aperçu</h1>

        <!-- Statistiques utilisateurs -->
        <h2>Utilisateurs</h2>
        <div class="wrap">
            <div class="stat-card">
                <span class="stat-label">Total</span>
                <span class="stat-value"><?= $totalUtilisateurs ?></span>
            </div>
            <div class="stat-card">
                <span class="stat-label">Créés aujourd'hui</span>
                <span class="stat-value"><?= $usersAujourdhui ?></span>
            </div>
            <div class="stat-card">
                <span class="stat-label">Créés cette semaine</span>
                <span class="stat-value"><?= $usersSemaine ?></span>
            </div>
            <div class="stat-card warning">
                <span class="stat-label">Non confirmés (&gt;1 mois)</span>
                <span class="stat-value"><?= $usersNonConfirmes ?></span>
            </div>
        </div>

        <!-- Statistiques annonces -->
        <h2>Annonces</h2>
        <div class="wrap">
            <div class="stat-card active">
                <span class="stat-label">Actives</span>
                <span class="stat-value"><?= $annoncesActives ?></span>
            </div>
            <div class="stat-card inactive">
                <span class="stat-label">Inactives</span>
                <span class="stat-value"><?= $annoncesInactives ?></span>
            </div>
            <div class="stat-card retired">
                <span class="stat-label">Retirées</span>
                <span class="stat-value"><?= $annoncesRetirees ?></span>
            </div>
        </div>

        <!-- Tables de la base de données -->
        <h2>Tables de la base de données</h2>
        <div class="container">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nom de la table</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($nomTables as $i => $t): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= ucfirst(array_values($t)[0]) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</body>
</html>

<style>
    body {
        font-family: Arial, Helvetica, sans-serif;
        display: flex;
    }

    .contenu {
        padding: 20px;
        width: 100%;
    }

    h1 {
        margin-bottom: 10px;
    }

    h2 {
        margin-top: 30px;
        margin-bottom: 10px;
        border-bottom: 1px solid #ccc;
        padding-bottom: 5px;
    }

    .wrap {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        margin-bottom: 10px;
    }

    .stat-card {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 7px;
        padding: 15px 25px;
        min-width: 160px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }

    .stat-label {
        font-size: 13px;
        color: #666;
        text-align: center;
    }

    .stat-value {
        font-size: 28px;
        font-weight: bold;
        color: #222;
    }

    .container {
        border: 1px solid #ddd;
        border-radius: 7px;
        padding: 10px;
        background: #fff;
        display: inline-block;
    }

    table {
        border-collapse: collapse;
        min-width: 250px;
    }

    th, td {
        text-align: left;
        padding: 8px 16px;
        border-bottom: 1px solid #eee;
    }

    th {
        background-color: #f0f0f0;
        font-size: 13px;
        color: #555;
    }

    tr:last-child td {
        border-bottom: none;
    }
</style>