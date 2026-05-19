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

    // Tous les utilisateurs sauf MotDePasse et AutresInfos, ordre alphabétique
    $stmt = $con->query("
        SELECT 
            u.NoUtilisateur, u.Courriel, u.Creation, u.NbConnexions, u.Statut,
            u.NoEmpl, u.Nom, u.Prenom, u.NoTelMaison, u.NoTelTravail,
            u.NoTelCellulaire, u.Modification,
            COUNT(CASE WHEN a.Etat = 1 THEN 1 END) AS nbrActif,
            COUNT(CASE WHEN a.Etat = 2 THEN 1 END) AS nbrInactif,
            COUNT(CASE WHEN a.Etat = 3 THEN 1 END) AS nbrRetire
        FROM utilisateurs u
        LEFT JOIN annonces a ON u.NoUtilisateur = a.NoUtilisateur
        GROUP BY u.NoUtilisateur
        ORDER BY u.Nom ASC, u.Prenom ASC
    ");
    $utilisateurs = $stmt->fetch_all(MYSQLI_ASSOC);

    // 5 dernières connexions/déconnexions par utilisateur
    $stmt = $con->query("
        SELECT NoUtilisateur, Connexion, Deconnexion
        FROM connexions
        ORDER BY Connexion DESC
    ");
    $toutesConnexions = $stmt->fetch_all(MYSQLI_ASSOC);

    // Regrouper les 5 dernières par utilisateur
    $connexionsParUser = [];
    foreach($toutesConnexions as $c){
        $no = $c['NoUtilisateur'];
        if(!isset($connexionsParUser[$no])){
            $connexionsParUser[$no] = [];
        }
        if(count($connexionsParUser[$no]) < 5){
            $connexionsParUser[$no][] = $c;
        }
    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Utilisateurs</title>
    <link rel="icon" type="image/x-icon" href="https://projet03-wserveur.alwaysdata.net/favicons/admin.ico">
</head>
<body>
<div class="contenu">
    <h1>Affichage de tous les utilisateurs</h1>
    <!--
    <?php
        echo "<pre>";
        print_r($connexionsParUser);
        echo "</pre>";
    ?>-->
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Courriel</th>
                <th>Création</th>
                <th>Nb Connexions</th>
                <th>Statut</th>
                <th>No Empl</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Tél. Maison</th>
                <th>Tél. Travail</th>
                <th>Tél. Cellulaire</th>
                <th>Modification</th>
                <th>5 dernières connexions</th>
                <th>5 dernières déconnexions</th>                
                <th>Actives</th>
                <th>Inactives</th>
                <th>Retirées</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($utilisateurs as $u): ?>
                <tr>
                    <td><?= $u['NoUtilisateur'] ?></td>
                    <td><?= $u['Courriel'] ?></td>
                    <td><?= $u['Creation'] ?></td>
                    <td><?= $u['NbConnexions'] ?></td>
                    <td><?= $u['Statut'] ?></td>
                    <td><?= $u['NoEmpl'] ?? 'N/A' ?></td>
                    <td><?= $u['Nom'] ?></td>
                    <td><?= $u['Prenom'] ?></td>
                    <td><?= $u['NoTelMaison'] ?? 'N/A' ?></td>
                    <td><?= $u['NoTelTravail'] ?? 'N/A' ?></td>
                    <td><?= $u['NoTelCellulaire'] ?? 'N/A' ?></td>
                    <td><?= $u['Modification'] ?? 'N/A' ?></td>
                    <td>
                        <?php
                            $no = $u['NoUtilisateur'];
                            if(!empty($connexionsParUser[$no])){
                                foreach($connexionsParUser[$no] as $c){
                                    echo $c['Connexion'] . "<br>";
                                }
                            } else {
                                echo "Aucune";
                            }
                        ?>
                    </td>
                    <td>
                        <?php
                            if(!empty($connexionsParUser[$no])){
                                foreach($connexionsParUser[$no] as $c){
                                    echo ($c['Deconnexion'] ?? 'N/A') . "<br>";
                                }
                            } else {
                                echo "Aucune";
                            }
                        ?>
                    </td>
                    <td><?= $u['nbrActif'] ?></td>
                    <td><?= $u['nbrInactif'] ?></td>
                    <td><?= $u['nbrRetire'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
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
        overflow-x: auto;
    }

    table {
        border-collapse: collapse;
        width: 100%;
        font-size: 13px;
    }

    th, td {
        text-align: left;
        padding: 8px 10px;
        border-bottom: 1px solid #eee;
        white-space: nowrap;
    }

    th {
        background-color: #f0f0f0;
        color: #555;
    }

    tr:hover {
        background-color: #fafafa;
    }
</style>