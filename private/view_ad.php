<?php
require_once "../app/database/annonces.php";
require_once "../app/functions/session_manager.php";
require "./navbars/navigation_signed_in.php";
logout_if_no_session();
$ads_obj = new annonces();
$ad_id = $_GET["id"];
$ad = $ads_obj->get_ad($ad_id);
if ($ad == null) {
    header("Location: index.php");
    die();
}
$ad_img = $ad["Photo"];
$ad_author = $ad["Prenom"] . " " .$ad["Nom"];
$ad_title = $ad["DescriptionAbregee"];
$ad_desc = $ad["DescriptionComplete"];
$ad_category = $ad["Description"];
$ad_price = number_format($ad["Prix"], 2, ".") . " $";
$ad_date_added = $ad["Parution"];
$ad_date_modified = $ad["MiseAJour"];
$ad_photo = $ad["Photo"];

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Annonce #<?php echo $ad["NoAnnonce"] ?></title>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://projet03-wserveur.alwaysdata.net/private/css/style.css?v=1" rel="stylesheet">
</head>

<body style="background-color: #f5f7fa;">
<div class="container mb-5">

    <div class="card shadow-sm border-0">
        <div class="row g-0">
            <div class="col-md-6">
                <img 
                    src="<?php echo "https://projet03-wserveur.alwaysdata.net/private/ads-images/$ad_photo" ?>"
                    class="img-fluid rounded-start h-100"
                    style="object-fit: cover;"
                >
            </div>
            <div class="col-md-6">
                <div class="card-body d-flex flex-column h-100">
                    <h3 class="fw-bold mb-2">
                        <?php echo $ad_title ?>
                    </h3>
                    <h2 class="text-success fw-bold mb-3">
                        <?php echo $ad_price ?>
                    </h2>
                    <p class="text-muted">
                        <?php echo $ad_desc ?>
                    </p>
                    <hr>
                    <div class="mb-3">
                        <p class="mb-1"><strong>Vendeur:</strong> <?php echo $ad_author ?></p>
                        <p class="mb-1"><strong>Catégorie:</strong> <?php echo $ad_category ?></p>
                        <p class="mb-1"><strong>Publié le:</strong> <?php echo $ad_date_added ?></p>

                        <?php if ($ad_date_modified): ?>
                            <p class="mb-1">
                                <strong>Mis à jour:</strong> <?php echo $ad_date_modified ?>
                            </p>
                        <?php endif; ?>
                    </div>
                    <div class="mt-auto">
                        <a href="index.php" class="btn btn-outline-secondary w-100 mb-2">
                            ← Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>


</html>