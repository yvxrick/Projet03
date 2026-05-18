<?php
require_once "database.php";
/**
 * Classe utilitaires pour gérer tout ce qui touche les catégories.
 */
class categories {
    private mysqli $con;
    public function __construct() {
        $this->con = Database::Connect();
    }

    /**
     * Retorune l'ensemble des catégories.
     * @return array
     */
    public function get_all_categories() {
        return $this->con->query("SELECT * FROM categories")->fetch_all(MYSQLI_ASSOC);
    }

    public function make_categories_list($no_categorie = false) {
        $categories = $this->get_all_categories();
        $HTML = "<select required id='ad-categorie' name='ad-categorie' class='form-select'>";
        foreach ($categories as $row) {
            if (intval($row["NoCategorie"]) == $no_categorie) {
                $HTML .= "<option selected value='{$row["NoCategorie"]}'> {$row["Description"]} </option>";
            } else {
                $HTML .= "<option value='{$row["NoCategorie"]}'> {$row["Description"]} </option>";
            }
        }
        $HTML .= "</select>";
        return $HTML; 
    }
    
}