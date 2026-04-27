<?php
    include_once __DIR__ . "/../app/database/database.php";
    include_once __DIR__ . "/navigation.php";

    #Creation connexion base de donnee
    $con = DATABASE::Connect();
    
    #Recuperer les annonces
    $stmt = $con->query("SELECT * FROM annonces");
    $annonces = $stmt->fetch_all(MYSQLI_ASSOC);

    #Recuperer les utilisateurs
    $stmt = $con->query("SELECT * FROM utilisateurs");
    $utilisateurs = $stmt->fetch_all(MYSQLI_ASSOC);

    #Supprimer une annonce
    if($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'supprimerAnnonce'){
        $id = $_POST['id'];
        $stmt = $con->prepare("DELETE FROM annonces WHERE NoAnnonce = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }

    #Supprimer un utilisateur
    function supprimerUtilisateur($id){

    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nettoyage</title>
</head>
<body>
    <h2>Annonces</h2>
    <table>
        <thead>
            <?php
                #Affichage des cles pour l'entete du tableau
                foreach(array_keys($annonces[0]) as $cle){
                    echo "<td>" . $cle . "<td>";
                }
            ?>
        </thead>
        <tbody>
            <?php
                #Affichage des annonces
                foreach($annonces as $a): ?>
                <tr>
                    <?php foreach($a as $cle => $valeur): ?>
                        <td><?= $valeur ?? 'N/A' ?></td>
                    <?php endforeach; ?>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="id" value="<?= $a['NoAnnonce'] ?>">
                            <button type="submit" name="action" value="supprimerAnnonce">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        
        </tbody>
    </table>

    <h2>Utilisateurs</h2>
    <table>
        <thead>
            <?php
                #Affichage des cles pour l'entete du tableau
                foreach(array_keys($utilisateurs[0]) as $cle){
                    echo "<td>" . $cle . "<td>";
                }
            ?>
        </thead>
        <tbody>
            <?php
                #Affichage des utilisateurs
                foreach($utilisateurs as $u){
                    echo "<tr>";
                    foreach($u as $cle => $valeur){
                        echo "<td>" . ($valeur ?? 'N/A') . "<td>"; 
                    }
                    echo "</tr>";
                }
            ?>
        </tbody>
    </table>
</body>
</html>