//allow user to log out and destroy session data
<?php

session_start();
session_destroy();
header("Location: login.php");

exit();

?>