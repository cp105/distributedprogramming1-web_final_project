<?php

include_once "definitions.php";

session_start();

/** check if user timeout expired **/
$diff = 0;
if (isset($_SESSION['time'])) {
    $t0 = $_SESSION['time'];
    $diff = (time() - $t0);
    if (($diff > SESSION_TIMEOUT)) {
        /** the session timed out **/
        echo json_encode(array("outcome"=>"timeout","new_status"=>"not_specified"));
        exit;
    }
    // update the timeout time for the given session
    $_SESSION['time'] = time();
} else {
    echo json_encode(array("outcome" => "expired", "new_status" => "not_specified"));
    exit;
}


/** un reserve the seat in the Database if it is still mine (reserved by me), otherwise do nothing **/
/** in any case return to the client a json containing the outcome of the operation and the new color of the given seat {outcome: true; new_status:"reserved"} **/

$seat_number = false;
if (isset($_REQUEST['seat_number']) && is_numeric($_REQUEST['seat_number'])) {      // !empty($_REQUEST['seat_number']) must NOT be included (empty($_REQUEST['seat_number']) returns true if seat_number is 0)
    $seat_number = $_REQUEST['seat_number'];
} else {
    echo json_encode(array("outcome" => "false", "new_status" => "not_specified"));
    exit;
}
$account_email = false;
if (isset($_REQUEST['email']) && !empty($_REQUEST['email'])) {
    $account_email = $_REQUEST['email'];                        /** sanitizing is done afterwards with $mysqli->real_escape_string($account_email); **/
} else {
    echo json_encode(array("outcome" => "false", "new_status" => "not_specified"));
    exit;
}

function queryDatabase1($mysqli, $sql)
{
    if (!$result = $mysqli->query($sql)) {
        throw new Exception("Temporary Database problems occurred. We advise to try again later.");
    }
    if ($result->num_rows !== 1) {
        throw new Exception("Temporary Database problems occurred. We advise to try again later.");
    }
    if (!$record = $result->fetch_assoc()) {
        throw new Exception("Temporary Database problems occurred. We advise to try again later.");
    }
    return $record;
}

try {
    $mysqli = new mysqli(SQL_HOST, SQL_USER, SQL_PASS, SQL_DB);
    if ($mysqli->connect_errno) {
        throw new Exception("Temporary Database problems occurred. We advise to try again later");
    }

    if (!($mysqli->begin_transaction())) {                   /** autocommit mode is deactivated implicitly by using begin_transaction (see MySql docs for more infos) **/
        throw new Exception("Temporary Database problems occurred. We advise to try again later");
    }

} catch (Exception $e) {
    /** error occurred while opening communication with the DB or starting new transaction **/
    echo json_encode(array("outcome" => "false", "new_status" => "not_specified"));
    exit;
}

try {
    /** Un reserve the seat **/
    $record = queryDatabase1($mysqli, "SELECT status, email FROM airplane_seats WHERE seat_number='$seat_number' FOR UPDATE");
    $seat_status = $record['status'];
    $seat_email = $record['email'];

    if ($seat_status == 'reserved' && $seat_email == $account_email) {
        /** the seat is reserved by me **/
        $sql = "UPDATE airplane_seats SET status='free', email='' WHERE seat_number='$seat_number'";
        if ($mysqli->query($sql) === true) {
            /** successful un reservation **/
            echo json_encode(array("outcome" => "true", "new_status" => "free"));
            $mysqli->commit();
            $mysqli->close();
            exit;
        } else {
            throw new Exception("Temporary Database problems occurred. We advise to try again later.");
        }
    } else if ($seat_status == 'reserved' && $seat_email !== $account_email) {
        echo json_encode(array("outcome" => "true", "new_status" => "reserved"));       // the seat is reserved by someone else
        $mysqli->commit();
        $mysqli->close();
        exit;
    } else if ($seat_status == 'purchased') {
        echo json_encode(array("outcome" => "true", "new_status" => "purchased"));       // the seat is purchased by someone else
        $mysqli->commit();
        $mysqli->close();
        exit;
    } else {
        echo json_encode(array("outcome"=>"false","new_status"=>"not_specified"));
        $mysqli->rollback();
        $mysqli->close();
        exit;
    }

} catch (Exception $e) {
    /** error occurred while communicating with the Database **/
    $mysqli->rollback();
    $mysqli->close();
    echo json_encode(array("outcome" => "false", "new_status" => "not_specified"));
    exit;
}