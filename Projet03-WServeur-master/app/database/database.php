<?php
class Database {
    /**
     * Connecte à la base de donnée
     * @return mysqli Retourne la connection `mysqli` s'il n'y a pas d'erreur, sinon `False`
     */
    public static function Connect() {
        try {
            $con = new mysqli("mysql-projet03-wserveur.alwaysdata.net", "projet03-wserveur", "Qwerty-01$", "projet03-wserveur_db");
        } catch (mysqli_sql_exception $e) {
            header("HTTP/1.0 500");
            echo "500 Internal server error.";
            die();
        }
        return $con;
    }
}