<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "app/database/database.php";
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
        if (intval($ad['Etat']) !== 1) continue; // skip les annonces non actives

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








    /**
     * SECTION TRI
     */


    /**
     * Arrange les annonces par leur dates de parutions inverse chronologique.
     * @param mixed $ads
     * @return array Retorune une nouvelle `array` contenant le tri.
     */
    public function sortByDDP_DESC($limit, $offset) {
        $ads =  $this->con->query("SELECT a.NoAnnonce, a.DescriptionAbregee, a.Prix, a.Photo, a.Etat, u.Nom, u.Prenom, a.Parution ,c.Description AS Categorie FROM annonces a JOIN utilisateurs u ON a.NoUtilisateur = u.NoUtilisateur JOIN categories c ON a.Categorie = c.NoCategorie ORDER BY a.Parution DESC LIMIT $limit OFFSET $offset;")->fetch_all(MYSQLI_ASSOC);
        return $ads;
    }

    /**
     * Arrange les annonces par leur dates de parutions chronologique.
     * @param mixed $ads
     * @return array Retorune une nouvelle `array` contenant le tri.
     */
    public function sortByDDP_ASC($limit, $offset) {
        $ads =  $this->con->query("SELECT a.NoAnnonce, a.DescriptionAbregee, a.Prix, a.Photo, a.Etat, u.Nom, u.Prenom, a.Parution ,c.Description AS Categorie FROM annonces a JOIN utilisateurs u ON a.NoUtilisateur = u.NoUtilisateur JOIN categories c ON a.Categorie = c.NoCategorie ORDER BY a.Parution ASC LIMIT $limit OFFSET $offset;")->fetch_all(MYSQLI_ASSOC);
        return $ads;
    }
}