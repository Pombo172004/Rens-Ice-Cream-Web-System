<?php

session_start();

require '../includes/db.php';

if (!isset($_SESSION['email']) || !isset($_SESSION['reset_code_verified']) || !$_SESSION['reset_code_verified']) {
    header('Location: send-code.php');
    exit();
}

// This is where we reset the password
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Passed the value of our form into variables
    $newPassword = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword === $confirmPassword) {
        // Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

        // Update the user's password
        $stmt = $pdo_makmak1->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->execute([$hashedPassword, $_SESSION['reset_email']]);

        // Unset session variables
        unset($_SESSION['reset_email']);
        unset($_SESSION['reset_code_verified']);

        // Redirect to login page with success message
        $_SESSION['success'] = "Your password has been reset successfully.";
        header('Location: Userlogin.php');
        exit();
    } else {
        $_SESSION['error'] = "Passwords do not match. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="styles.css">
    <meta charset="UTF-8">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #e0e7ff 0%, #f3f3f3 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .reset-card {
            background: linear-gradient(135deg, #4CAF50 0%, #2196F3 100%);
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(44, 62, 80, 0.15);
            padding: 40px 32px 32px 32px;
            max-width: 370px;
            width: 100%;
            text-align: center;
            animation: fadeIn 0.7s;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .reset-card .logo {
            width: 70px;
            height: 70px;
            object-fit: contain;
            margin-bottom: 18px;
            border-radius: 50%;
            background: #f3f3f3;
            display: inline-block;
        }
        .reset-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 18px;
            color: #222;
        }
        .reset-desc {
            color: #222;
            font-size: 1.05em;
            margin-bottom: 22px;
        }
        .alert-success, .alert-danger {
            margin-bottom: 18px;
            border-radius: 6px;
            padding: 10px 0;
            font-size: 1em;
        }
        .alert-success { background: #e8f5e9; color: #388e3c; border: 1px solid #81c784; }
        .alert-danger { background: #ffebee; color: #c62828; border: 1px solid #e57373; }
        .reset-form input[type="password"] {
            width: 100%;
            padding: 12px 14px;
            margin-bottom: 18px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            font-size: 1rem;
            background: #f5f7fa;
            transition: border 0.2s;
        }
        .reset-form input[type="password"]:focus {
            border: 1.5px solid #7b2ff2;
            outline: none;
            background: #fff;
        }
        .reset-form button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            background: #4CAF50;
            color: #222;
            font-size: 1.1rem;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(44, 62, 80, 0.08);
            transition: background 0.3s, color 0.3s;
        }
        .reset-form button:hover {
            background: #377739;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="reset-card">
        <img src="../Images/NewLogo.png" class="logo" alt="Logo" onerror="this.onerror=null;this.src='https://via.placeholder.com/70?text=Logo';">
        <div class="reset-title">Reset Password</div>
        <div class="reset-desc">Enter your new password below. Make sure it's strong and secure.</div>
        <?php
        if (isset($_SESSION['success'])) {
            echo '<div class="alert-success text-center">' . $_SESSION['success'] . '</div>';
            unset($_SESSION['success']);
        }
        if (isset($_SESSION['error'])) {
            echo '<div class="alert-danger text-center">' . $_SESSION['error'] . '</div>';
            unset($_SESSION['error']);
        }
        ?>
        <form action="nw-password.php" method="POST" class="reset-form">
            <input type="password" placeholder="New password" name="password" required>
            <input type="password" placeholder="Confirm password" name="confirm_password" required>
            <button type="submit">Change Password</button>
        </form>
    </div>
</body>
</html>
