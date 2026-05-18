<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://projet03-wserveur.alwaysdata.net/private/css/style.css" rel="stylesheet">
</head>

<?php
ob_start();
$page = basename(__FILE__, ".php");
require_once "../app/functions/session_manager.php";
require "./navbars/navigation_signed_in.php";
require $_SERVER['DOCUMENT_ROOT'] . "app/functions/status.php";

$user_email = $_SESSION["email"];
$user_obj = new user($user_email);
logout_if_no_session();

$fname = $user_obj->get_prenom();
$lname = $user_obj->get_nom();
$courriel = $user_obj->get_email();
$statut = $user_obj->get_statut();
$no_empl = $user_obj->get_no_employe();
$no_tel_maison = $user_obj->get_tel_maison();
$no_tel_travail = $user_obj->get_tel_travail();
$no_tel_cell = $user_obj->get_tel_cellulaire();

$no_tel_maison_public = $user_obj->get_house_number_visibility() == "P" ? true : false;
$no_tel_travail_public = $user_obj->get_work_number_visibility() == "P" ? true : false;
$no_tel_cell_public = $user_obj->get_phone_number_visibility() == "P" ? true : false;
?>

<body style="background-color: rgba(0, 0, 0, 0.03);">
    <form id="form" method="post">
    <div id="container">
        <p id="header" style="text-align: center;">Mon profil</p>
        <p>Statut d'employé</p> <?php echo make_status($user_obj->get_statut()) ?>
        <p>No. d'employé</p> <input name="no-employe" class="form-control" style="width: 100px;" type="number" value="<?php echo $no_empl ?>">
        <p>Nom de famille <span id="required">*</span> </p> <input placeholder="Nom de famille" id="nom-famille" name="nom-famille" class="form-control" style="width: 250px; margin-bottom: 0px;" type="text" value="<?php echo $lname ?>">
        <label hidden="true" id="err_lname" class="invalid-fields">Veuillez entrer votre nom de famille</label>

        <p>Prénom <span id="required">*</span> </p> <input placeholder="Prénom" id="prenom" name="prenom"class="form-control" style="width: 250px; margin-bottom: 0px;" type="text" value="<?php echo $fname ?>">
        <label hidden="true" id="err_fname" class="invalid-fields">Veuillez entrer votre prénom</label>

        <p>Courriel</p> <input name="courriel" disabled class="form-control" style="width: 250px;" type="text" value="<?php echo $courriel ?>">
        <p>Téléphone à la maison</p> <input placeholder="Facultatif" id="tel-maison" name="tel-maison" class="form-control" style="width: 250px;" type="text" value="<?php echo $no_tel_maison?>">
        <label style="margin: 0px;" hidden="true" id="err_num_maison" class="invalid-fields">Veuillez entrer un numéro de téléphone valide</label>
        <p>Téléphone au travail</p> <input placeholder="Facultatif" id="tel-travail" name="tel-travail" class="form-control" style="width: 250px;" type="text" value="<?php echo $no_tel_travail ?>">
        <label style="margin: 0px;" hidden="true" id="err_num_travail" class="invalid-fields">Veuillez entrer un numéro de téléphone valide</label>
        <p>Téléphone cellulaire</p> <input placeholder="Facultatif" id="tel-cell" name="tel-cell" class="form-control" style="width: 250px;" type="text" value="<?php echo $no_tel_cell ?>">
        <label style="margin: 0px;" hidden="true" id="err_num_cell" class="invalid-fields">Veuillez entrer un numéro de téléphone valide</label>
        <div>
            Informations de contact publiques:
            <br>
            <input <?php echo $no_tel_maison_public == true ? "checked" : ""; ?> name="contact-info-public-maison" type="checkbox" id="contact-info-public-maison" value="true"> Maison |
            <input <?php echo $no_tel_travail_public == true ? "checked" : ""; ?> name="contact-info-public-travail" type="checkbox" id="contact-info-public-travail" value="true"> Travail |
            <input <?php echo $no_tel_cell_public == true ? "checked" : ""; ?> name="contact-info-public-cel" type="checkbox" id="contact-info-public-cel" value="true"> Cellulaire |
        </div>
        <p id="legend">Légende: <span id="required">* requis</span></p>
        <input id="btn-send" style="margin-top: 10px;" class="btn btn-primary" type="button" value="Enregistrer" onclick="validateForm()">
    </div>
    </form>
    <script>
        let canSendForm = true;
        let btn_send = document.getElementById("btn-send")
        let regExPhoneNumber = /^\+?[0-9]{0,3}\W?\(?[0-9]{3}\)?[-\s.]?[0-9]{3}[-\s.]?[0-9]{4,6}$/

        let err_lname = document.getElementById("err_lname");
        let err_fname = document.getElementById("err_fname");
        let err_num_maison = document.getElementById("err_num_maison")
        let err_num_travail = document.getElementById("err_num_travail")
        let err_num_cell = document.getElementById("err_num_cell")

        function validateForm() {
            let nom_famille = document.getElementById("nom-famille");
            let prenom = document.getElementById("prenom");

            // telephones
            let num_maison = document.getElementById("tel-maison").value
            let num_travail = document.getElementById("tel-travail").value
            let num_cel = document.getElementById("tel-cell").value

            err_lname.hidden = nom_famille.value.trim() != ""
            err_fname.hidden = prenom.value.trim() != ""
            canSendForm = nom_famille.value.trim() !== "" && prenom.value.trim() !== "" && 
                        !(regExPhoneNumber.test(num_maison) == false && num_maison.trim() != "") &&
                        !(regExPhoneNumber.test(num_travail) == false && num_travail.trim() != "") &&
                        !(regExPhoneNumber.test(num_cel) == false && num_cel.trim() != "")

            err_num_maison.hidden = !(regExPhoneNumber.test(num_maison) == false && num_maison.trim() != "")
            err_num_travail.hidden = !(regExPhoneNumber.test(num_travail) == false && num_travail.trim() != "")
            err_num_cell.hidden = !(regExPhoneNumber.test(num_cel) == false && num_cel.trim() != "")
    
            if (canSendForm) {sendForm();}
        }

        function sendForm() {
            // a mettre statut empl
            btn_send.disabled = true;
            btn_send.value = "En cours d'enregistrement...";
            document.getElementById("form").submit()
        }

    </script>
</body>
</html>

<?php
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $set_public_tel_maison = $_POST["contact-info-public-maison"] ?? null;
    $set_public_tel_travail = $_POST["contact-info-public-travail"] ?? null;
    $set_public_tel_cel = $_POST["contact-info-public-cel"] ?? null;

    $no_empl = $_POST["no-employe"] ?? "";
    $nom_famille = $_POST["nom-famille"] ?? "";
    $prenom = $_POST["prenom"] ?? "";
    $tel_maison = $_POST["tel-maison"] ?? "";
    $tel_travail = $_POST["tel-travail"] ?? "";
    $tel_cell = $_POST["tel-cell"] ?? "";
    $statut = $_POST["statut"] ?? null;

    $user_obj->set_statut($statut);
    $user_obj->set_no_empl($no_empl);
    $user_obj->set_nom($nom_famille);
    $user_obj->set_prenom($prenom);
    $user_obj->set_tel_maison($tel_maison, $set_public_tel_maison);
    $user_obj->set_tel_travail($tel_travail, $set_public_tel_travail);
    $user_obj->set_tel_cellulaire($tel_cell, $set_public_tel_cel);
    $user_obj->add_profile_change();
    header("Location: index.php?page=1&num_ads=5");
    echo "OK";
    ob_get_clean();
}