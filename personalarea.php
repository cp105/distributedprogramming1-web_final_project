<?php

session_start();

include_once "PHPscripts/definitions.php";
include_once 'PHPscripts/redirectHTTPS.php';

/** check if the user is logged into the system, if not destroy session object an REDIRECT to the login page **/
$is_logged = false;
if(!isset($_SESSION['is_logged']) || $_SESSION['is_logged'] !== true) {
    $_SESSION = array();
    // delete the session cookie in the client side
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 3600*24, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
    }
    // destroy session
    session_destroy();

    header('HTTP/1.1 307 temporary redirect');
    header("Location: login.php?msg=".urlencode("You must Login first for your Personal Area"));
    exit;
} else {
    $is_logged = true;
}

/** check timeout, if not destroy session object an REDIRECT to the login page **/
include_once 'sessionTimeoutManager.php';

/** get email of logged user **/
$account_email = false;
if (isset($_SESSION['email']) && !empty($_SESSION['email'])) {
    $account_email = $_SESSION['email'];
} else {
    $_SESSION = array();
    // delete the session cookie in the client side
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 3600*24, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
    }
    // destroy session
    session_destroy();

    header('HTTP/1.1 307 temporary redirect');
    header("Location: index.php?msg=".urlencode("Fatal Error Happened"));
    exit;
}
?>



<!DOCTYPE html>

<html>

<head>
    <meta charset="utf-8">
    <meta name="description" content="Personal Area Airplane Bookings">
    <meta name="author" content="Victor Cappa s257443">

    <title>Personal Area Airplane Bookings</title>

    <!-- Bootstrap core CSS -->
    <link href="libraries/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google font 'Roboto' -->
    <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="CSS/styling.css" rel="stylesheet">

    <!-- Check JS and Cookies are enabled -->
    <script>
        if (!navigator.cookieEnabled) {
            // if JS is enabled but cookies are not enabled logout
            window.location.href = "PHPscripts/logout.php";
        }
    </script>
    <noscript>
        <!-- JS is not enabled, logout -->
        <meta http-equiv="refresh" content="0.0;url=PHPscripts/logout.php" />
    </noscript>
</head>

