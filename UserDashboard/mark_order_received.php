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

if (!isset($_POST['order_id'])) {
    echo 'error: missing order_id';
    exit();
}

$order_id = $_POST['order_id'];
$user_id = $_SESSION['user_id'];

try {
    // Verify that this order belongs to the user and is either approved or already received
    $stmt = $pdo_makmak1->prepare("SELECT * FROM orders WHERE id = :order_id AND user_id = :user_id AND (status = 'approved' OR status = 'received')");
    $stmt->execute([
        ':order_id' => $order_id,
        ':user_id' => $user_id
    ]);

    if ($stmt->rowCount() === 0) {
        // Log this specific case as it's not a PDO error
        error_log('Mark order received failed: Order not found or not for this user or not in approved/received status.', 0, 'error_log.txt'); // Log to a file
        echo 'error: Order not found, not for this user, or not in approved status.'; // Keep message consistent with previous
        exit();
    }

    // Get the current status
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    $current_status = $order['status'];

    // Only update if the status is currently 'approved'
    if ($current_status === 'approved') {
        // Update the order status to received
        $updateStmt = $pdo_makmak1->prepare("UPDATE orders SET status = 'received' WHERE id = :order_id");
        $updateStmt->execute([':order_id' => $order_id]);
    }

    // If we reached here, the request is successful in that the order is now either received or was already received
    echo 'success';

} catch (PDOException $e) {
    // Log the PDO error and output a detailed message
    error_log('Database error marking order as received: ' . $e->getMessage(), 0, 'error_log.txt'); // Log to a file
    echo 'error: Database error: ' . $e->getMessage(); // Include the PDO error message
} catch (Exception $e) {
     // Catch any other unexpected errors
     error_log('Unexpected error marking order as received: ' . $e->getMessage(), 0, 'error_log.txt'); // Log to a file
     echo 'error: An unexpected error occurred: ' . $e->getMessage();
}
?> 