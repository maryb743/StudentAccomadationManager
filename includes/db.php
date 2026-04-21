<?php
//link database to project
$conn = new mysqli("localhost", "root", "", "accommodation_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>