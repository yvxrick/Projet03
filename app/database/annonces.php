<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "app/database/database.php";
/**
 * Classe utilitaires pour gérer tout ce qui touche les annonces.
 */
class annonces {
    private mysqli $con;
    public function __construct() {
        $this->con = Database::Connect();
    }

    /**
     * Retorune un array contenant toutes les annonces avec une limite et offset.
     * @return array
     */
    public function get_all_cards_ads($limit, $offset) {
        if ($limit > 20) {return [];}
        return $this->con->query("SELECT a.NoAnnonce, a.DescriptionAbregee, a.Prix, a.Photo, a.Etat, u.Nom, u.Prenom, a.Parution ,c.Description AS Categorie FROM annonces a JOIN utilisateurs u ON a.NoUtilisateur = u.NoUtilisateur JOIN categories c ON a.Categorie = c.NoCategorie LIMIT $limit OFFSET $offset;")->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Retourune le HTML pour les cartes de toutes les annonces passées en paramètre.
     * @param $array_ads Array contenant les annonces
     */
    public function load_cards_ads_html($ads) {
    $HTML = "";

    foreach ($ads as $ad) {
        $img = !empty($ad['Photo']) 
            ? $ad['Photo'] 
            : "placeholder.jpg";

        $HTML .= "<div class='ad-card' onclick='window.location.href = `https://projet03-wserveur.alwaysdata.net/private/view_ad.php?id={$ad['NoAnnonce']}`'>";
        $HTML .= "<img src='https://projet03-wserveur.alwaysdata.net/private/ads-images/$img'>";
        $HTML .= "<p class='ad-title'>{$ad['DescriptionAbregee']}</p>";
        $HTML .= "<p class='ad-author'>{$ad['Prenom']} {$ad['Nom']}</p>";
        $HTML .= "<p class='ad-category'>{$ad['Categorie']}</p>";
        $HTML .= "<p class='ad-price'>" . number_format($ad['Prix'], 2) . " $</p>";
        $HTML .= "<p class='ad-author'>ID: {$ad['NoAnnonce']}</p>";
        $HTML .= "<p class='ad-author'>Paru le: {$ad['Parution']}</p>";
        $HTML .= "</div>";
    }
        return $HTML;
    }

    public function load_all_cards_users_ads($ads) {
        $HTML = "";
        $etats = ["1" => "Actif", "2" => "Inactif", "3" => "Retiré"];
        foreach ($ads as $ad) {
            $img = !empty($ad['Photo']) 
            ? $ad['Photo'] 
            : "placeholder.jpg";
            
            $HTML .= "<div class='ad-card' href=`https://projet03-wserveur.alwaysdata.net/private/my_ad.php?id={$ad['NoAnnonce']}`>";
            $HTML .= "<img src='https://projet03-wserveur.alwaysdata.net/private/ads-images/$img'>";
            $HTML .= "<p class='ad-title'>{$ad['DescriptionAbregee']}</p>";
            $HTML .= "<p class='ad-author'>{$ad['Prenom']} {$ad['Nom']}</p>";
            $HTML .= "<p class='ad-category'>{$ad['Categorie']}</p>";
            $HTML .= "<p class='ad-price'>" . number_format($ad['Prix'], 2) . " $</p>";
            $HTML .= "<p class='ad-author'>ID: {$ad['NoAnnonce']}</p>";
            $HTML .= "<p class='ad-author'>Paru le: {$ad['Parution']}</p>";
            $HTML .= "<p class='ad-title'>Etat: {$etats[$ad['Etat']]}"; 
            $HTML .= "</div>";
        }   
            return $HTML;
        }
    /**
     * Évalue si l'annonce demandé appartient à un utilisateur spécifique.
     * @param mixed $users_id
     * @param mixed $ad_id
     * @return bool `True` si l'annonce appartient à l'utilisateur, `False` sinon.
     */
    public function is_users_ad($users_id, $ad_id) {
        $response_id = $this->con->query("SELECT NoUtilisateur FROM annonces WHERE NoAnnonce = '$ad_id'")->fetch_row();
        if ($response_id == null) {return false;}
        return intval($response_id[0]) === $users_id;
    }
    
    
    /**
     * Retorune le nombre d'annonces total qui sont actives.
     * @return int
     */
    public function get_number_of_ads_active() {
        return intval($this->con->query("SELECT COUNT(*) FROM annonces WHERE Etat = 1")->fetch_row()[0]);
    }

    /**
     * Retorune un array associative de l'annonce et de son auteur.
     * @param $id NoAnnonce
     * @return array
     */
    public function get_ad($id) {
        return $this->con->query("SELECT * FROM annonces a RIGHT JOIN utilisateurs u ON u.NoUtilisateur = a.NoUtilisateur RIGHT JOIN categories c ON c.NoCategorie = a.Categorie WHERE NoAnnonce = '$id'")->fetch_assoc();
    }

    public function add_ad($ad_title, $ad_desc, $ad_category, $ad_price, $ad_photo, $ad_state, $noUtilisateur) {
        $query = "INSERT INTO annonces(NoUtilisateur, Parution, Categorie, DescriptionAbregee, DescriptionComplete, Prix, Photo, Etat, MiseAJour) VALUES (?, NOW(), ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param("sssssss", $noUtilisateur, $ad_category, $ad_title, $ad_desc, $ad_price, $ad_photo, $ad_state);
        $stmt->execute();
        return true;
    }

    public function get_all_users_add($user_id, $offset) {
        return $this->con->query("SELECT a.NoAnnonce, a.DescriptionAbregee, a.Prix, a.Photo, a.Etat, u.Nom, u.Prenom, a.Parution ,c.Description AS Categorie FROM annonces a JOIN utilisateurs u ON a.NoUtilisateur = u.NoUtilisateur JOIN categories c ON a.Categorie = c.NoCategorie WHERE a.NoUtilisateur = '$user_id' ORDER BY a.Parution ASC LIMIT 10 OFFSET $offset")->fetch_all(MYSQLI_ASSOC);
    }

    public function get_number_all_ads_users($user_id) {
        return intval($this->con->query("SELECT COUNT(*) FROM annonces a WHERE a.NoUtilisateur = '$user_id'")->fetch_row()[0]);
    }

    public function modify_ad($ad_id, $ad_title, $ad_desc, $ad_category, $ad_price, $ad_photo, $ad_state, $keep) {
        if ($keep) {
            $query = "UPDATE annonces SET Categorie = ?, DescriptionAbregee = ?, DescriptionComplete = ?, Prix = ?, MiseAJour = NOW(), Etat = ? WHERE NoAnnonce = ?";
            $stmt = $this->con->prepare($query);
            $stmt->bind_param("issiii", $ad_category, $ad_title, $ad_desc, $ad_price, $ad_state, $ad_id);
            $stmt->execute();
            return true;
        }
        if ($ad_photo == null) {return false;}
        $query = "UPDATE annonces SET Categorie = ?, DescriptionAbregee = ?, DescriptionComplete = ?, Prix = ?, MiseAJour = NOW(), Etat = ?, Photo = ? WHERE NoAnnonce = ?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param("issiisi",$ad_category, $ad_title, $ad_desc, $ad_price, $ad_state, $ad_photo, $ad_id);
        $stmt->execute();
        return true;
    }

    /**
     * Mets l'état d'une annonce à retiré.
     * @param mixed $ad_id
     * @return boolean
     */
    public function remove_ad($ad_id) {
        $query = "UPDATE annonces SET Etat = 3 WHERE NoAnnonce = ?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param("i", $ad_id);
        $stmt->execute();
        return true;
    }








    /**
     * SECTION TRI
     */


    /**
     * Arrange les annonces par leur dates de parutions inverse chronologique.
     * @return array Retorune une nouvelle `array` contenant le tri.
     */
    public function sortByDDP_DESC($limit, $offset) {
        return $this->con->query("SELECT a.NoAnnonce, a.DescriptionAbregee, a.Prix, a.Photo, a.Etat, u.Nom, u.Prenom, a.Parution ,c.Description AS Categorie FROM annonces a JOIN utilisateurs u ON a.NoUtilisateur = u.NoUtilisateur JOIN categories c ON a.Categorie = c.NoCategorie WHERE Etat = 1 ORDER BY a.Parution DESC LIMIT $limit OFFSET $offset;")->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Arrange les annonces par leur dates de parutions chronologique.
     * @return array Retorune une nouvelle `array` contenant le tri.
     */
    public function sortByDDP_ASC($limit, $offset) {
        return $this->con->query("SELECT a.NoAnnonce, a.DescriptionAbregee, a.Prix, a.Photo, a.Etat, u.Nom, u.Prenom, a.Parution ,c.Description AS Categorie FROM annonces a JOIN utilisateurs u ON a.NoUtilisateur = u.NoUtilisateur JOIN categories c ON a.Categorie = c.NoCategorie WHERE Etat = 1 ORDER BY a.Parution ASC LIMIT $limit OFFSET $offset;")->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Arrange les annonces par le nom et prénom de l'auteur.
     * @return array Retorune une nouvelle `array` contenant le tri.
     */
    public function sortByLNAME_ASC($limit, $offset) {
        return $this->con->query("SELECT a.NoAnnonce, a.DescriptionAbregee, a.Prix, a.Photo, a.Etat, u.Nom, u.Prenom, a.Parution ,c.Description AS Categorie FROM annonces a JOIN utilisateurs u ON a.NoUtilisateur = u.NoUtilisateur JOIN categories c ON a.Categorie = c.NoCategorie WHERE Etat = 1 ORDER BY u.Nom ASC LIMIT $limit OFFSET $offset;")->fetch_all(MYSQLI_ASSOC);
    }

    public function sortByLNAME_DESC($limit, $offset) {
        return $this->con->query("SELECT a.NoAnnonce, a.DescriptionAbregee, a.Prix, a.Photo, a.Etat, u.Nom, u.Prenom, a.Parution ,c.Description AS Categorie FROM annonces a JOIN utilisateurs u ON a.NoUtilisateur = u.NoUtilisateur JOIN categories c ON a.Categorie = c.NoCategorie WHERE Etat = 1 ORDER BY u.Nom DESC LIMIT $limit OFFSET $offset;")->fetch_all(MYSQLI_ASSOC);
    }

    public function sortByFNAME_ASC($limit, $offset) {
        return $this->con->query("SELECT a.NoAnnonce, a.DescriptionAbregee, a.Prix, a.Photo, a.Etat, u.Nom, u.Prenom, a.Parution ,c.Description AS Categorie FROM annonces a JOIN utilisateurs u ON a.NoUtilisateur = u.NoUtilisateur JOIN categories c ON a.Categorie = c.NoCategorie WHERE Etat = 1 ORDER BY u.Prenom ASC LIMIT $limit OFFSET $offset;")->fetch_all(MYSQLI_ASSOC);
    }

    public function sortByFNAME_DESC($limit, $offset) {
        return $this->con->query("SELECT a.NoAnnonce, a.DescriptionAbregee, a.Prix, a.Photo, a.Etat, u.Nom, u.Prenom, a.Parution ,c.Description AS Categorie FROM annonces a JOIN utilisateurs u ON a.NoUtilisateur = u.NoUtilisateur JOIN categories c ON a.Categorie = c.NoCategorie WHERE Etat = 1 ORDER BY u.Prenom DESC LIMIT $limit OFFSET $offset;")->fetch_all(MYSQLI_ASSOC);
    }

    public function sortByCategorie_ASC($limit, $offset) {
        return $this->con->query("SELECT a.NoAnnonce, a.DescriptionAbregee, a.Prix, a.Photo, a.Etat, u.Nom, u.Prenom, a.Parution ,c.Description AS Categorie FROM annonces a JOIN utilisateurs u ON a.NoUtilisateur = u.NoUtilisateur JOIN categories c ON a.Categorie = c.NoCategorie WHERE Etat = 1 ORDER BY c.Description ASC LIMIT $limit OFFSET $offset;")->fetch_all(MYSQLI_ASSOC);
    }
    public function sortByCategorie_DESC($limit, $offset) {
        return $this->con->query("SELECT a.NoAnnonce, a.DescriptionAbregee, a.Prix, a.Photo, a.Etat, u.Nom, u.Prenom, a.Parution ,c.Description AS Categorie FROM annonces a JOIN utilisateurs u ON a.NoUtilisateur = u.NoUtilisateur JOIN categories c ON a.Categorie = c.NoCategorie WHERE Etat = 1 ORDER BY c.Description DESC LIMIT $limit OFFSET $offset;")->fetch_all(MYSQLI_ASSOC);
    }

    public function sortByTimePeriod($date_begin, $date_end, $limit, $offset) {
        return $this->con->query("SELECT a.NoAnnonce, a.DescriptionAbregee, a.Prix, a.Photo, a.Etat, u.Nom, u.Prenom, a.Parution ,c.Description AS Categorie FROM annonces a JOIN utilisateurs u ON a.NoUtilisateur = u.NoUtilisateur JOIN categories c ON a.Categorie = c.NoCategorie WHERE a.Parution AND Etat = 1 BETWEEN '$date_begin' AND '$date_end' LIMIT $limit OFFSET $offset;")->fetch_all(MYSQLI_ASSOC);
    }

    public function sortByAuthorName($author_name, $limit, $offset) {
        return $this->con->query("SELECT a.NoAnnonce, a.DescriptionAbregee, a.Prix, a.Photo, a.Etat, u.Nom, u.Prenom, a.Parution ,c.Description AS Categorie FROM annonces a JOIN utilisateurs u ON a.NoUtilisateur = u.NoUtilisateur JOIN categories c ON a.Categorie = c.NoCategorie WHERE u.Nom AND Etat = 1 LIKE '$author_name' OR u.Prenom LIKE '$author_name' LIMIT $limit OFFSET $offset;")->fetch_all(MYSQLI_ASSOC);
    }

}