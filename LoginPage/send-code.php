<?php

session_start();

require '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $enteredCode = $_POST['code']; // This is from the form in our HTML
    $email = $_SESSION['email']; // We store the email from the forgot password page in a session

    if (!isset($_SESSION['email'])) {
        $_SESSION['error'] = "No email session found. Please try again.";
        header('Location: forgotpassword.php');
        exit();
    }

    // Fetch the code from the database
    $stmt = $pdo_makmak1->prepare("SELECT reset_code FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if ($enteredCode === $user['reset_code']) {
            // Store session data for next step
            $_SESSION['reset_email'] = $email;
            $_SESSION['reset_code_verified'] = true;
            $_SESSION['success'] = "Code has been verified!";
            
            header('Location: nw-password.php');
            exit();
        } else {
            $_SESSION['error'] = "Invalid code. Please try again.";
        }
    } else {
        $_SESSION['error'] = "No user found with that email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Verify Code</title>
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
        .verify-card {
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
        .verify-card .logo {
            width: 70px;
            height: 70px;
            object-fit: contain;
            margin-bottom: 18px;
            border-radius: 50%;
            background: #f3f3f3;
            display: inline-block;
        }
        .verify-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 18px;
            color: #222;
        }
        .verify-desc {
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
        .verify-form input[type="number"] {
            width: 100%;
            padding: 12px 14px;
            margin-bottom: 18px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            font-size: 1rem;
            background: #f5f7fa;
            transition: border 0.2s;
        }
        .verify-form input[type="number"]:focus {
            border: 1.5px solid #7b2ff2;
            outline: none;
            background: #fff;
        }
        .verify-form button {
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
        .verify-form button:hover {
            background: #377739;
            color: #fff;
        }
    </style>
</head>

<body>
    <div class="verify-card">
        <img src="../Images/VerifyLogo.png" class="logo" alt="Logo" onerror="this.onerror=null;this.src='https://via.placeholder.com/70?text=Logo';">
        <div class="verify-title">Verify Code</div>
        <div class="verify-desc">Please enter the verification code sent to your email.</div>
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
        <form action="send-code.php" method="POST" class="verify-form">
            <input type="number" placeholder="Enter the code" name="code" required>
            <button type="submit">Verify Code</button>
        </form>
    </div>
</body>

</html>
