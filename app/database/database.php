<?php
class Database {
    /**
     * Connecte à la base de donnée
     * @return mysqli Retourne la connection `mysqli` s'il n'y a pas d'erreur, sinon `False`
     */
    public static function Connect() {
        try {
            $con = new mysqli("localhost", "root", "", "projet_03");
        } catch (mysqli_sql_exception $e) {
            header("HTTP/1.0 500");
            echo "The database did not respond.";
            die();
        }
        return $con;
    }
}