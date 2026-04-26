<?php
//allow user to log out and destroy session data
session_start();
session_destroy();
//redirect to login page after logout
header("Location: login.php");

exit();

?>