<?php
session_start();

//if user isn't logged in send to login page
if (!isset($_SESSION['user_id'])) {

    header("Location: login.php");
    exit();

}


?>