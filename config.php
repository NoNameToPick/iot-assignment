<?php
$servername = "98.93.82.44";
$db_username = "phpadmin";
$db_password = "StrongPassword123";
$db_name = "intruderSystem";
$port = 3306;

$conn = new mysqli($servername, $db_username, $db_password, $db_name, $port);

// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }
// echo "Database connected successfully!";
?>
