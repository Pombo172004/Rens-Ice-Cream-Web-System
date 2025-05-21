<?php

session_start();

require '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // reCAPTCHA validation
    $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';
    $secretKey = '6Lcr9kIrAAAAAMT0rT92OmrxcS5_dC0mnSNepWeA'; // Replace with your actual Secret Key

    $verifyUrl = 'https://www.google.com/recaptcha/api/siteverify';
    $responseData = file_get_contents($verifyUrl . '?secret=' . $secretKey . '&response=' . $recaptchaResponse);
    $response = json_decode($responseData);

    if (!$response->success) {
        // CAPTCHA verification failed
        $_SESSION['error'] = "Please complete the CAPTCHA.";
        header('Location: ../LoginPage/Userlogin.php');
        exit();
    }

    // CAPTCHA verification successful, proceed with login
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Corrected variable name from $stmp to $stmt
    $stmt = $pdo_makmak1->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        // You can optionally store user session dahere if needed
        header('Location: ../UserDashboard/Dashboard.php');
        exit();
    } else {
        $_SESSION['error'] = "Invalid username and password.";
        header('Location: ../LoginPage/Userlogin.php');
        exit();
    }
}
