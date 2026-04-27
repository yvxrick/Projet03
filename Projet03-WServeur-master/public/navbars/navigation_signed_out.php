<?php
require $_SERVER['DOCUMENT_ROOT'] . "app/functions/page_is_active.php";
$p =  $_GET['p'] ?? null;

$login = "https://projet03-wserveur.alwaysdata.net/index.php?p=login";
$signup = "https://projet03-wserveur.alwaysdata.net/index.php?p=signup";
?>

<div id="navbar">
    <ul>
        <li class='<?php echo page_active("login", $p)?>'><a href='<?php echo $login ?>'>Connexion</a></li>
        <li class='<?php echo page_active("signup", $p)?>'><a href='<?php echo $signup ?>'>S'inscrire</a></li>
    </ul>
</div>

    <style>
        #navbar {
            overflow: hidden;
            background-color: #333333;
            margin-bottom: 30px;
        }

        #navbar ul li {
            float: right;
            list-style-type: none;
        }

        #navbar ul li a {
            display: block;
            color: white;
            text-align: center;
            padding: 16px 10px;
            text-decoration: none;
            font-size: 20px;
        }

        #navbar ul li a:hover {
            background-color: #111111;
        }
        .active {
            background-color: green;
        }
    </style>