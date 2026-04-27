<?php
require "navbars/navigation_signed_out.php";
require "footers/footer.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://projet03-wserveur.alwaysdata.net/public/css/signup-in.css" rel="stylesheet">
    <title>Mot de passe oublié</title>
</head>

<body>
    <div hidden="true" id="status-msg">
        <span id="msg"></span>
        <button style="padding-left: 20px " onclick="closeStatusMsg()" type="button" class="btn-close"
            aria-label="Close"></button>
    </div>
    <div class="form-group" id="container">
        <label id="header">Mot de passe oublié</label>
        <label>Entrer votre adresse courriel et nous vous enverrons votre mot de passe.</label>
        <input id="email" class="form-control" type="text" placeholder="Adresse courriel">
        <label hidden="true" class="invalid-fields" id="error-msg">Veuillez entrer une adresse courriel</label>
        <input onclick="sendRequest()" id="send-passwd" type="button" class="btn btn-primary"
            value="Envoyer mon mot de passe">
    </div>
    <script>
        let status_msg_div = document.querySelector("#status-msg")
        let status_msg = document.querySelector("#status-msg #msg")
        function sendRequest() {
            let email = document.getElementById("email").value
            let error_msg_tag = document.getElementById("error-msg")
            let btn_send = document.getElementById("send-passwd")

            if (email.trim() == "") {
                error_msg_tag.hidden = false
                return;
            }
            email = email.trim()
            error_msg_tag.hidden = true
            btn_send.value = "En cours d'envoie..."
            btn_send.disabled = true
            status_msg_div.hidden = true

            fetch(`https://projet03-wserveur.alwaysdata.net/app/controller/forgot_password.php?email=${email}`)
                .then((response) => response.text())
                .then((response) => { getRequestResponse(response) })
                .finally(() => { btn_send.value = "Envoyer mon mot de passe"; btn_send.disabled = false; })
        }

        function getRequestResponse(response) {
            const status = {
                OK: "OK",
                EMAIL_NOT_FOUND: "Email was not found.",
                INTERNAL_SERVER_ERROR: "The email was not sent due to an internal server error."
            }

            status_msg_div.hidden = false;
            switch (response) {
                case status.OK:
                    status_msg_div.style.backgroundColor = "rgba(88, 252, 96, 1)"
                    status_msg.innerText = "Votre mot de passe à été envoyé."
                    break;
                case status.EMAIL_NOT_FOUND:
                    status_msg_div.style.backgroundColor = "orange"
                    status_msg.innerText = "Ce courriel n'existe pas dans notre base de donnée."
                    break;
                case status.INTERNAL_SERVER_ERROR:
                    status_msg_div.style.backgroundColor = "red"
                    status_msg.innerHTML = "Le serveur n'a pas répondu à la requête. <br> Code d'erreur: 500"
                    break;
            }
        }
        function closeStatusMsg() {
            status_msg_div.hidden = true
        }
    </script>
</body>

</html>