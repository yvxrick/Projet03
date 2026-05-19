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

    // Supprimer physiquement les annonces avec Etat = 3
    if($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'supprimerAnnonces'){
        $stmt = $con->query("DELETE FROM annonces WHERE Etat = 3");
        $msgAnnonces = "Les annonces retirées ont été supprimées.";
    }

    // Supprimer physiquement les utilisateurs en attente depuis plus d'un mois
    if($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'supprimerUtilisateurs'){
        $stmt = $con->query("DELETE FROM utilisateurs WHERE Statut = 0 AND Creation < DATE_SUB(NOW(), INTERVAL 1 MONTH)");
        $nbSupprimes = $con->affected_rows;
        $msgUtilisateurs = "$nbSupprimes utilisateur(s) supprimé(s).";
    }

    // Compter les annonces retirées logiquement
    $stmt = $con->query("SELECT COUNT(*) as total FROM annonces WHERE Etat = 3");
    $nbAnnoncesRetirees = $stmt->fetch_assoc()['total'];

    // Compter les utilisateurs non confirmés depuis plus d'un mois
    $stmt = $con->query("SELECT COUNT(*) as total FROM utilisateurs WHERE Statut = 0 AND Creation < DATE_SUB(NOW(), INTERVAL 1 MONTH)");
    $nbUsersNonConfirmes = $stmt->fetch_assoc()['total'];

    // Récupérer les annonces retirées pour le tableau
    $stmt = $con->query("SELECT NoAnnonce, NoUtilisateur, Parution, Categorie, DescriptionAbregee, Prix, Etat FROM annonces WHERE Etat = 3");
    $annoncesRetirees = $stmt->fetch_all(MYSQLI_ASSOC);

    // Récupérer les utilisateurs non confirmés depuis plus d'un mois
    $stmt = $con->query("SELECT NoUtilisateur, Courriel, Nom, Prenom, Creation, NbConnexions, Statut FROM utilisateurs WHERE Statut = 0 AND Creation < DATE_SUB(NOW(), INTERVAL 1 MONTH)");
    $usersNonConfirmes = $stmt->fetch_all(MYSQLI_ASSOC);

    $vue = $_GET['nettoyage'] ?? 'annonces';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nettoyage</title>
</head>
<body>
<div class="contenu">
    <h1>Nettoyage de la base de données</h1>

    <!-- Toggle -->
    <div class="toggle">
        <a href="?nettoyage=annonces" class="<?= $vue === 'annonces' ? 'actif' : '' ?>">
            Annonces retirées (<?= $nbAnnoncesRetirees ?>)
        </a>
        <a href="?nettoyage=utilisateurs" class="<?= $vue === 'utilisateurs' ? 'actif' : '' ?>">
            Utilisateurs non confirmés (<?= $nbUsersNonConfirmes ?>)
        </a>
    </div>

    <!-- Section Annonces -->
    <?php if($vue === 'annonces'): ?>
    <div class="section">
        <h2>Annonces retirées logiquement</h2>
        <p>Ces annonces ont <strong>Etat = 3</strong> (retirées). Le nettoyage les supprime définitivement.</p>

        <?php if(isset($msgAnnonces)): ?>
            <p class="succes"><?= $msgAnnonces ?></p>
        <?php endif; ?>

        <?php if($nbAnnoncesRetirees > 0): ?>
            <form method="POST" onsubmit="return confirm('Confirmer la suppression de <?= $nbAnnoncesRetirees ?> annonce(s) ?');">
                <button type="submit" name="action" value="supprimerAnnonces" class="btn-supprimer">
                    Supprimer les <?= $nbAnnoncesRetirees ?> annonce(s) retirée(s)
                </button>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>No Utilisateur</th>
                        <th>Parution</th>
                        <th>Catégorie</th>
                        <th>Description abrégée</th>
                        <th>Prix</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($annoncesRetirees as $a): ?>
                        <tr>
                            <td><?= $a['NoAnnonce'] ?></td>
                            <td><?= $a['NoUtilisateur'] ?></td>
                            <td><?= $a['Parution'] ?></td>
                            <td><?= $a['Categorie'] ?></td>
                            <td><?= $a['DescriptionAbregee'] ?></td>
                            <td><?= $a['Prix'] == 0 ? 'À donner' : $a['Prix'] . ' $' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="aucun">Aucune annonce retirée à supprimer.</p>
        <?php endif; ?>
    </div>

    <!-- Section Utilisateurs -->
    <?php elseif($vue === 'utilisateurs'): ?>
    <div class="section">
        <h2>Utilisateurs non confirmés depuis plus d'un mois</h2>
        <p>Ces utilisateurs ont <strong>Statut = 0</strong> (En attente) et se sont inscrits il y a plus d'un mois sans confirmer leur compte.</p>

        <?php if(isset($msgUtilisateurs)): ?>
            <p class="succes"><?= $msgUtilisateurs ?></p>
        <?php endif; ?>

        <?php if($nbUsersNonConfirmes > 0): ?>
            <form method="POST" onsubmit="return confirm('Confirmer la suppression de <?= $nbUsersNonConfirmes ?> utilisateur(s) ?');">
                <button type="submit" name="action" value="supprimerUtilisateurs" class="btn-supprimer">
                    Supprimer les <?= $nbUsersNonConfirmes ?> utilisateur(s) non confirmé(s)
                </button>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Courriel</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Date inscription</th>
                        <th>Nb connexions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($usersNonConfirmes as $u): ?>
                        <tr>
                            <td><?= $u['NoUtilisateur'] ?></td>
                            <td><?= $u['Courriel'] ?></td>
                            <td><?= $u['Nom'] ?></td>
                            <td><?= $u['Prenom'] ?></td>
                            <td><?= $u['Creation'] ?></td>
                            <td><?= $u['NbConnexions'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="aucun">Aucun utilisateur à supprimer.</p>
        <?php endif; ?>
    </div>
    <?php endif; ?>

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

    .toggle {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }

    .toggle a {
        padding: 8px 18px;
        border: 1px solid #ddd;
        border-radius: 5px;
        text-decoration: none;
        color: #333;
        background: #fff;
    }

    .toggle a.actif {
        background-color: #b3b3b3;
        color: white;
        border-color: #676767;
    }

    .section {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 7px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .btn-supprimer {
        background-color: red;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        margin-bottom: 15px;
    }

    .btn-supprimer:hover {
        background-color: #c0392b;
    }

    table {
        border-collapse: collapse;
        width: 100%;
        margin-top: 10px;
    }

    th, td {
        text-align: left;
        padding: 8px 12px;
        border-bottom: 1px solid #eee;
    }

    th {
        background-color: #f0f0f0;
        font-size: 13px;
        color: #555;
    }

    .succes {
        color: #27ae60;
        font-weight: bold;
    }

    .aucun {
        color: #888;
        font-style: bold;
    }
</style>