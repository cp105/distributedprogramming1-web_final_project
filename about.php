<?php
require_once 'PHPscripts/redirectHTTPS.php';

/** check if there is an active session **/
session_start();

$is_logged = false;
if (isset($_SESSION['is_logged'])  && $_SESSION['is_logged'] == true) {
    $is_logged = true;
}

?>

<!DOCTYPE html>

<html>
    <head>
        <meta charset="utf-8">
        <meta name="description" content="about page Aiplane Bookings">
        <meta name="author" content="Victor Cappa s257443">

        <title>About Airplane Bookings</title>

        <!-- Bootstrap core CSS -->
        <link href="libraries/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <!-- Google font 'Roboto' -->
        <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
        <!-- Custom styles for this template -->
        <link href="CSS/styling.css" rel="stylesheet">

        <!-- Check JS and Cookies are enabled -->
        <script>
            if (!navigator.cookieEnabled) {
                // if JS is enabled but cookies are not enabled return to errorindex.php page
                window.location.href = "errorindex.php";
            }
        </script>
        <noscript>
            <!-- JS is not enabled, go to errorindex.php page -->
            <meta http-equiv="refresh" content="0.0;url=errorindex.php" />
        </noscript>
    </head>


    <body>
        <!-- Sidebar -->
        <div class="sidenav">
            <div>
                <a href="index.php"><strong>Airplane Bookings</strong></a>
                <a href="index.php">Home</a>
                <a href="about.php">About</a>
                <?php if(!$is_logged) {echo "<a href='login.php'>Login</a>";} ?>
                <a href="signup.php">Sign-up</a>
                <?php if($is_logged) {echo "<a href='personalarea.php' style='color: greenyellow;'>Goto Personal Area</a>";} ?>
                <?php if($is_logged) {echo "<a href='PHPscripts/logout.php'style='color: red;'>Logout</a>";} ?>
            </div>
        </div>

        <div class="header">
            <p>Distributed Programming I exam - solution proposed by Victor Cappa s257443</p>
        </div>

        <!-- Page Content -->
        <div class="main">
            <!-- body of the page -->

            <div>
                <!-- print incoming message from POST or GET method -->
                <?php require_once "PHPscripts/printMsg.php"; ?>
            </div>

            <div style="height: 200px"></div>
            <p style="margin: 0 auto; text-align: center; width: 800px;"><strong>Distributed Programming I Academic Year 2018/19 - exam 25 June 2019</strong></p>
            <p style="margin: 0 auto; text-align: justify; width: 800px;">This website is developed by Victor Cappa S257443 for the Web Programming assignment of the Distributed Programming I exam offered by <a href="http://www.polito.it">Politecnico di Torino</a> in the first year of the Master Degree in Computer Engineering.</p>
        </div>

        <!-- JavaScript -->
        <script src="libraries/jquery/jquery.min.js"></script>

    </body>
</html>