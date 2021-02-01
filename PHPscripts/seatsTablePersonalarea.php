<?php

include_once "PHPscripts/definitions.php";

/** check if a user logged in the current session or not (necessary to print yellow seats, instead of orange) **/
/** IMPORTANT session_start() must be already called at that point by the calling script **/

$session_email = false;
if (isset($_SESSION['is_logged']) && $_SESSION['is_logged'] == true) {
    if (isset($_SESSION['email']) && !empty($_SESSION['email'])) {
        $session_email = $_SESSION['email'];
    }
}


function queryDatabase1($mysqli, $sql) {
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
    if($mysqli->connect_errno) {
        throw new Exception("Temporary Database problems occurred. We advise to try again later");
    }

    if (!($mysqli->begin_transaction())) {                   /** autocommit mode is deactivated implicitly by using begin_transaction (see MySql docs for more infos) **/
        throw new Exception("Temporary Database problems occurred. We advise to try again later");
    }

} catch (Exception $e) {
    /** error occurred while opening communication with the DB **/
    $var = $e->getMessage();
    echo "<h4>$var</h4>";
    exit;
}

try {
    $width_table = (SEATS_WIDTH * 75).'px';
    $seats_num = SEATS_WIDTH * SEATS_LEN;

    /** Display the Seat Map **/
    $count = 0;
    echo "<div style='padding: 50px; float: left;'>";
    echo "<h3>Airplane Seats Table</h3>";
    echo "<table class='table' style='width: $width_table'>";
    for ($i = 0; $i < SEATS_LEN; $i++) {
        echo "<tr>";
        for ($j = 0; $j < SEATS_WIDTH; $j++) {

            $record = queryDatabase1($mysqli, "SELECT status, email FROM airplane_seats WHERE seat_number='$count'");
            $seat_status = $record['status'];
            $seat_email = $record['email'];
            $seatID = chr($j + 65).strval($i + 1);
            if ($seat_status == 'free') {
                echo "<td id='$count' style='border-radius: 10px 20px; background-color: green; padding: 15px; text-align: center; border: 5px solid white;'> Seat<br>$seatID </td>";
            } else if ($seat_status == 'reserved' && $session_email == $seat_email) {
                echo "<td id='$count' style='border-radius: 10px 20px; background-color: yellow; padding: 15px; text-align: center; border: 5px solid white;'> Seat<br>$seatID </td>";
            } else if ($seat_status == 'purchased') {
                echo "<td id='$count' style='border-radius: 10px 20px; background-color: red; padding: 15px; text-align: center; border: 5px solid white;'> Seat<br>$seatID </td>";
            } else if ($seat_status == 'reserved') {
                echo "<td id='$count' style='border-radius: 10px 20px; background-color: orange; padding: 15px; text-align: center; border: 5px solid white;'> Seat<br>$seatID </td>";
            } else {
                throw new Exception("Temporary Database problems occurred. We advise to try again later.");
            }

            $count++;
        }
        echo "</tr>";
    }
    echo "</table>" ;
    echo "</div>";


    echo "<div style='padding: 50px; float: left;'>";

    ?>

<table class="table" style='width: 400px'>
    <tr>
        <td style='border-radius: 10px 20px; background-color: green; padding: 15px; text-align: center; border: 5px solid white;'> Free<br>seat</td>
        <td style='border-radius: 10px 20px; background-color: red; padding: 15px; text-align: center; border: 5px solid white;'> Purchased<br>seat</td>
        <td style='border-radius: 10px 20px; background-color: orange; padding: 15px; text-align: center; border: 5px solid white;'> Reserved<br>seat</td>
        <td style='border-radius: 10px 20px; background-color: yellow; padding: 15px; text-align: center; border: 5px solid white;'> My reserved<br>seat</td>
    </tr>
</table><br>

    <?php
    /** Display basic statistics about the Seat Map **/

    echo "<h3>Airplane Seats Table Statistics</h3>";
    echo "<table class='table' style='width: 400px'>";

    echo "<tr><td>Total number of seats</td> <td>$seats_num</td></tr>";

    $record = queryDatabase1($mysqli, "SELECT count(*) AS free FROM airplane_seats WHERE status='free'");
    $seats_free = $record['free'];
    echo "<tr><td>Total number of free seats</td> <td id='freeSeats'>$seats_free</td></tr>";

    $record = queryDatabase1($mysqli, "SELECT count(*) AS reserved FROM airplane_seats WHERE status='reserved'");
    $seats_reserved = $record['reserved'];
    echo "<tr><td>Total number of reserved seats</td> <td id='reservedSeats'>$seats_reserved</td></tr>";

    $seats_purchased = $seats_num - $seats_free - $seats_reserved;
    echo "<tr><td>Total number of purchased seats</td> <td id='purchasedSeats'>$seats_purchased</td></tr>";
    echo "</table><br>";


    /** Display Update and Buy button **/
    ?>

<button type="button" id="buyButton" class="btn btn-primary">Buy</button>
<button type="button" id="updateButton" class="btn btn-primary" onclick="window.location.href = 'personalarea.php';">Update</button>

    <?php

    /** Show the Buy Log if available **/
    require_once "showBuylog.php";

    echo "</div>";

    $mysqli->commit();

} catch(Exception $e) {
    /** error occurred while communicating with the DB **/
    echo "</tr>";
    echo "</table>";
    $var = $e->getMessage();
    echo "<h4>$var</h4>";

    $mysqli->rollback();
    $mysqli->close();
    exit;
}