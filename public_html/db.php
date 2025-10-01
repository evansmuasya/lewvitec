<?php
$host = "localhost";
$user = "root";
$pass = ""; // your MySQL password (set it if not empty)
$dbname = "shopping"; // your database name

$conn = new mysqli($host, $user, $pass, $dbname);

// Test connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
