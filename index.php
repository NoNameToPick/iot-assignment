<?php
session_start();
require "config.php";

$message = ''; // Message to show on the page

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username == '' || $password == '') {
        $message = "⚠ Please enter both username and password.";
    } else {
        // Check failed attempts for this username in last 15 minutes
        $sql = "SELECT COUNT(*) AS fail_count
                FROM login_attempts
                WHERE username = ? 
                AND is_successful = 0
                AND attempt_time > (NOW() - INTERVAL 15 MINUTE)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $data = $stmt->get_result()->fetch_assoc();
        $failed_attempts = $data['fail_count'];

        if ($failed_attempts >= 3) {
            $message = "🚫 Too many failed login attempts, Please try again later.";
        } else {
            // Get user from DB
            $sql = "SELECT * FROM users WHERE username = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();

            if ($user && password_verify($password, $user['password'])) {
                // Record success
                $insert = "INSERT INTO login_attempts (username, is_successful) VALUES (?, 1)";
                $stmt = $conn->prepare($insert);
                $stmt->bind_param("s", $username);
                $stmt->execute();

                $_SESSION['username'] = $username;
                header("Location: dashboard.php");
                exit;
            } else {
                // Record failed attempt
                $insert = "INSERT INTO login_attempts (username, is_successful) VALUES (?, 0)";
                $stmt = $conn->prepare($insert);
                $stmt->bind_param("s", $username);
                $stmt->execute();

                // Log notification only once on 3rd failed attempt
                if ($failed_attempts == 2) {
                    $notify = "INSERT INTO notifications (username, message) VALUES (?, 'User locked after 3 failed attempts')";
                    $stmt = $conn->prepare($notify);
                    $stmt->bind_param("s", $username);
                    $stmt->execute();
                }

                $message = "❌ Invalid login for <b>$username</b>. Attempt " . ($failed_attempts + 1) . "/3";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            margin-top: 100px;
        }
        .card {
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
<div class="container login-container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <h3 class="card-title text-center mb-4">Login</h3>
                
                <?php if ($message != ''): ?>
                    <div class="alert alert-warning"><?php echo $message; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>

                    <button type="submit" class="btn w-100" style="background-color: #28282B; color: #fff;">Login</button>
                </form>

                <div class="mt-3 text-center">
                    <!-- <a href="register.php">Reset Password</a> -->
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
