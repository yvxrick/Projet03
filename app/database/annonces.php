<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "app/database/database.php";
/**
 * Classe utilitaires pour gérer tout ce qui touche les annonces.
 */
class annonces
{
    private mysqli $con;
    public function __construct()
    {
        $this->con = Database::Connect();
    }

    /**
     * Retorune un array contenant toutes les annonces avec une limite et offset.
     * @return array
     */
    public function get_all_cards_ads($limit, $offset)
    {
        if ($limit > 20) {
            return [];
        }
        return $this->con->query("SELECT a.NoAnnonce, a.DescriptionAbregee, a.Prix, a.Photo, a.Etat, u.Nom, u.Prenom, a.Parution ,c.Description AS Categorie FROM annonces a JOIN utilisateurs u ON a.NoUtilisateur = u.NoUtilisateur JOIN categories c ON a.Categorie = c.NoCategorie LIMIT $limit OFFSET $offset;")->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Retourune le HTML pour les cartes de toutes les annonces passées en paramètre.
     * @param $array_ads Array contenant les annonces
     */
    public function load_cards_ads_html($ads)
    {
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

    public function load_all_cards_users_ads($ads)
    {
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
    public function is_users_ad($users_id, $ad_id)
    {
        $response_id = $this->con->query("SELECT NoUtilisateur FROM annonces WHERE NoAnnonce = '$ad_id'")->fetch_row();
        if ($response_id == null) {
            return false;
        }
        return intval($response_id[0]) === $users_id;
    }


    /**
     * Retorune le nombre d'annonces total qui sont actives.
     * @return int
     */
    public function get_number_of_ads_active()
    {
        return intval($this->con->query("SELECT COUNT(*) FROM annonces WHERE Etat = 1")->fetch_row()[0]);
    }

    /**
     * Retorune un array associative de l'annonce et de son auteur.
     * @param $id NoAnnonce
     * @return array
     */
    public function get_ad($id)
    {
        return $this->con->query("SELECT * FROM annonces a RIGHT JOIN utilisateurs u ON u.NoUtilisateur = a.NoUtilisateur RIGHT JOIN categories c ON c.NoCategorie = a.Categorie WHERE NoAnnonce = '$id'")->fetch_assoc();
    }

    public function add_ad($ad_title, $ad_desc, $ad_category, $ad_price, $ad_photo, $ad_state, $noUtilisateur)
    {
        $query = "INSERT INTO annonces(NoUtilisateur, Parution, Categorie, DescriptionAbregee, DescriptionComplete, Prix, Photo, Etat, MiseAJour) VALUES (?, NOW(), ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param("sssssss", $noUtilisateur, $ad_category, $ad_title, $ad_desc, $ad_price, $ad_photo, $ad_state);
        $stmt->execute();
        return true;
    }

    public function get_all_users_add($user_id, $offset)
    {
        return $this->con->query("SELECT a.NoAnnonce, a.DescriptionAbregee, a.Prix, a.Photo, a.Etat, u.Nom, u.Prenom, a.Parution ,c.Description AS Categorie FROM annonces a JOIN utilisateurs u ON a.NoUtilisateur = u.NoUtilisateur JOIN categories c ON a.Categorie = c.NoCategorie WHERE a.NoUtilisateur = '$user_id' ORDER BY a.Parution ASC LIMIT 10 OFFSET $offset")->fetch_all(MYSQLI_ASSOC);
    }

    public function get_number_all_ads_users($user_id)
    {
        return intval($this->con->query("SELECT COUNT(*) FROM annonces a WHERE a.NoUtilisateur = '$user_id'")->fetch_row()[0]);
    }

    public function modify_ad($ad_id, $ad_title, $ad_desc, $ad_category, $ad_price, $ad_photo, $ad_state, $keep)
    {
        if ($keep) {
            $query = "UPDATE annonces SET Categorie = ?, DescriptionAbregee = ?, DescriptionComplete = ?, Prix = ?, MiseAJour = NOW(), Etat = ? WHERE NoAnnonce = ?";
            $stmt = $this->con->prepare($query);
            $stmt->bind_param("issiii", $ad_category, $ad_title, $ad_desc, $ad_price, $ad_state, $ad_id);
            $stmt->execute();
            return true;
        }
        if ($ad_photo == null) {
            return false;
        }
        $query = "UPDATE annonces SET Categorie = ?, DescriptionAbregee = ?, DescriptionComplete = ?, Prix = ?, MiseAJour = NOW(), Etat = ?, Photo = ? WHERE NoAnnonce = ?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param("issiisi", $ad_category, $ad_title, $ad_desc, $ad_price, $ad_state, $ad_photo, $ad_id);
        $stmt->execute();
        return true;
    }

    /**
     * Mets l'état d'une annonce à retiré.
     * @param mixed $ad_id
     * @return boolean
     */
    public function remove_ad($ad_id)
    {
        $query = "UPDATE annonces SET Etat = 3 WHERE NoAnnonce = ?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param("i", $ad_id);
        $stmt->execute();
        return true;
    }
    /**
     * Applique les tris sur les annonces. Chaque paramètre doit être un array, sinon la fonction retourne une liste vide.
     * La fonction retourne les annonces basées sur le filtre et le nombre d'annonces totales affectées dans une liste 2D. <br>
     * 0 => Les annonces <br>
     * 1 => Le nombre de lignes affectées (nombres d'annonces totales)
     */
    public function set_ads_sort($sort, $ddp_motor, $author_name_motor, $category_motor, $description_motor, $limit, $offset)
    {
        $query = "SELECT a.NoAnnonce, a.DescriptionAbregee, a.Prix, a.Photo, a.Etat, u.Nom, u.Prenom, a.Parution ,c.Description AS Categorie, a.DescriptionComplete FROM annonces a JOIN utilisateurs u ON a.NoUtilisateur = u.NoUtilisateur JOIN categories c ON a.Categorie = c.NoCategorie ";
        $num_rows_query = "SELECT COUNT(*) FROM annonces a LEFT JOIN utilisateurs u ON a.NoUtilisateur = u.NoUtilisateur LEFT JOIN categories c ON a.Categorie = c.NoCategorie ";
        // Regarde si chaque paramètre est un array
        if (!is_array($sort) || !is_array($ddp_motor) || !is_array($author_name_motor) || !is_array($category_motor) || !is_array($description_motor) || count($sort) > 2) {
            return [[], []];
        }

        // tri
        $selected_sort = $sort[0];
        $sort_order = $sort[1];

        // moteur de recherche
        $isset_ddp_motor = $ddp_motor[0] != null && $ddp_motor[1] != null;
        $isset_author_name_motor = $author_name_motor[0] != null;
        $isset_category_motor = $category_motor[0] != null;
        $isset_description_motor = $description_motor[0] != null;
        $has_search_motor = $isset_ddp_motor != null || $isset_author_name_motor != null || $isset_category_motor != null || $isset_description_motor != null;

        if ($has_search_motor) {
            $conditions = ["Etat = 1"];

            if ($isset_ddp_motor) {
                $conditions[] = "a.Parution BETWEEN '$ddp_motor[0]' AND '$ddp_motor[1]'";
            }

            if ($isset_author_name_motor) {
                $conditions[] = "u.Prenom LIKE '%$author_name_motor[0]%'";
            }

            if ($isset_category_motor) {
                $conditions[] = "c.Description LIKE '%$category_motor[0]%'";
            }

            if ($isset_description_motor) {
                $conditions[] = "a.DescriptionComplete LIKE '%$description_motor[0]%'";
            }

            if (!empty($conditions)) {
                $query .= " WHERE " . implode(" AND ", $conditions);
                $num_rows_query .= " WHERE " . implode(" AND ", $conditions);
            }
        } else {
            $query .= "WHERE Etat = 1 ";
            $num_rows_query .= "WHERE Etat = 1";
        }

        switch ($selected_sort) {
            case "date_paru": // date de parution
                $sort_order == "asc" ? $query .= "ORDER BY a.Parution ASC" : $query .= "ORDER BY a.Parution DESC";
                break;
            case "lname": // nom de famille
                $sort_order == "asc" ? $query .= "ORDER BY u.Nom ASC" : $query .= "ORDER BY u.NOM DESC";
                break;
            case "fname": // prenom
                $sort_order == "asc" ? $query .= "ORDER BY u.Prenom ASC" : $query .= "ORDER BY u.Prenom DESC";
                break;
            case "categorie": // categorie
                $sort_order == "asc" ? $query .= "ORDER BY Categorie ASC" : $query .= "ORDER BY Categorie DESC";
                break;
        }
        $query .= " LIMIT $limit OFFSET $offset;";
        $num_rows_affected = intval($this->con->query($num_rows_query)->fetch_row()[0]);
        if ($num_rows_affected <= 0) $num_rows_affected = 1;
        return [$this->con->query($query)->fetch_all(MYSQLI_ASSOC), $num_rows_affected];
    }

    /**
     * Applique les tris sur les annonces d'un utilisateur. <br>
     * 0 => Les annonces <br>
     * 1 => Le nombre de lignes affectées (nombres d'annonces totales)
     * @param mixed $user_id
     * @param mixed $sort
     * @param mixed $offset
     * @return array<array|int>
     */
    public function set_ads_sort_user($user_id, $sort, $offset) {
        $query = "SELECT a.NoAnnonce, a.DescriptionAbregee, a.Prix, a.Photo, a.Etat, u.Nom, u.Prenom, a.Parution ,c.Description AS Categorie FROM annonces a JOIN utilisateurs u ON a.NoUtilisateur = u.NoUtilisateur JOIN categories c ON a.Categorie = c.NoCategorie WHERE a.NoUtilisateur = '$user_id' ";
        $num_rows_query = "SELECT COUNT(*) FROM annonces a JOIN utilisateurs u ON a.NoUtilisateur = u.NoUtilisateur JOIN categories c ON a.Categorie = c.NoCategorie WHERE a.NoUtilisateur = '$user_id'";
        $selected_sort = $sort[0];
        $sort_order = $sort[1];

        switch ($selected_sort) {
            case "date_paru": // date de parution
                $sort_order == "asc" ? $query .= "ORDER BY a.Parution ASC" : $query .= "ORDER BY a.Parution DESC";
                break;
            case "desc": // description
                $sort_order == "asc" ? $query .= "ORDER BY a.DescriptionAbregee ASC" : $query .= "ORDER BY a.DescriptionAbregee DESC";
                break;
            case "etat": // état
                $sort_order == "asc" ? $query .= "ORDER BY a.Etat ASC" : $query .= "ORDER BY a.Etat DESC";
                break;
            case "categorie": // categorie
                $sort_order == "asc" ? $query .= "ORDER BY Categorie ASC" : $query .= "ORDER BY Categorie DESC";
                break;
        }
        $query .= " LIMIT 10 OFFSET $offset";
        $num_rows_affected = intval($this->con->query($num_rows_query)->fetch_row()[0]);
        if ($num_rows_affected <= 0) $num_rows_affected = 1;
        return [$this->con->query($query)->fetch_all(MYSQLI_ASSOC), $num_rows_affected];
    }
}