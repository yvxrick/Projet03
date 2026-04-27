<?php
    include_once __DIR__ . "/../app/database/database.php";
    include_once __DIR__ . "/navigation.php";
        
    #Creation connexion base de donnee
    $con = DATABASE::Connect();

    #Recuperer les annonces
    $stmt = $con->query("SELECT * FROM annonces");
    $annonces = $stmt->fetch_all(MYSQLI_ASSOC);

    foreach($annonces as $a){
        foreach($a as $cle => $valeur){
            echo $cle . ": " . $valeur . "<br>";
        } 
        echo "<br>";
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Annonces</title>
</head>
<body style="display:flex">
    
</body>
</html>



