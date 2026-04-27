<?php
require_once "database.php";
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

    public function make_categories_list() {
        $categories = $this->get_all_categories();
        $HTML = "<select required name='ad-categorie' class='form-select'>";
        foreach ($categories as $row) {
            $HTML .= "<option value='{$row["NoCategorie"]}'> {$row["Description"]} </option>";
        }
        $HTML .= "</select>";
        return $HTML; 
    }
    
}