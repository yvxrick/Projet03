<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une annonce</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://projet03-wserveur.alwaysdata.net/private/css/style.css?v=12" rel="stylesheet">
</head>

<?php
$page = basename(__FILE__, ".php");
require_once "../app/functions/session_manager.php";
require_once "./navbars/navigation_signed_in.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "app/database/categories.php";
$categories_obj = new categories();
$categories_options = $categories_obj->make_categories_list();
?>

<body>
    <form method="post" action="https://projet03-wserveur.alwaysdata.net/app/auth/add_ad.php" enctype="multipart/form-data">
        <div style="gap: 3px" id="container">
            <p id="header" style="text-align: center;">Ajouter une annonce</p>
            <p>Titre de l'annonce (Description abrégée)</p>
            <input required class="form-control" name="ad-desc-abr" type="text" maxlength="50">
            <p>Description complète</p>
            <textarea required class="form-control" name="ad-desc-full" style="max-height: 200px; min-height: 100px;"
                maxlength="250"></textarea>
            <p>Type d'annonce</p>
            <?php echo $categories_options ?>
            <p>Prix</p>
            <div style="display: flex; align-items: center; gap: 5px;">
                <input required min="0" max="99999.99" style="max-width: 20%;" class="form-control" name="ad-price"
                    type="number">
                <span style="font-weight: bold; color: green">$CAD</span>
            </div>
            <p>Photo</p>
            <input required class="form-control" name="ad-photo" type="file" value="Choisir" accept=".jpg .png .gif">
            <p>État de l'annonce</p>
            <select style="max-width: 20%;" class="form-control" required name="ad-state">
                <option value="1">Actif</option>
                <option value="2">Inactif</option>
                <option value="3">Retiré</option>
            </select>
            <input style="margin-top: 10px;" class="btn btn-success" type="submit" value="Publier mon annonce ↑"> 
        </div>
    </form>
</body>

</html>