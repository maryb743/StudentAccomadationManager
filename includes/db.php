<?php
//connect database
$conn = new mysqli("localhost", "root", "", "accommodation_db");

//error handling
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>