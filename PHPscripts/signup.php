<?php

include_once "definitions.php";

/** Register the received user in the Database **/

$valid_email = isset($_POST['email']) && !empty($_POST['email']) && ($_POST['email'] !== "") && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
$valid_psw1 = isset($_POST['psw1']) && !empty($_POST['psw1']) && ($_POST['psw1'] !== "");
$valid_psw2 = isset($_POST['psw2']) && !empty($_POST['psw2']) && ($_POST['psw2'] !== "");
$psw_validity = preg_match('/[a-z]/', $_POST['psw1']) && (preg_match('/[A-Z]/', $_POST['psw1']) || preg_match('/[0-9]/', $_POST['psw1']));
$psw_equality = $_POST['psw1'] == $_POST['psw2'];

if ($valid_email && $valid_psw1 && $valid_psw2 && $psw_equality && $psw_validity) {

    try {
        $mysqli = new mysqli(SQL_HOST, SQL_USER, SQL_PASS, SQL_DB);
        if ($mysqli->connect_errno) {
            throw new Exception("Temporary Database problems occurred. We advise to try again later");
        }

        $email = $mysqli->real_escape_string($_POST['email']);
        $password = $_POST['psw1'];
        $hash = hash('sha512', $password);

        if (!($mysqli->begin_transaction())) {                   /** autocommit mode is deactivated implicitly by using begin_transaction (see MySql docs for more infos) **/
            throw new Exception("Temporary Database problems occurred. We advise to try again later");
        }

    } catch (Exception $e) {
        $var = $e->getMessage();
        $mysqli->close();
        header('HTTP/1.1 307 temporary redirect');
        header("Location: ../signup.php?msg=".urlencode($var));
        exit;
    }

    try {
        $sql = "SELECT * FROM users FOR UPDATE";                /** locking the whole table for preventing concurrent insertions of users with the same email **/
        if (!$result = $mysqli->query($sql)) {
            throw new Exception("Temporary Database problems occurred. We advise to try again later");
        }
        $result->free();

        $sql = "SELECT password FROM users WHERE email='$email'";
        if (!$result = $mysqli->query($sql)) {
            throw new Exception("Temporary Database problems occurred. We advise to try again later");
        }
        if ($result->num_rows == 0) {
            /** there are no other users with the same email (the email must be unique in the Database), register the email and related password **/

            $sql = "INSERT INTO users(email, password) VALUES('$email','$hash')";
            if ($mysqli->query($sql) === true) {
                /** successful registration of new account **/

                $mysqli->commit();
                $result->free();
                $mysqli->close();
                header('HTTP/1.1 307 temporary redirect');
                header("Location: ../signup.php?msg=".urlencode("Sign-up was successful! thanks for registering on the website"));
                exit;
            }
            else {
                throw new Exception("Temporary Database problems occurred. We advise to try again later");
            }
        } else {
            /** the given email is already registered in the system (the email must be unique in the Database) **/
            $mysqli->rollback();
            $result->free();
            $mysqli->close();
            header('HTTP/1.1 307 temporary redirect');
            header("Location: ../signup.php?msg=".urlencode("The given Email is already registered in the Database, please Sign-up with another one"));
            exit;
        }
    } catch(Exception $e) {
        /** error occurred while communicating with the DB **/
        $var = $e->getMessage();
        $mysqli->rollback();
        $mysqli->close();
        header('HTTP/1.1 307 temporary redirect');
        header("Location: ../signup.php?msg=".urlencode($var));
        exit;
    }
}
else {
    /** the passed password is not valid or incomplete **/
    header('HTTP/1.1 307 temporary redirect');
    header("Location: ../signup.php?msg=".urlencode("Invalid Email or Password"));
    exit;
}