<body>

    <div class="sidenav">
        <div>
            <a href="index.php"><strong>Airplane Bookings</strong></a>
            <a href="index.php">Home</a>
            <a href="about.php">About</a>
            <a href="signup.php">Sign-up</a>
            <?php if($is_logged) {echo "<a href='PHPscripts/logout.php' style='color: red;'>Logout</a>";} ?>
        </div>
    </div>

    <div class="header">
        <span>Distributed Programming I exam - solution proposed by Victor Cappa s257443</span>
        <span><p  style="text-align: right;">Welcome to your personal area <strong><?php echo "$account_email"; ?></strong>!</p></span>
    </div>


    <div class="main">
        <!-- content of the page -->

        <!-- print incoming mesage from POST or GET method -->
        <?require_once "PHPscripts/printMsg.php"; ?>

        <!-- show seats table -->
        <br><h1 style="text-align: center;">Airplane Seats Map</h1><br>
        <?php require_once "PHPscripts/seatsTablePersonalarea.php"; ?>

    </div>

    <!-- JavaScript -->
    <script src="libraries/jquery/jquery.min.js"></script>


    <!-- Table Interaction Script -->
    <script>
        /** event handlers for clicks on seats **/

        function seatClick(current_elem) {
            let account_email = "<?php echo $account_email; ?>";

            /** reserve on Database **/
            if (current_elem.style.backgroundColor === "green" || current_elem.style.backgroundColor === "orange") {
                $.post("PHPscripts/reserveSeat.php", {seat_number:current_elem.id.toString(), email:account_email.toString()},
                    function(data) {
                    if (data.outcome === 'true') {
                        // succesful operation
                        if (data.new_status === 'reserved')                         // meaning it is reserved by me
                            current_elem.style.backgroundColor = "yellow";
                        else if (data.new_status === 'purchased') {
                            window.alert("The seat is purchased already!");
                            current_elem.style.backgroundColor = "red";
                        } else if (data.new_status === 'free')
                            current_elem.style.backgroundColor = "green";
                        else
                            window.alert("An error from the server side occurred, please try again later.");
                    } else if (data.outcome === 'timeout') {
                        window.alert("Session Timeout. The operation is not performed");
                        // the page is reloaded and given that the session timed out it will destroy the session and redirect to the main page
                        window.location.href = "personalarea.php";
                    }  else if (data.outcome === 'expired') {
                        window.alert("The session expired. The operation is not performed");
                        // the page is reloaded
                        window.location.href = "personalarea.php";
                    } else {
                        // server error
                        window.alert("An error from the server side occurred, please try again later.");
                    }
                }, "json").fail(
                    function() {
                        window.alert("The ajax request cannot be performed. Please try again later");
                    });

            } else if (current_elem.style.backgroundColor === "yellow") {
                /** un reserve the seat (if it is still owned by the current user, meaning nobody bought it or reserved it in the meantime) **/
                $.post("PHPscripts/unreserveSeat.php", {seat_number: current_elem.id.toString(), email:account_email.toString() },
                    function (data) {
                        if (data.outcome === 'true') {
                            // succesful operation
                            if (data.new_status === 'free')
                                current_elem.style.backgroundColor = "green";
                            else if (data.new_status === 'purchased')
                                current_elem.style.backgroundColor = "red";
                            else if (data.new_status === 'reserved')                // meaning it is reserved by someone else
                                current_elem.style.backgroundColor = "orange";
                            else
                                window.alert("An error from the server side occurred, please try again later.");
                        } else if (data.outcome === 'timeout') {
                            window.alert("Session Timeout. The operation is not performed");
                            // the page is reloaded and given that the session timed out it will destroy the session and redirect to the main page
                            window.location.href = "personalarea.php";
                        }  else if (data.outcome === 'expired') {
                            window.alert("The session expired. The operation is not performed");
                            // the page is reloaded
                            window.location.href = "personalarea.php";
                        } else {
                            // server error
                            window.alert("An error from the server side occurred, please try again later.");
                        }
                }, "json").fail(
                    function() {
                        window.alert("The ajax request cannot be performed. Please try again later");
                    });

            } else if (current_elem.style.backgroundColor === "red") {
                /** perform no action **/
                window.alert("The selected seat cannot be reserved after it is bought!");
            }
        }

        /** attach event handler to each shown button **/
        num_seats = <?php echo SEATS_LEN * SEATS_WIDTH; ?>;
        for (let a = 0; a < num_seats; a++) {
            document.getElementById(a.toString()).addEventListener('click', function() {
                seatClick(this);
            });
        }
    </script>

    <script>
        /** event handler for Buy button **/

        function buySeats() {
            let account_email = "<?php echo $account_email; ?>";
            let booked_seats = [];
            let num_booked_seats = 0;
            let num_seats = "<?php echo SEATS_LEN * SEATS_WIDTH; ?>";
            for (let a = 0; a < num_seats; a++) {
                if (document.getElementById(a.toString()).style.backgroundColor === 'yellow') {
                    booked_seats.push(a);
                    num_booked_seats++;
                }
            }
            if (num_booked_seats > 0) {
                $.post('PHPscripts/buySeats.php', {'booked_seats[]':booked_seats, email:account_email}, function(data) {
                    /** reload the page in order to update the seats map and print the report on the buy operation **/
                    if (data.outcome === 'true') {
                        /** reloading the page will show the buy log (of successful buy operation or not) and updated seats map**/
                        window.location.href = "personalarea.php";
                    } else if (data.outcome === 'timeout') {
                        window.alert("Session Timeout. The operation is not performed");
                        // the page is reloaded and given that the session timed out it will destroy the session and redirect to the main page
                        window.location.href = "personalarea.php";
                    }  else if (data.outcome === 'expired') {
                        window.alert("The session expired. The operation is not performed");
                        // the page is reloaded
                        window.location.href = "personalarea.php";
                    } else {
                        window.alert("Temporary Database problems occurred. We advise to try again later");
                    }

                }, 'json').fail(
                    function() {
                        window.alert("The ajax request cannot be performed. Please try again later");
                    });
            } else {
                window.alert("No seats have been reserved. Please reserve some seats prior to Buy them");
            }
        }

        document.getElementById('buyButton').addEventListener('click', function() {
            buySeats();
        });

    </script>

</body>

</html>
