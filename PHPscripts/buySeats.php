<?php

/** buy the reserved seats of the user (the user sends the list of seats visualized as booked by him in his current personal area) **/

include_once "definitions.php";

session_start();

/** check if user timeout expired **/
$diff = 0;
if (isset($_SESSION['time'])) {
    $t0 = $_SESSION['time'];
    $diff = (time() - $t0);
    if (($diff > SESSION_TIMEOUT)) {
        /** the session timed out **/
        echo json_encode(array("outcome"=>"timeout"));
        exit;
    }
    // update the timeout time for the given session
    $_SESSION['time'] = time();
} else {
    echo json_encode(array("outcome"=>"expired"));
    exit;
}


/** check if all the seats are available **/

$booked_seats = false;
if (isset($_REQUEST['booked_seats']) && !empty($_REQUEST['booked_seats'])) {
    // get the content of the passed array
    $booked_seats = $_REQUEST['booked_seats'];

} else {
    echo json_encode(array("outcome"=>"false"));
    exit;
}
foreach ($booked_seats as $value) {                     // sanitize the incoming array
    if (!is_numeric($value)) {
        echo json_encode(array("outcome"=>"false"));
        exit;
    }
}
$account_email = false;
if (isset($_REQUEST['email']) && !empty($_REQUEST['email'])) {
    $account_email = $_REQUEST['email'];                    /** sanitizing is done afterwards with $mysqli->real_escape_string($account_email); **/
} else {
    echo json_encode(array("outcome"=>"false"));
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
    /** error occurred while opening communication with the DB **/
    echo json_encode(array("outcome" => "false"));
    exit;
}

$can_be_booked = array();
$cannot_be_booked = array();
$cannot_be_booked_status = array();
$cannot_be_booked_email = array();
$successful_operation = false;
try {
    /** Reserve the seats **/
    /** compute number of reserved seat in the Database **/
    $sanitized_account_email = $mysqli->real_escape_string($account_email);
    $record = queryDatabase1($mysqli, "SELECT count(*) AS booked_count FROM airplane_seats WHERE email='$sanitized_account_email' AND status='reserved' FOR UPDATE");
    $DBbooked_count = $record['booked_count'];

    /** check booked seats in the Database with the ones requested from the client side **/
    foreach ($booked_seats as $value) {
        $record = queryDatabase1($mysqli, "SELECT status, email FROM airplane_seats WHERE seat_number='$value' FOR UPDATE");
        $seat_status = $record['status'];
        $seat_email = $record['email'];
        if ($seat_status == 'reserved' && $seat_email == $account_email) {
            array_push($can_be_booked, $value);
        } else {
            array_push($cannot_be_booked, $value);
            array_push($cannot_be_booked_status, $seat_status);
            array_push($cannot_be_booked_email, $seat_email);
        }
    }

    /** purchase or free-up the seats **/
    if (count($booked_seats) == $DBbooked_count && count($cannot_be_booked) == 0) {
        // the seats can be booked
        foreach ($booked_seats as $value) {
            $sql = "UPDATE airplane_seats SET status='purchased', email='$sanitized_account_email' WHERE seat_number='$value'";
            if ($mysqli->query($sql) !== true) {
                throw new Exception("Temporary Database problems occurred. We advise to try again later.");
            }
        }
        $successful_operation = true;
        echo json_encode(array("outcome" => "true"));
        $mysqli->commit();
        $mysqli->close();
    } else {
        // the seats cannot be booked
        $tmp = count($cannot_be_booked);
        echo json_encode(array("outcome" => "true"));

        // free-up all the seats reserved by the user
        $sql = "UPDATE airplane_seats SET status='free', email='' WHERE status='reserved' AND email='$sanitized_account_email'";
        if ($mysqli->query($sql) !== true) {
            throw new Exception("Temporary Database problems occurred. We advise to try again later.");
        }

        $mysqli->commit();
        $mysqli->close();
    }

} catch (Exception $e) {
    /** error occurred while communicating with the Database **/
    $mysqli->rollback();
    $mysqli->close();
    echo json_encode(array("outcome" => "false"));

    exit;
}


/** produce a report for the client about the outcome of the operation. It is stored in the Session object and will be shown at reload time in the personal area **/
function seatID($seat_number) {
    $row = (int) ($seat_number / (int) SEATS_WIDTH);
    $pos = $seat_number % SEATS_WIDTH;
    $sID = chr($pos + 65).strval($row + 1);
    return $sID;
}

if ($successful_operation === true) {
    $_SESSION['buy_log'] = true;
    $_SESSION['buy_log_array'] = array();
    array_push($_SESSION['buy_log_array'], "The buy operation was successful!");
    foreach ($booked_seats as $value) {
        $value = seatID($value);
        array_push($_SESSION['buy_log_array'], "Seat $value successfully purchased");
    }
} else {
    $_SESSION['buy_log'] = true;
    $_SESSION['buy_log_array'] = array();
    array_push($_SESSION['buy_log_array'], "The buy operation cannot be performed!");
    foreach ($can_be_booked as $value) {
        $value = seatID($value);
        array_push($_SESSION['buy_log_array'], "Seat $value can be successfully purchased");
    }
    for ($i = 0; $i < count($cannot_be_booked); $i++) {
        $value = seatID($cannot_be_booked[$i]);
        if ($cannot_be_booked_status[$i] == 'free') {
            array_push($_SESSION['buy_log_array'], "Seat $value cannot be successfully purchased because is free");
        } else {
            array_push($_SESSION['buy_log_array'], "Seat $value cannot be successfully purchased because $cannot_be_booked_status[$i] by user $cannot_be_booked_email[$i]");
        }
    }
}
