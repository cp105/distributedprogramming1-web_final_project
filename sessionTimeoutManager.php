<?php

/** session_start() must have been already called in the caller script **/

function destroySession($string) {
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
    header("Location: login.php?msg=".urlencode($string));
    exit;
}

$diff = 0;
if (isset($_SESSION['time'])){
    $t0 = $_SESSION['time'];
    $diff = (time() - $t0);
    if (($diff > SESSION_TIMEOUT)) {
        /** the session timed out, the function exits the execution of the script **/
        destroySession('The previous session timed out');
    }

    // update the timeout time for the given session
    $_SESSION['time'] = time();
} else {
    destroySession("Fatal Error happened");
}

	



