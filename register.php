<?php
session_start();
require "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Hash password
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    // Insert into users table
    $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password_hashed);

    if ($stmt->execute()) {
        echo "🎉 Registration successful! You can now <a href='login.php'>login</a>";
    } else {
        echo "❌ Error: " . $stmt->error;
    }
}
?>

<form method="POST">
    <h2>Register New User</h2>
    <label>Username:</label><br>
    <input type="text" name="username" required><br><br>

    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Register</button>
</form>
