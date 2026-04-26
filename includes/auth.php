<?php
session_start();

//user authenitication (send user to login if they're not)
if (!isset($_SESSION['user_id'])) {

    header("Location: login.php");
    exit();

}


?>