<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

try {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $ip_address = $_SERVER['REMOTE_ADDR'];
    
    // Check if user exists
    $stmt = $pdo_makmak1->prepare("SELECT * FROM admin WHERE username = :username AND status = 'active'");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (password_verify($password, $row['password'])) {
            // Successful login
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_username'] = $row['username'];
            
            // Update last login time
            $update_stmt = $pdo_makmak1->prepare("UPDATE admin SET last_login = CURRENT_TIMESTAMP WHERE id = :id");
            $update_stmt->bindParam(':id', $row['id']);
            $update_stmt->execute();
            
            // Log successful attempt
            $log_stmt = $pdo_makmak1->prepare("INSERT INTO login_attempts (username, ip_address, status) VALUES (:username, :ip, 'success')");
            $log_stmt->bindParam(':username', $username);
            $log_stmt->bindParam(':ip', $ip_address);
            $log_stmt->execute();
            
            header("Location: ../AdminPage/BookingsOrders.php");
            exit();
        } else {
            // Log failed attempt
            $log_stmt = $pdo_makmak1->prepare("INSERT INTO login_attempts (username, ip_address, status) VALUES (:username, :ip, 'failed')");
            $log_stmt->bindParam(':username', $username);
            $log_stmt->bindParam(':ip', $ip_address);
            $log_stmt->execute();
            
            $_SESSION['error'] = "Invalid password";
            header("Location: AdminLogin.php");
            exit();
        }
    } else {
        // Log failed attempt
        $log_stmt = $pdo_makmak1->prepare("INSERT INTO login_attempts (username, ip_address, status) VALUES (:username, :ip, 'failed')");
        $log_stmt->bindParam(':username', $username);
        $log_stmt->bindParam(':ip', $ip_address);
        $log_stmt->execute();
        
        $_SESSION['error'] = "Invalid username or account is inactive";
        header("Location: AdminLogin.php");
        exit();
    }
} catch(PDOException $e) {
    $_SESSION['error'] = "Connection failed: " . $e->getMessage();
    header("Location: AdminLogin.php");
    exit();
}
?> 