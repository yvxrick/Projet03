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

<?php
require "navbars/navigation_signed_out.php";
require "footers/footer.php";
?>

<body>
    <form id="form" method="post">
        <div hidden="true" id="log-in-status-msg">
            <span id="msg"></span>
            <button style="padding-left: 20px " onclick="closeStatusMsg()" type="button" class="btn-close"
                aria-label="Close"></button>
        </div>
        <div class="form-group" id="container">

            <label id="header">Se connecter</label>
            <label>Adresse courriel</label>
            <input class="form-control" id="email" name="email" type="email" placeholder="Adresse courriel">
            <label hidden="true" class="invalid-fields" id="invalid-email">Veuillez entrez votre adresse
                courriel</label>
            <label>Mot de passe</label>
            <input class="form-control" type="password" id="password" name="password" placeholder="Mot de passe">
            <div hidden="true" id="invalid-password" class="invalid-fields">
                <label>Veuillez entrez votre mot de passe
                </label>
            </div>
            <input id="log-in-button" onclick="sendForm()" type="button" class="btn btn-primary" value="Se connecter">
        </div>
        <div id="forgot-passwd">
            <a href="https://projet03-wserveur.alwaysdata.net/public/forgot_password.php">J'ai oublié mon mot de
                passe</a>
        </div>
    </form>
    <script>
        let URLParams = new URLSearchParams(document.location.search)
        let email_match = false
        let err_email = document.getElementById("invalid-email")
        let err_passwd = document.getElementById("invalid-password")
        let login_msg_div = document.getElementById("log-in-status-msg")
        let stauts_msg = document.querySelector("#log-in-status-msg #msg")
        let btn_login = document.getElementById("log-in-button")

        function sendForm() {
            let email = document.getElementById("email").value
            let passwd = document.getElementById("password").value
            let canSendForm = true
            if (email.trim() == "") {
                err_email.hidden = false
                canSendForm = false
            } else {
                err_email.hidden = true
                canSendForm = true
            }
            if (passwd.trim() == "") {
                err_passwd.hidden = false
                canSendForm = false
            } else {
                err_passwd.hidden = true
                canSendForm = true
            }
            if (canSendForm) {
                login_msg_div.hidden = true

                let formData = new FormData()
                formData.append("email", email)
                formData.append("password", passwd)
                btn_login.disabled = true
                btn_login.value = "Connexion..."
                fetch("https://projet03-wserveur.alwaysdata.net/app/auth/login.php", {
                    method: "POST",
                    body: formData
                }).then((response) => response.text())
                .then((response) => {getLogInResponse(response); btn_login.disabled = false; btn_login.value = "Se connecter";})
            }
        }
        function getLogInResponse(response) {
            const status = {
                NOT_AUTHENTICATED: "Not authenticated.",
                INVALID: "Invalid credentials.",
                OK: "OK"
            }
            if (response === status.INVALID) {
                login_msg_div.style.backgroundColor = "red"
                stauts_msg.innerHTML = "Votre mot de passe est incorrect ou le compte n'existe pas."
                login_msg_div.hidden = false
                return;
            }
            if (response === status.NOT_AUTHENTICATED) {
                login_msg_div.style.backgroundColor = "red"
                stauts_msg.innerHTML = "Votre compte existe, mais il n'est pas authentifié."
                login_msg_div.hidden = false
                return;
            }
            // Redirige l'utilisateur authentifié
            window.location.href = "https://projet03-wserveur.alwaysdata.net/private/index.php"
        }

        function closeStatusMsg() {
            login_msg_div.hidden = true
        }
    </script>

</body>

</html>