<?php

require_once "definitions.php";

session_start();

if (isset($_SESSION['is_logged']) && $_SESSION['is_logged'] == true) {
    /** the user already logged into the system, he must log out before logging in again **/
    header('HTTP/1.1 307 temporary redirect');
    header("Location: ../login.php?msg=".urlencode("You are already logged into the system, in order to login again you must Logout first!"));
    exit;
}


/** check validity of passed password and username (with post method) **/
if (isset($_POST['psw']) && isset($_POST['email']) && !empty($_POST['psw']) && !empty($_POST['email'])  && ($_POST['psw'] !== "") && ($_POST['email'] !== "")) {

    try {
        $mysqli = new mysqli(SQL_HOST, SQL_USER, SQL_PASS, SQL_DB);
        if($mysqli->connect_errno) {
            throw new Exception("Temporary Database problems occurred. We advise to try again later.");
        }

        $email = $mysqli->real_escape_string($_POST['email']);
        $password = $_POST['psw'];
        $hash = hash('sha512', $password);              /** create the hash of the password with sha512 **/

        $sql = "SELECT password FROM users WHERE email = '$email'";
        if (!$result = $mysqli->query($sql)) {
            throw new Exception("Temporary Database problems occurred. We advise to try again later.");
        }
        if ($result->num_rows !== 1) {
            $result->free();
            $mysqli->close();
            header('HTTP/1.1 307 temporary redirect');
            header("Location: ../login.php?msg=".urlencode("Wrong email or password"));
            exit;
        }
        if(!$record = $result->fetch_assoc()) {
            throw new Exception("Temporary Database problems occurred. We advise to try again later.");
        }

        if(isset($record['password']) && $record['password'] == $hash) {
            /** Login successful. Data initialization for the given session $_SESSION **/

            $_SESSION['is_logged'] = true;
            $_SESSION['time'] = time();
            $_SESSION['email'] = "$email";
            $_SESSION['buy_log'] = false;

            header('HTTP/1.1 307 temporary redirect');
            header("Location: ../personalarea.php");

            $result->free();
            $mysqli->close();
            exit;
        }
        else {
            /** the passed password is not valid **/
            $result->free();
            $mysqli->close();
            header('HTTP/1.1 307 temporary redirect');
            header("Location: ../login.php?msg=".urlencode("Wrong username or password"));
            exit;
        }

    } catch(Exception $e) {
        /** error occurred while communicating with the DB **/
        $var = $e->getMessage();
        $mysqli->close();
        header('HTTP/1.1 307 temporary redirect');
        header("Location: ../login.php?msg=".urlencode($var));
        exit;
    }
}
else {
    /** the passed password or-and email is not valid or incomplete **/
    header('HTTP/1.1 307 temporary redirect');
    header("Location: ../login.php?msg=".urlencode("Invalid Email or Password"));
    exit;
}
