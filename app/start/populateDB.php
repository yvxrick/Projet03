<?php 
require "../database/database.php";

$con = Database::Connect();
$sql = file_get_contents("init.sql");
$con->multi_query($sql);
echo "OK";