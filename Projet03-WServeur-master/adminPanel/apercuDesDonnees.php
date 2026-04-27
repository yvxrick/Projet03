<?php
    include_once __DIR__ . "/../app/database/database.php";
    include_once __DIR__ . "/navigation.php";

    $con = DATABASE::Connect();

    function getTables(){
        global $con;

        $stmt = $con->query("SHOW TABLES");
        $nomTables = $stmt->fetch_all(MYSQLI_ASSOC);
        
        return $nomTables;    
    }

    $nomTables = getTables();
    //print_r($r);
    foreach($nomTables as $t){
        foreach($t as $cle=>$valeur){
            echo $valeur . "<br>";
        }
    }

    function getTablesInfo(){
        global $con;

        $stmt = $con->query("DESCRIBE annonces");
    }

    function getTablesColumnsInfo(){

    }

?>