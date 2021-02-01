<?php

if (isset($_SESSION['buy_log']) && $_SESSION['buy_log'] === true) {

    echo "<div style='height: 50px;'> </div>";
    echo "<h3>Buy Operation Message</h3>";
    echo "<table class='table' style='width: 400px'>";
    $count = 0;
    foreach ($_SESSION['buy_log_array'] as $value) {
        if ($count === 0) {
            echo "<tr><td><strong>$value<strong></td></tr>";
        } else {
            echo "<tr><td> $value </td></tr>";
        }
        $count++;
    }
    echo "</table>";

    /** the Buy Log is only shown once **/
    $_SESSION['buy_log'] = false;
    $_SESSION['buy_log_array'] = array();
}
