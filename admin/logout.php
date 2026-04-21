<?php
//allow user to log out and destroy session data
session_start();
session_destroy();
header("Location: login.php");

exit();

?>