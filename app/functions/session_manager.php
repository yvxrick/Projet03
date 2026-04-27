<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "app/database/user.php";
$login_page_link = "https://projet03-wserveur.alwaysdata.net/index.php?p=login";
session_start(); // Starts session globally.
/**
 * Logs out a user by destroying his session.
 * @return bool
 */
function logout_user()
{
    session_unset();
    session_destroy();

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    return true;
}
/**
 * Starts the user's session.
 * @param $id User's id
 * @return bool
 */
function login_user($id = null, $email = null, $statut = null) {
    if ($id == null || $email == null || $statut == null) {
        return false;
    }

    $_SESSION["user_id"] = intval($id);
    $_SESSION["email"] = $email;
    $_SESSION["statut"] = intval($statut);
    return true;
}

/**
 * Logs out a user if he dosen't have an outgoing session. If that's the case, user is sent to login page.
 * @return bool Returns `True` if he was logged out, else `False`
 */
function logout_if_no_session() {
    global $login_page_link;
    if (!(isset($_SESSION["user_id"]))) {
        logout_user();
        header("Location: $login_page_link");
        return true;
    }
    return false;
}

/**
 * Returns a boolean indicating if the user is already logged in, meaning he has an outgoing session.
 * @return bool
 */
function user_is_logged_in() {
    if (isset($_SESSION) && count($_SESSION) > 0) {
        return true;
    }
    return false;
}

/**
 * Redirige l'utilisateur s'il n'a pas encore de profil.
 * @param mixed $user_email
 * @return bool
 */
function redirect_if_no_profile($user_email) {
    $user_obj = new user($user_email);
    if (!($user_obj->has_filled_profile())) {
        header("Location: profil.php");
        return true;
    }
    return false;
}

/**
 * Redirige l'utilisateur vers une page HTTP 403 s'il n'a pas le droit d'accéder à la page demandé.
 * @param mixed $id_to_access
 * @return bool
 */
function can_access_page($id_to_access) {
    $id_to_access = intval($id_to_access);
    $user_id = intval($_SESSION["user_id"]);
    if ($id_to_access !== $user_id) {
        header("Location: forbidden.html");
        return false;
    }
    return true;
}

/**
 * Retourne un booléan indiquant si l'utilisateur est un administrateur.
 * @return bool
 */
function is_admin() {
    $statut = $_SESSION["statut"];
    return $statut === 1;
}