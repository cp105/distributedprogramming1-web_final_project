<?php

require_once 'PHPscripts/redirectHTTPS.php';

/** check if there is an active session **/
session_start();

$is_logged = false;
if (isset($_SESSION['is_logged']) && $_SESSION['is_logged'] == true) {
    $is_logged = true;
}

?>

<!DOCTYPE html>

<html>

<head>
    <meta charset="utf-8">
    <meta name="description" content="Sign-up Airplane Bookings">
    <meta name="author" content="Victor Cappa s257443">

    <title>Sign-up Airplane Bookings</title>

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

    <div class="sidenav">
        <!-- Sidebar -->
        <div>
            <a href="index.php"><strong>Airplane Bookings</strong></a>
            <a href="index.php">Home</a>
            <a href="about.php">About</a>
            <?php if(!$is_logged) {echo "<a href='login.php'>Login</a>";} ?>
            <a href="signup.php">Sign-up</a>
            <?php if($is_logged) {echo "<a href='personalarea.php' style='color: greenyellow;'>Goto Personal Area</a>";} ?>
            <?php if($is_logged) {echo "<a href='PHPscripts/logout.php' style='color: red;'>Logout</a>";} ?>
        </div>
    </div>

    <div class="header">
        <p>Distributed Programming I exam - solution proposed by Victor Cappa s257443</p>
    </div>

    <!-- Page Content -->
    <div class="main">
        <!-- body of the page -->

        <!-- print incoming mesage from POST or GET method -->
        <?php require_once "PHPscripts/printMsg.php"; ?>

        <!-- sign-up form -->
        <form id="form-data" action="PHPscripts/signup.php" method="post">
            <br><h2>Sign-up form:</h2>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter email">
            </div>
            <div class="form-group">
                <label for="psw1">Password:</label>
                <input type="password" class="form-control" id="psw1" name="psw1" placeholder="Enter password">
            </div>
            <div class="form-group">
                <label for="psw2">Repeat password:</label>
                <input type="password" class="form-control" id="psw2" name="psw2" placeholder="Repeat password">
            </div>
            <div class="form-group">
                <div>
                    <p><strong>Please enter a valid Email and Password. The Password must contain at least one lower-case alphabetic character, and at least one other character that is either alphabetical uppercase or numeric"</strong></p>
                    <button type="submit" class="btn btn-primary" onclick="return validateFormData()">Submit</button>
                    <button type="button" class="btn btn-primary"  onclick="resetForm()">Reset</button>
                </div>
            </div>
        </form>
    </div>

    <!-- JavaScript -->
    <script src="libraries/jquery/jquery.min.js"></script>

    <!-- JS check passwords and email-->
    <script>

        /** clear data on the form **/
        function resetForm() {
            document.getElementById("form-data").reset();
        }

        /** client-side signup controls **/
        function validateFormData() {
            let email = document.getElementById("email").value;
            let psw1 = document.getElementById("psw1").value;
            let psw2 = document.getElementById("psw2").value;
            if (email == null || email === "" || psw1 == null || psw1 === "" || psw2 == null || psw2 === "") {
                window.alert("Please enter a valid Email and Passwords. The Passwords must be equals and must contain at least one lower-case alphabetic character, and at least one other character that is either alphabetical uppercase or numeric");
                return false;
            }

            let reg1 = new RegExp(/[a-z]/);
            let reg2 = new RegExp(/[A-Z]/);
            let reg3 = new RegExp(/[0-9]/);
            let psw_validity = reg1.test(psw1) && (reg2.test(psw1) || reg3.test(psw1));
            if (psw1 !== psw2 || !psw_validity) {
                window.alert("Please enter a valid Email and Passwords. The Passwords must be equals and must contain at least one lower-case alphabetic character, and at least one other character that is either alphabetical uppercase or numeric");
                return false;
            }

            return true;
        }
    </script>

</body>
</html>