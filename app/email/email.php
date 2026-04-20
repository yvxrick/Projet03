<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../phpmailer/vendor/autoload.php';

class email
{
    /**
     * @type PHPMailer
     */
    private $mail;
    public function __construct()
    {
        // Server settings
        $this->mail = new PHPMailer(true);
        $this->mail->isSMTP();
        $this->mail->Host = 'smtp.gmail.com';
        $this->mail->SMTPAuth = true;
        $this->mail->Username = 'riratsey@gmail.com';
        $this->mail->Password = 'bjzu lvag tuvf yraj';
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $this->mail->Port = 465;
    }

    /**
     * Envoie un courriel.
     * Le message accepete le format HTML.
     * @param string $to L'adresse courriel du receveur
     * @param string $subjet Le sujet du courriel
     * @param string $message Le message du courriel
     * @return boolean
     */

    public function sendEmail($to, $subject, $message)
    {
        $this->mail->setFrom("riratsey@gmail.com", "Les petites annonces GG");
        $this->mail->addAddress($to);
        $this->mail->isHTML(true);
        $this->mail->Subject = $subject;
        $this->mail->Body = $message;

        try {
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    /**
     * Envoie un message de confirmation de courriel.
     * @param mixed $to
     * @param mixed $user_hash
     * @return mixed Retourne `True` si le email de confirmation à été envoyé, sinon `False`
     */
    public function send_confirmation_email($to, $token)
    {
        $con = Database::Connect();
        $link = sprintf("http://localhost/projet03/public/confirm_email.php?token=%s", $token);

        $this->mail->setFrom("riratsey@gmail.com", "Les petites annonces GG");
        $this->mail->addAddress($to);
        $this->mail->isHTML(true);

        $this->mail->Subject = "Confirmation de courriel";
        $this->mail->Body = "<h2> Confirmation de courriel </h2>
                            Pour confirmer votre adresse courriel <b> $to </b>, veuillez cliquer sur le lien si-dessous: <br>
                            <a href='$link'> Confirmer mon adresse courriel </a>";
        try {
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

}
