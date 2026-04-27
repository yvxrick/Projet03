<?php
    include_once __DIR__ . '/../app/database/database.php';

    $con = Database::Connect();

    $noUtilisateur = 3; 

    $result = $con->query("SELECT * FROM utilisateurs WHERE NoUtilisateur = $noUtilisateur");
    $user = $result->fetch_assoc();

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $statut = $_POST['statut'];
        $noEmploye = $_POST['noEmploye'];
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $telMaison = $_POST['telMaison'];
        $telTravail = $_POST['telTravail'];
        $telCell = $_POST['telCell'];

        $stmt = $con->prepare("UPDATE utilisateurs SET 
            Statut = ?, 
            NoEmpl = ?, 
            Nom = ?, 
            Prenom = ?, 
            NoTelMaison = ?, 
            NoTelTravail = ?, 
            NoTelCellulaire = ?,
            Modification = NOW()
            WHERE NoUtilisateur = ?");

        $stmt->bind_param("issssssi", 
            $statut, 
            $noEmploye, 
            $nom, 
            $prenom, 
            $telMaison, 
            $telTravail, 
            $telCell,
            $noUtilisateur
        );

        $stmt->execute();

        header("Location: profile.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="profile.css">
</head>
<body style="display:flex">
    <?php include_once 'menuPrincipal.php' ?>
    <div class="contenu">
        <h2>Mon profil</h2>

        <form method="POST" class="form-profile">

            <div class="ligne">
                <label>Statut employé</label>
                <input type="text" name="statut" value="<?= $user['Statut'] ?>">
            </div>

            <div class="ligne">
                <label>Numéro employé</label>
                <input type="text" name="noEmploye" value="<?= $user['NoEmpl'] ?>">
            </div>

            <div class="ligne">
                <label>Nom</label>
                <input type="text" name="nom" value="<?= $user['Nom'] ?>">
            </div>

            <div class="ligne">
                <label>Prénom</label>
                <input type="text" name="prenom" value="<?= $user['Prenom'] ?>">
            </div>

            <div class="ligne">
                <label>Courriel</label>
                <input type="email" value="<?= $user['Courriel'] ?>" disabled>
            </div>

            <div class="ligne">
                <label>Téléphone maison</label>
                <input type="text" name="telMaison" value="<?= $user['NoTelMaison'] ?>">
            </div>

            <div class="ligne">
                <label>Téléphone travail</label>
                <input type="text" name="telTravail" value="<?= $user['NoTelTravail'] ?>">
            </div>

            <div class="ligne">
                <label>Téléphone cellulaire</label>
                <input type="text" name="telCell" value="<?= $user['NoTelCellulaire'] ?>">
            </div>

            <button type="submit">Sauvegarder</button>

        </form>
    </div>

</body>
</html>