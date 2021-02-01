<?php
    session_start();

    $_SESSION = array();

    // delete the session cookie in the client side
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 3600*24, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
    }

    // destroy session
    session_destroy();

    // redirect client to login page
    header('HTTP/1.1 307 temporary redirect');
    header("Location: ../index.php?msg=".urlencode("Logout successful"));

    exit;

