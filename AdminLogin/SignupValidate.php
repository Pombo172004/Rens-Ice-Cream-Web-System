<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

try {
    // Get form data
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate input
    $errors = [];
    
    // Check if username already exists
    $stmt = $pdo_makmak1->prepare("SELECT COUNT(*) FROM admin WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    if ($stmt->fetchColumn() > 0) {
        $errors[] = "Username already exists";
    }
    
    // Check if email already exists
    $stmt = $pdo_makmak1->prepare("SELECT COUNT(*) FROM admin WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    if ($stmt->fetchColumn() > 0) {
        $errors[] = "Email already exists";
    }
    
    // Validate password
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    }
    if (!preg_match("/[0-9]/", $password)) {
        $errors[] = "Password must contain at least one number";
    }
    if (!preg_match("/[^A-Za-z0-9]/", $password)) {
        $errors[] = "Password must contain at least one special character";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }
    
    // If there are errors, redirect back to signup page
    if (!empty($errors)) {
        $_SESSION['error'] = implode("<br>", $errors);
        header("Location: AdminSignup.php");
        exit();
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new admin
    $stmt = $pdo_makmak1->prepare("INSERT INTO admin (username, password, email, full_name, status) VALUES (:username, :password, :email, :full_name, 'active')");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $hashed_password);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':full_name', $full_name);
    $stmt->execute();
    
    // Set success message and redirect to login
    $_SESSION['success'] = "Registration successful! Please login with your credentials.";
    header("Location: AdminLogin.php");
    exit();
    
} catch(PDOException $e) {
    $_SESSION['error'] = "Registration failed: " . $e->getMessage();
    header("Location: AdminSignup.php");
    exit();
}
?> 