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
$ad_author = $ad["Prenom"] . " " . $ad["Nom"];
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
    <link href="https://projet03-wserveur.alwaysdata.net/private/css/style.css?v=22" rel="stylesheet">
</head>

<body style="background-color: #f5f7fa;">
    <div class="container mb-5">
        <div hidden="true" id="ad_upload_status_msg">
            <span id="msg"></span>
            <button style="padding-left: 20px " onclick="closeStatusMsg()" type="button" class="btn-close"
                aria-label="Close"></button>
        </div>
        <div class="card shadow-sm border-0">
            <div id="remove-msg-div" style="background-color: red; border: 1px solid black;" class="pb-2 p-1">
                <h3 style="color: yellow">Êtes-vous sûr de vouloir supprimer cette annonce ?</h3>
                <label style="color: white">Cette action n'est pas reversible.</label>
            </div>
            <div class="row g-0">
                <div class="col-md-6 view_ad">
                    <img src="<?php echo "https://projet03-wserveur.alwaysdata.net/private/ads-images/$ad_photo" ?>"
                        style="border-radius: 0px 0px 0px 5px">
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
                            <a href="my_ads.php" class="btn btn-outline-secondary w-100 mb-2">
                                ← Retour
                            </a>
                            <button id="btn-send" onclick="requestDeletion()" class="btn btn-outline-danger w-100 mb-2">
                                Supprimer →
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        let URLParams = new URLSearchParams(document.location.search)
        let ad_id = URLParams.get("id")
        let remove_ad_div = document.getElementById("remove-msg-div")
        let status_div = document.getElementById("ad_upload_status_msg")
        let status_msg = document.getElementById("msg")
        let btn_send = document.getElementById("btn-send")

        function requestDeletion() {
            btn_send.innerText = "En cours de supprimation..."
            btn_send.disabled = true
            remove_ad_div.hidden = true
            fetch(`https://projet03-wserveur.alwaysdata.net/app/auth/remove_ad.php?id=${ad_id}&delete=true`)
                .then((response) => response.text())
                .then((response) => {getResponse(response)})
                .finally(() => {btn_send.innerText = "Supprimé"; btn_send.style.backgroundColor = "red"; btn_send.style.color = "white"})
        }

        function getResponse(response) {
            if (response === "OK") {
                status_div.style.backgroundColor = "rgb(255, 146, 146)"
                status_msg.innerText = "Votre annonce a été supprimé. Vous allez être redirigé..."
                status_div.hidden = false
                setTimeout(() => window.location.href = "my_ads.php", 3000)
            }
        }
        function closeStatusMsg() {
            status_div.hidden = true
        }
    </script>
</body>


</html>