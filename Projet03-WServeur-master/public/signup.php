<?php
require "navbars/navigation_signed_out.php";
require "footers/footer.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S'inscrire</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://projet03-wserveur.alwaysdata.net/public/css/signup-in.css" rel="stylesheet">
</head>

<body>
    <form id="form" method="post">
        <div hidden="true" id="sign-up-status-msg">
            <span id="msg"></span>
            <button style="padding-left: 20px " onclick="closeStatusMsg()" type="button" class="btn-close" aria-label="Close"></button>
        </div>
        <div class="form-group" id="container">

            <label id="header">S'inscrire</label>
            <label>Adresse courriel</label>
            <input onkeyup="validateEmail()" class="form-control" id="email" name="email" type="email"
                placeholder="Adresse courriel">
            <label hidden="true" class="invalid-fields" id="invalid-email">Veuillez entrer une adresse courriel
                valide</label>
            <label>Confirmation de courriel</label>
            <input onkeyup="validateEmail()" class="form-control" id="email-confirm" name="email-confirm" type="email"
                placeholder="Confirmer votre courriel">
            <label>Mot de passe</label>
            <input onkeyup="validePassword()" class="form-control" type="password" id="password" name="password"
                placeholder="Mot de passe">
            <div hidden="true" id="invalid-password" class="invalid-fields">
                <label>Le mot de passe doit respecter les contraintes suivantes:
                    <ul>
                        <li>Doit contenir 5 à 15 caractères</li>
                        <li>Doit contenir au moins un caractère minuscule et majuscule</li>
                        <li>Doit contenir au moins un chiffre</li>
                    </ul>
                </label>
            </div>
            <input id="sign-up-button" onclick="sendForm()" type="button" class="btn btn-primary" value="S'inscrire">
        </div>
    </form>
    <script>
        let URLParams = new URLSearchParams(document.location.search)
        let email_match = false, passwd_valide = false;
        let err_passwd = document.getElementById("invalid-password")
        let err_format_email = document.getElementById("invalid-email")

        function validateEmail() {
            let regex_email = /^[\w\-\.]+@([\w-]+\.)+[\w-]{2,}$/
            let email = document.getElementById("email").value
            let email_confirm = document.getElementById("email-confirm").value
            let email_valide = regex_email.test(email)

            if (email == "" || email_confirm == "") {
                err_format_email.hidden = true
                return
            }
            if (!email_valide) {
                err_format_email.innerText = "Veuillez entrer une adresse courriel valide"
                err_format_email.hidden = false
                return;
            }
            if (email != "" && email_confirm != "") {
                email_match = email == email_confirm
                if (email_valide && !email_match) {
                    err_format_email.innerText = "Les adresses courrielles doivent être identiques"
                    err_format_email.hidden = false
                } else {
                    err_format_email.hidden = true
                }
            }
        }
        function validePassword() {
            let regex_passwd = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{5,15}$/
            let passwd = document.getElementById("password").value
            passwd_valide = regex_passwd.test(passwd)

            err_passwd.hidden = passwd_valide
        }

        function sendForm() {
            let canSendForm = true

            if (!email_match) {
                err_format_email.hidden = false
                canSendForm = false
            }
            if (!passwd_valide) {
                err_passwd.hidden = false
                canSendForm = false
            }
            if (canSendForm) {
                let email = document.getElementById("email").value
                let email_confirm = document.getElementById("email-confirm").value
                let passwd = document.getElementById("password").value

                let button_sign_in = document.getElementById("sign-up-button")
                button_sign_in.disabled = true
                button_sign_in.value = "En cours d'inscription..."

                let status_msg_div = document.getElementById("sign-up-status-msg")
                status_msg_div.hidden = true

                let formData = new FormData();
                formData.append("email", email);
                formData.append("email-confirm", email_confirm);
                formData.append("password", passwd);

                fetch("https://projet03-wserveur.alwaysdata.net/app/auth/sign_up.php", {
                    method: "POST",
                    body: formData
                }).then(data => data.text())
                    .then((data) => getSignUpResponse(data, formData))
                    .finally(() => {button_sign_in.disabled = false; button_sign_in.value = "S'inscrire";})
            }


        }
        function getSignUpResponse(msg, signUpInfo) {
            let email = signUpInfo.get("email");
            let status_msg_tag = document.querySelector("#sign-up-status-msg #msg");
            let status_msg_div = document.querySelector("#sign-up-status-msg");

            const status = {
                OK: "OK",
                EMAIL_TAKEN: "This email is already taken.",
                INTERNAL_SERVER_ERROR: "The database did not respond.",
                EMAIL_DID_NOT_SEND: "OK; Email failed to send"
            }
        
            if (msg == status.OK) {
                status_msg_tag.style.backgroundColor = "rgba(88, 252, 96, 1)"
                status_msg_div.style.backgroundColor = "rgba(88, 252, 96, 1)"
                status_msg_tag.innerText = "Un email de confirmation a été envoyé à l'adresse couriel suivante: " + email
                status_msg_div.hidden = false
            }
            else if (msg == status.EMAIL_TAKEN) {
                status_msg_tag.style.backgroundColor = "red"
                status_msg_div.style.backgroundColor = "red"
                status_msg_tag.innerText = `L'adresse courriel '${email}' est déjà prise.`
                status_msg_div.hidden = false
            }
            else if (msg == status.INTERNAL_SERVER_ERROR) {
                status_msg_tag.style.backgroundColor = "red"
                status_msg_div.style.backgroundColor = "red"
                status_msg_tag.innerHTML = `Le serveur n'a pas répondu à la requête. <br> Code d'erreur: 500`
                status_msg_div.hidden = false
            }
            else if (msg == status.EMAIL_DID_NOT_SEND) {
                status_msg_tag.style.backgroundColor = "red"
                status_msg_div.style.backgroundColor = "red"
                status_msg_tag.innerHTML = `Vous avez été inscrit, mais l'envoie du courriel à échoué. <br> Veuillez contacter un administrateur pour confirmer votre adresse courriel.`
                status_msg_div.hidden = false
            }
            console.log(msg)
        }
        function closeStatusMsg() {
            let status_msg_div = document.querySelector("#sign-up-status-msg")
            status_msg_div.hidden = true
        }
    </script>
</body>


</html>