<?php
$servername = "localhost";
$username = "root";
$password = ""; // Leave blank if no password set in XAMPP
$dbname = "pos_db"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>