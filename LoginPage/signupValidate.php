<?php

session_start();

require '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['Password'];
    $confirm = $_POST['confirmPassword'];

    if ($password != $confirm) {
        $_SESSION['ERROR'] = "Password didn't match";
        header('Location: ../LoginPage/Usersignin.php');
        exit();
    }

    $stmt = $pdo_makmak1->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['ERROR'] = "Username already exists";
        header('Location: ../LoginPage/Usersignin.php');
        exit();      
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo_makmak1->prepare("INSERT INTO users (firstname, lastname, username, email, password) VALUES (?, ?, ?, ?, ?)");

    if ($stmt->execute([$firstname, $lastname, $username, $email, $hashedPassword])) {
        $_SESSION['success'] = "Your account has been created. You can now login."; // fixed 'sucess' and 'acount'
        header('Location: ../LoginPage/Userlogin.php');
        exit();
    } else {
        echo "There is an error";
        exit();
    }
}
