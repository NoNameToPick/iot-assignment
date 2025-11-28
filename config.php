<?php
$servername = "localhost";   // Local server
$db_username = "root";       // Default MySQL account
$db_password = "";           // Default password (empty)
$db_name = "test";           // Using 'test' database

$conn = new mysqli($servername, $db_username, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4"); // Ensure UTF-8 format
?>
