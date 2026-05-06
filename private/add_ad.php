<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta id="" name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une annonce</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://projet03-wserveur.alwaysdata.net/private/css/style.css?v=1" rel="stylesheet">
</head>

<?php
$page = basename(__FILE__, ".php");
require_once "../app/functions/session_manager.php";
require_once "./navbars/navigation_signed_in.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "app/database/categories.php";
logout_if_no_session();
$categories_obj = new categories();
$categories_options = $categories_obj->make_categories_list();
?>

<body>
    <form method="post" onsubmit="sendForm(event)" enctype="multipart/form-data">
        <div hidden="true" id="ad_upload_status_msg">
            <span id="msg"></span>
            <button style="padding-left: 20px " onclick="closeStatusMsg()" type="button" class="btn-close" aria-label="Close"></button>
        </div>
        <div style="gap: 3px" id="container">
            <p id="header" style="text-align: center;">Ajouter une annonce</p>
            <p>Titre de l'annonce (Description abrégée)</p>
            <input required class="form-control" id="ad-desc-abr" name="ad-desc-abr" type="text" maxlength="50">
            <p>Description complète</p>
            <textarea required class="form-control" id="ad-desc-full" name="ad-desc-full" style="max-height: 200px; min-height: 100px;"
                maxlength="250"></textarea>
            <p>Type d'annonce</p>
            <?php echo $categories_options ?>
            <p>Prix</p>
            <div style="display: flex; align-items: center; gap: 5px;">
                <input required min="0" max="99999.99" style="max-width: 20%;" class="form-control" id="ad-price" name="ad-price"
                    type="number">
                <span style="font-weight: bold; color: green">$CAD</span>
            </div>
            <p>Photo</p>
            <input required class="form-control" id="ad-photo" name="ad-photo" type="file" value="Choisir" accept=".jpg, .png, .gif">
            <p>État de l'annonce</p>
            <select style="max-width: 20%;" class="form-control" required id="ad-state" name="ad-state">
                <option value="1">Actif</option>
                <option value="2">Inactif</option>
                <option value="3">Retiré</option>
            </select>
            <input style="margin-top: 10px;" id="btn-send-form" class="btn btn-success" type="submit" value="Publier mon annonce ↑"> 
        </div>
    </form>
    <script>
        let status_div = document.getElementById("ad_upload_status_msg")
        let status_msg = document.getElementById("msg")

        function sendForm(event) {
            event.preventDefault();
            status_div.hidden = true

            let ad_desc_abr = document.getElementById("ad-desc-abr").value
            let ad_desc_full = document.getElementById("ad-desc-full").value
            let ad_price = document.getElementById("ad-price").value
            let ad_photo = document.getElementById("ad-photo").files[0]
            let ad_state = document.getElementById("ad-state").value
            let ad_category = document.getElementById("ad-categorie").value
            let btn_send = document.getElementById("btn-send-form")

            let formData = new FormData()
            formData.append("ad-desc-abr", ad_desc_abr)
            formData.append("ad-desc-full", ad_desc_full)
            formData.append("ad-price", ad_price)
            formData.append("ad-photo", ad_photo)
            formData.append("ad-state", ad_state)
            formData.append("ad-categorie", ad_category)

            btn_send.disabled = true;
            btn_send.value = "Ajout de l'annonce..."
            fetch("https://projet03-wserveur.alwaysdata.net/app/auth/add_ad.php",{
                method: "POST",
                body: formData
            }).then(response => response.text())
            .then((response) => getResponse(response))
            .finally(() => {btn_send.disabled = false; btn_send.value = "Publier mon annonce ↑"})
        }

        function getResponse(response) {
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
                    status_msg.innerText = "Votre annonce à été ajouté. Vous serez redirigé dans un instant..."
                    status_div.style.backgroundColor = "rgba(88, 252, 96, 1)"
                    status_div.hidden = false
                    setTimeout(() => {window.location.href = "index.php"}, 3000)
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