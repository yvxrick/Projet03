<?php
require "navbars/navigation_signed_out.php";
require "footers/footer.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Se connecter</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://projet03-wserveur.alwaysdata.net/public/css/signup-in.css" rel="stylesheet">
</head>

<body>
    <form id="form" method="post">
        <div hidden="true" id="sign-up-status-msg">
            <span id="msg"></span>
            <button style="padding-left: 20px " onclick="closeStatusMsg()" type="button" class="btn-close"
                aria-label="Close"></button>
        </div>
        <div class="form-group" id="container">

            <label id="header">Se connecter</label>
            <label>Adresse courriel</label>
            <input onkeyup="validateEmail()" class="form-control" id="email" name="email" type="email"
                placeholder="Adresse courriel">
            <label hidden="true" class="invalid-fields" id="invalid-email">Veuillez entrer une adresse courriel
                valide</label>
            <label>Mot de passe</label>
            <input class="form-control" type="password" id="password" name="password"
                placeholder="Mot de passe">
            <div hidden="true" id="invalid-password" class="invalid-fields">
                <label>Le mot de passe doit respecter les contraintes suivantes:
                </label>
            </div>
            <input id="sign-up-button" onclick="sendForm()" type="button" class="btn btn-primary" value="Se connecter">
        </div>
        <div id="forgot-passwd">
            <a href="https://projet03-wserveur.alwaysdata.net/public/forgot_password.php">J'ai oublié mon mot de passe</a>
        </div>
    </form>
    <script>
        let URLParams = new URLSearchParams(document.location.search)
        let email_match = false
        let err_format_email = document.getElementById("invalid-email")

        function validateEmail() {
            let regex_email = /^[\w\-\.]+@([\w-]+\.)+[\w-]{2,}$/
            let email = document.getElementById("email").value
            let email_valide = regex_email.test(email)

            if (email == "") {
                err_format_email.hidden = true
                return
            }
            if (!email_valide) {
                err_format_email.innerText = "Veuillez entrer une adresse courriel valide"
                err_format_email.hidden = false
                return;
            }
            else {
                err_format_email.hidden = true  
            }
            
        }
    </script>

</body>


</html>