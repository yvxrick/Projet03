<?php
    include_once __DIR__ . "/../app/database/database.php";
    include_once __DIR__ . "/navigation.php";

    #Creation connexion base de donnee
    $con = DATABASE::Connect();

    #Recuperer les utilisateurs
    $stmt = $con->query("SELECT * FROM utilisateurs");
    $utilisateurs = $stmt->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Utilisateurs</title>
</head>
<body>
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