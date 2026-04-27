<?php
require "database.php";
class user {
    /**
     * Connection à la base de donnée
     * @var mysqli
     */
    private $con;
    private $email;
    public function __construct($email) {
        $this->con = Database::Connect();
        $this->email = $email;
    }
    /**
     * Retorune un booléan indiquant si l'utilisateur existe dans la DB
     * @return bool
     */
    public function exists() {
        $stmt = $this->con->prepare("SELECT NoUtilisateur FROM utilisateurs WHERE Courriel = ?");
        $stmt->bind_param("s", $this->email);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }
    /**
     * Retourne le mot de passe d'un utilisateur.
     * @return string
     */
    public function get_passwd() {
        $password = null;
        if ($this->exists()) {
            $stmt = $this->con->prepare("SELECT MotDePasse FROM utilisateurs WHERE Courriel = ?");
            $stmt->bind_param("s", $this->email);
            $stmt->execute();
            $stmt->bind_result($password);
            $stmt->fetch();
            return $password;
        }
        return null;
    }
    /**
     * Retourne l'adresse courriel d'un utilisateur
     * @return string
     */
    public function get_email() {
        return $this->email;
    }
}