<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/../includes/db.php';

// Check if database connection is valid
if (!$pdo_makmak1) {
    echo 'error: Database connection failed.';
    exit();
}

if (!isset($_SESSION['user_id'])) {
    echo 'error: user not logged in';
    exit();
}

if (!isset($_POST['order_id']) || !isset($_POST['rating'])) {
    echo 'error: missing order_id or rating';
    exit();
}

$order_id = $_POST['order_id'];
$rating = $_POST['rating'];
$user_id = $_SESSION['user_id'];

// Validate rating
if ($rating < 1 || $rating > 5) {
    echo 'error: invalid rating value';
    exit();
}

try {
    // First verify that this order belongs to the user and is in a state where it can be reviewed (e.g., 'received')
    $stmt = $pdo_makmak1->prepare("SELECT * FROM orders WHERE id = :order_id AND user_id = :user_id AND status = 'received'");
    $stmt->execute([
        ':order_id' => $order_id,
        ':user_id' => $user_id
    ]);
    
    if ($stmt->rowCount() === 0) {
        error_log('Submit review failed: Order not found, not for this user, or not in received status.', 0, 'error_log.txt');
        echo 'error: Order not found, not for this user, or not received.';
        exit();
    }

    // Update the order with the rating
    // Only attempt update if rating is currently null
    $updateStmt = $pdo_makmak1->prepare("UPDATE orders SET rating = :rating WHERE id = :order_id AND user_id = :user_id AND rating IS NULL");
    $updateStmt->execute([
        ':rating' => $rating,
        ':order_id' => $order_id,
        ':user_id' => $user_id
    ]);

    // We echo success if the order is now rated, regardless of whether it was just updated or already had a rating.
    echo 'success';

} catch (PDOException $e) {
    error_log('Database error submitting review: ' . $e->getMessage(), 0, 'error_log.txt');
    echo 'error: Database error: ' . $e->getMessage();
} catch (Exception $e) {
     error_log('Unexpected error submitting review: ' . $e->getMessage(), 0, 'error_log.txt');
     echo 'error: An unexpected error occurred: ' . $e->getMessage();
}
?> 