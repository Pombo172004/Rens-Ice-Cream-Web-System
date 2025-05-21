<?php

session_start();

require '../includes/db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    $stmt = $pdo_makmak1->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $reset_code = rand(100000, 999999);

        $update = $pdo_makmak1->prepare("UPDATE users SET reset_code = ? WHERE email = ?");
        $update->execute([$reset_code, $email]);

        $_SESSION['email'] = $email;

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'christiandavepombo@gmail.com';
            $mail->Password   = 'xxqv tije djrh giuk'; // Consider storing this securely!
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('christiandavepombo@gmail.com', 'Christian Dave Pombo');
            $mail->addAddress($email, 'User');

            $mail->isHTML(true);
            $mail->Subject = "Password Reset Code";
            $mail->Body    = "<p>Hello, this is your password reset code: <strong>{$reset_code}</strong></p>";
            $mail->AltBody = "Hello, use the code below to reset your password:\n\n{$reset_code}";

            $mail->send();

            $_SESSION['email_sent'] = true;
            $_SESSION['success'] = "A verification code has been sent to your email.";
            header('Location: send-code.php');
            exit();

        } catch (Exception $e) {
            $_SESSION['error'] = "Message could not be sent. Please try again.";
            header('Location: forgotpassword.php');
            exit();
        }

    } else {
        $_SESSION['error'] = "No user found with that email.";
        header('Location: forgotpassword.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Forgot Password</title>
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
        .forgot-card {
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
        .forgot-card .logo {
            width: 70px;
            height: 70px;
            object-fit: contain;
            margin-bottom: 18px;
            border-radius: 50%;
            background: #f3f3f3;
            display: inline-block;
        }
        .forgot-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 18px;
            color: #222;
        }
        .forgot-desc {
            color: #222;
            font-size: 1.05em;
            margin-bottom: 22px;
        }
        .alert-success, .alert-info {
            margin-bottom: 18px;
            border-radius: 6px;
            padding: 10px 0;
            font-size: 1em;
        }
        .alert-success { background: #e8f5e9; color: #388e3c; border: 1px solid #81c784; }
        .alert-info { background: #fff3cd; color: #856404; border: 1px solid #ffe082; }
        .forgot-form input.form-control {
            width: 100%;
            margin-bottom: 18px;
        }
        .forgot-form button.btn {
            width: 100%;
        }
    </style>
</head>
<body>

<div class="forgot-card">
    <img src="../Images/ForgotLogo.png" class="logo" alt="logo" onerror="this.onerror=null;this.src='https://via.placeholder.com/70?text=Logo';">
    <div class="forgot-title">Forgot Password</div>
    <div class="forgot-desc">Enter your email address and we'll send you a verification code to reset your password.</div>

    <!-- PHP alerts -->
    <?php
    if (isset($_SESSION['success'])) {
        echo '<div class="alert-success text-center">' . $_SESSION['success'] . '</div>';
        unset($_SESSION['success']);
    }

    if (isset($_SESSION['error'])) {
        echo '<div class="alert-info text-center">' . $_SESSION['error'] . '</div>';
        unset($_SESSION['error']);
    }
    ?>

    <form action="forgotpassword.php" method="POST" class="forgot-form">
        <input type="email" name="email" class="form-control mb-3" placeholder="Enter your email" required>
        <button type="submit" class="btn btn-primary w-100">Send Verification Code</button>
    </form>
</div>

</body>
</html>
