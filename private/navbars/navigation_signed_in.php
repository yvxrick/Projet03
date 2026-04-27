<?php
require $_SERVER['DOCUMENT_ROOT'] . "app/functions/page_is_active.php";
$user_id = $_SESSION["user_id"];
$profil = "https://projet03-wserveur.alwaysdata.net/private/profil.php";
?>

<div id="navbar">
    <ul>
        <li><a href='https://projet03-wserveur.alwaysdata.net/private/logout.php'>Déconnexion</a></li>
        <li class='<?php echo page_active("profil", $page) ?>'><a href='<?php echo $profil ?>'>Profil</a></li>
        <li class='<?php echo page_active("add_ad", $page)?>'><a href='https://projet03-wserveur.alwaysdata.net/private/add_ad.php'>Ajouter une annonce</a></li>
        <li class='<?php echo page_active("user", $page)?>'><a href='https://projet03-wserveur.alwaysdata.net/private/index.php?page=1&num_ads=5'>Menu principal</a></li>
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