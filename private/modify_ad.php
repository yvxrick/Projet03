<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier mon annonce</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://projet03-wserveur.alwaysdata.net/private/css/style.css?v=2" rel="stylesheet">
</head>

<?php
$page = basename(__FILE__, ".php");
require_once "../app/functions/session_manager.php";
require_once "../app/database/user.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "app/database/annonces.php";
require "../app/functions/pagination.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "app/database/categories.php";
$categories_obj = new categories();

$user_email = $_SESSION["email"];
$user_id = $_SESSION["user_id"];
$ad_id = $_GET["id"] ?? null;
$user_obj = new user($user_email);
$ads_obj = new annonces();
logout_if_no_session();
redirect_if_no_profile($user_email);
if (!$ads_obj->is_users_ad($user_id, $ad_id)) {
    require "./forbidden.html";
    exit();
}
$ad = $ads_obj->get_ad($ad_id);
$categories_options = $categories_obj->make_categories_list($ad["NoCategorie"]);

$ad_title = $ad["DescriptionAbregee"];
$ad_desc = $ad["DescriptionComplete"];
$ad_price = $ad["Prix"];
$ad_photo = $ad["Photo"];

require "./navbars/navigation_signed_in.php";
?>

<body style="background-color: rgba(0, 0, 0, 0.03);">
    <form method="post" onsubmit="sendForm(event)" enctype="multipart/form-data">
        <div hidden="true" id="ad_upload_status_msg">
            <span id="msg"></span>
            <button style="padding-left: 20px " onclick="closeStatusMsg()" type="button" class="btn-close"
                aria-label="Close"></button>
        </div>
        <div style="gap: 3px" id="container">
            <p id="header" style="text-align: center;">Modifier une annonce</p>
            <p>Titre de l'annonce (Description abrégée)</p>
            <input required class="form-control" id="ad-desc-abr" name="ad-desc-abr" type="text" maxlength="50"
                value="<?php echo $ad_title ?>">
            <p>Description complète</p>
            <textarea required class="form-control" id="ad-desc-full" name="ad-desc-full"
                style="max-height: 200px; min-height: 100px;" maxlength="250"><?php echo $ad_desc ?></textarea>
            <p>Type d'annonce</p>
            <?php echo $categories_options ?>
            <p>Prix</p>
            <div style="display: flex; align-items: center; gap: 5px;">
                <input required min="0" max="99999.99" style="max-width: 20%;" class="form-control" id="ad-price"
                    name="ad-price" type="number" value="<?php echo $ad_price ?>">
                <span style="font-weight: bold; color: green">$CAD</span>
            </div>
            <div>
                <input onchange="keepPhoto()" type="checkbox" id="keep-photo">
                <label for="keep-photo">Garder ma photo</label>
            </div>
            <div id="toggle-ad-photo">
                <p>Photo</p>
                <input required class="form-control" id="ad-photo" name="ad-photo" type="file" value="Choisir"
                    accept=".jpg, .png, .gif" value="<?php echo $ad_photo ?>">
            </div>
            <p>État de l'annonce</p>
            <select style="max-width: 20%;" class="form-control" required id="ad-state" name="ad-state">
                <option <?php echo is_ad_state(1, $ad) ?> value="1">Actif</option>
                <option <?php echo is_ad_state(2, $ad) ?> value="2">Inactif</option>
                <option <?php echo is_ad_state(3, $ad) ?> value="3">Retiré</option>
            </select>
            <input style="margin-top: 10px;" id="btn-send-form" class="btn btn-primary" type="submit"
                value="Mettre à jour mon annonce ↑">
            <a class="btn btn-dark" href="my_ads.php">
                ← Retour
            </a>
        </div>
    </form>
    <script>
        let keepUserPhoto = false
        let ad_photo_div = document.querySelector("#toggle-ad-photo")
        let ad_photo = document.getElementById("ad-photo")
        let ad_id = new URLSearchParams(document.location.search).get("id")
        let status_msg = document.getElementById("msg")
        let status_div = document.getElementById("ad_upload_status_msg")

        function sendForm() {
            event.preventDefault()
            let ad_desc_abr = document.getElementById("ad-desc-abr").value
            let ad_desc_full = document.getElementById("ad-desc-full").value
            let ad_price = document.getElementById("ad-price").value
            let ad_photo = document.getElementById("ad-photo").files[0]
            let ad_state = document.getElementById("ad-state").value
            let ad_category = document.getElementById("ad-categorie").value
            let btn_send = document.getElementById("btn-send-form")

            closeStatusMsg()
            btn_send.disabled = true
            btn_send.value = "En cours de traitement..."

            let formData = new FormData();
            formData.append("ad-id", ad_id)
            formData.append("ad-desc-abr", ad_desc_abr)
            formData.append("ad-desc-full", ad_desc_full)
            formData.append("ad-price", ad_price)
            formData.append("ad-state", ad_state)
            formData.append("ad-categorie", ad_category)
            keepUserPhoto === true ? formData.append("ad-photo", "keep") : formData.append("ad-photo", ad_photo)
            fetch("https://projet03-wserveur.alwaysdata.net/app/auth/modify_ad.php", {
                method: "POST",
                body: formData
            }).then((response) => response.text())
                .then((response) => getResponse(response))
            .finally(() => {btn_send.value = "Annonce mis à jour"; })


        }

        function keepPhoto() {
            let state = document.querySelector("#keep-photo").checked
            ad_photo_div.hidden = state
            keepUserPhoto = state
            state === true ? ad_photo.removeAttribute("required") : ad_photo.setAttribute("required", true)
        }

        function getResponse(response) {
            console.log(response)
            const status = {
                IMG_TOO_LARGE: "Image is too large.",
                NOT_AN_IMG: "File is not an image.",
                AD_TITLE_EMPTY: "The ad title cannot be empty.",
                OK: "OK"
            }
            switch (response) {
                case status.IMG_TOO_LARGE:
                    status_msg.innerText = "Votre image est trop large. La taille maximale d'une image est de 5MB."
                    status_div.style.backgroundColor = "red"
                    status_div.hidden = false
                    break;
                case status.NOT_AN_IMG:
                    status_msg.innerText = "Votre photo n'est pas une du format attendu. Veuillez choisir une image du format suivant: .jpg, .png, .gif"
                    status_div.style.backgroundColor = "red"
                    status_div.hidden = false
                    break;
                case status.AD_TITLE_EMPTY:
                    status_msg.innerText = "Le titre de l'annonce ne peut pas être vide."
                    status_div.style.backgroundColor = "red"
                    status_div.hidden = false
                    break;
                case status.OK:
                    status_msg.innerText = "Votre annonce a été modifiée. Vous serez redirigé dans un instant..."
                    status_div.style.backgroundColor = "rgba(88, 252, 96, 1)"
                    status_div.hidden = false
                    setTimeout(() => { window.location.href = "my_ads.php" }, 3000)
                    break;
                default:
                    status_msg.innerText = "Une erreur est survenue. Vérifier que vos champs respectent les critères demandés."
                    status_div.style.backgroundColor = "red"
                    status_div.hidden = false
                    break;
            }

        }

        function closeStatusMsg() {
            status_div.hidden = true
        }

    </script>
</body>

</html>