<?php
require_once "database.php";
/**
 * Classe utilitaires pour gérer tout ce qui touche les utilisateurs.
 */
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
     * Mets à jour le mot de passe d'un utilisateur.
     * @return void
     */
    public function set_passwd($new_passwd) {
        $query = "UPDATE utilisateurs SET MotDePasse = ? WHERE Courriel = ?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param("ss", $new_passwd, $this->email);
        $stmt->execute();
    }
    /**
     * Retourne l'adresse courriel d'un utilisateur
     * @return string
     */
    public function get_email() {
        return $this->email;
    }

    /**
     * Retourne le ID de l'utilisateur
     * @return int
     */
    public function get_id() {
        if ($this->exists()) {
            $email = $this->email;
            return intval($this->con->query("SELECT NoUtilisateur FROM utilisateurs WHERE Courriel = '$email'")->fetch_row()[0]);
        }
        return false;
    }

    /**
     * Retorune le statut de l'utilisateur.
     */
    public function get_statut() {
        if ($this->exists()) {
            return intval($this->con->query(sprintf("SELECT Statut FROM utilisateurs WHERE Courriel = '%s'", $this->email))->fetch_row()[0]);
        }
        return false;
    }
    
    /**
     * Retorune le numéro d'employé de l'utilisateur.
     */
    public function get_no_employe() {
        if ($this->exists()) {
            return $this->con->query(sprintf("SELECT NoEmpl FROM utilisateurs WHERE Courriel = '%s'", $this->email))->fetch_row()[0];
        }
        return false;
    }

    /**
     * Retorune le nom de l'utilisateur.
     */
    public function get_nom() {
        if ($this->exists()) {
            return $this->con->query(sprintf("SELECT Nom FROM utilisateurs WHERE Courriel = '%s'", $this->email))->fetch_row()[0];
        }
        return false;
    }

    /**
     * Retourne le prénom de l'utilisateur.
     */
    public function get_prenom() {
        if ($this->exists()) {
            return $this->con->query(sprintf("SELECT Prenom FROM utilisateurs WHERE Courriel = '%s'", $this->email))->fetch_row()[0];
        }
        return false;
    }

    /**
     * Retorune le numéro de maison de l'utilisateur.
     */
    public function get_tel_maison() {
        if ($this->exists()) {
            $num = $this->con->query(sprintf("SELECT NoTelMaison FROM utilisateurs WHERE Courriel = '%s'", $this->email))->fetch_row()[0];
            if ($num == null) {return;}
            return substr($num, 0, -1);
        }
        return false;
    }

    /**
     * Retorune le numéro de travail de l'utilisateur.
     */
    public function get_tel_travail() {
        if ($this->exists()) {
            $num = $this->con->query(sprintf("SELECT NoTelTravail FROM utilisateurs WHERE Courriel = '%s'", $this->email))->fetch_row()[0];
            if ($num == null) {return;}
            return substr($num, 0, -1);
        }
        return false;
    }
    /**
     * Retorune le numéro de cellulaire de l'utilisateur.
     */
    public function get_tel_cellulaire() {
        if ($this->exists()) {
            $num = $this->con->query(sprintf("SELECT NoTelCellulaire FROM utilisateurs WHERE Courriel = '%s'", $this->email))->fetch_row()[0];
            if ($num == null) {return;}
            return substr($num, 0, -1);
        }
        return false;
    }

    public function set_statut($value) {
        if ($this->exists()) {
            $this->con->query(sprintf("UPDATE utilisateurs SET Statut = '%s' WHERE Courriel = '%s'", $value, $this->email));
            return true;
        }
        return false;
    }

    public function set_no_empl($value) {
        if ($this->exists()) {
            $this->con->query(sprintf("UPDATE utilisateurs SET NoEmpl = '%s' WHERE Courriel = '%s'", $value, $this->email));
            return true;
        }
        return false;
    }

    public function set_nom($value) {
        if ($this->exists()) {
            $this->con->query(sprintf("UPDATE utilisateurs SET Nom = '%s' WHERE Courriel = '%s'", $value, $this->email));
            return true;
        }
        return false;
    }

    public function set_prenom($value) {
        if ($this->exists()) {
            $this->con->query(sprintf("UPDATE utilisateurs SET Prenom = '%s' WHERE Courriel = '%s'", $value, $this->email));
            return true;
        }
        return false;
    }

    public function set_tel_maison($value, $public) {
        if ($this->exists()) {
            $value = preg_replace("/[PN]$/", "", $value);
            $public != null ? $value .= "P" : $value .= "N";
            $this->con->query(sprintf("UPDATE utilisateurs SET NoTelMaison = '%s' WHERE Courriel = '%s'", $value, $this->email));
            return true;
        }
        return false;
    }

    public function set_tel_travail($value, $public) {
        if ($this->exists()) {
            $value = preg_replace("/[PN]$/", "", $value);
            $public != null ? $value .= "P" : $value .= "N";
            $this->con->query(sprintf("UPDATE utilisateurs SET NoTelTravail = '%s' WHERE Courriel = '%s'", $value, $this->email));
            return true;
        }
        return false;
    }

    public function set_tel_cellulaire($value, $public) {
        if ($this->exists()) {
            $value = preg_replace("/[PN]$/", "", $value);
            $public != null ? $value .= "P" : $value .= "N";
            $this->con->query(sprintf("UPDATE utilisateurs SET NoTelCellulaire = '%s' WHERE Courriel = '%s'", $value, $this->email));
            return true;
        }
        return false;
    }
    /**
     * Retourne un booléan indiquant si l'utilisateur est authentifé (à vérifié son courriel)
     * @return bool
     */
    public function is_authenticated() {
        if ($this->exists()) {
            $status = intval($this->con->query(sprintf("SELECT statut FROM utilisateurs WHERE Courriel = '%s'", $this->email))->fetch_row()[0]);
            return $status !== 0;
        }
        return false;
    }
    /**
     * Incrémente le nombre de connexions de l'utilisateur et inscrit sa date et heure de connexion dans la table connexions.
     * @return void
     */
    public function add_connection() {
        if ($this->exists()) {
            $query_utilisateurs = sprintf("UPDATE utilisateurs SET NbConnexions = NbConnexions + 1 WHERE NoUtilisateur = %d", $this->get_id());
            $query_connexions_normal = sprintf("UPDATE connexions SET Connexion = NOW() WHERE NoUtilisateur = %d", $this->get_id());
            $query_connexions_first_con = sprintf("INSERT INTO connexions(NoUtilisateur, Connexion) VALUES(%d, NOW())", $this->get_id());

            $this->con->query($query_utilisateurs); // Toujours la meme
            if ($this->first_connection()) {
                $this->con->query($query_connexions_first_con);
            } else {
                $this->con->query($query_connexions_normal);
            }
        }
    }

    /**
     * Retorune un booléan indiquant si c'est la première connexion de l'utilisateur.
     * @return bool
     */
    public function first_connection() {
        $query = sprintf("SELECT * FROM connexions WHERE NoUtilisateur = %d", $this->get_id());
        $this->con->query($query);
        return $this->con->affected_rows < 1;
    }

    /**
     * Retorune un booléan indiquant si l'utilisateur à entré son profile de première connexion.
     * @return bool
     */
    public function has_filled_profile() {
        $query = sprintf("SELECT Nom, Prenom FROM utilisateurs WHERE Courriel = '%s'", $this->email);
        if ($this->exists()) {
            $result = $this->con->query($query)->fetch_assoc();
            $fname = $result["Nom"];
            $lname = $result["Prenom"];

            $case_1 = $fname !== NULL && $lname !== NULL;
            $case_2 = $fname !== "" && $lname !== "";
            return $case_1 == true && $case_2 == true;
        }
        return false;
    }

    /**
     * Ajoute le temps de deconnexion dans la table connexions.
     * @return bool
     */
    public function add_disconnection() {
        if ($this->exists()) {
            $query = sprintf("UPDATE connexions SET Deconnexion = NOW() WHERE NoUtilisateur = %d", $this->get_id());
            $this->con->query($query);
            return true;
        }
        return false;
    }

    /**
     * Modifie dans la table `utilisateurs` la colonne `Modification` de l'utilisateur.
     * @return bool
     */
    public function add_profile_change() {
        if ($this->exists()) {
            $query = sprintf("UPDATE utilisateurs SET Modification = NOW() WHERE Courriel = '%s'", $this->email);
            $this->con->query($query);
            return true;
        }
        return false;
    }

    /**
     * Crée un nouvel utilisateur.
     * @param mixed $email
     * @param mixed $password
     * @param mixed $email_to_validate Le staut de 0, qui indique que l'utilisateur doit vérifier son courriel
     * @param mixed $user_hash Hash unique pour chaque utilisateur.
     * @param mixed $zero La valeur 0.
     * @return bool
     */
    public function add_new_user($email, $password, $email_to_validate, $user_hash, $zero) {
        if (!($this->exists())) {
            $query = "INSERT INTO utilisateurs(Courriel, MotDePasse, Statut, AutresInfos, NbConnexions, Creation) VALUES (?, ?, ?, ?, ?, NOW())";
            $stmt = $this->con->prepare($query);
            $stmt->bind_param("ssisi", $email, $password, $email_to_validate, $user_hash, $zero);
            $stmt->execute();
            return true;
        }
        return false;
    }

    /**
     * Retorune la visibilité de numéro de maison de l'utilisateur.
     * @return string|void
     */
    public function get_house_number_visibility() {
        $query = sprintf("SELECT NoTelMaison FROM utilisateurs WHERE Courriel = '%s'", $this->email);
        $num = $this->con->query($query)->fetch_row()[0];
        if ($num == null) {return;}
        return substr($num, -1);
    }

    /**
     * Retorune la visibilité de numéro de travail de l'utilisateur.
     * @return string|void
     */
    public function get_work_number_visibility() {
        $query = sprintf("SELECT NoTelTravail FROM utilisateurs WHERE Courriel = '%s'", $this->email);
        $num = $this->con->query($query)->fetch_row()[0];
        if ($num == null) {return;}
        return substr($num, -1);
    }

    /**
     * Retorune la visibilité de numéro de cellulaire de l'utilisateur.
     * @return string|void
     */
    public function get_phone_number_visibility() {
        $query = sprintf("SELECT NoTelCellulaire FROM utilisateurs WHERE Courriel = '%s'", $this->email);
        $num = $this->con->query($query)->fetch_row()[0];
        if ($num == null) {return;}
        return substr($num, -1);
    }

}