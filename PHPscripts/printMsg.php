<?php


if (isset($_REQUEST['msg'])) {
    $clean_msg = stripslashes(strip_tags($_REQUEST['msg']));
    echo "<br><h4 id='msg'>$clean_msg</h4><br>";
}