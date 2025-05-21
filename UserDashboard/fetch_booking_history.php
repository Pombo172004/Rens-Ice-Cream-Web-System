<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in.']);
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // Fetch user bookings with product name
    $stmt = $pdo_makmak1->prepare("SELECT b.*, p.name AS product_name FROM bookings b JOIN products p ON b.product_id = p.product_id WHERE b.user_id = :user_id ORDER BY b.id DESC");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($bookings);

} catch (PDOException $e) {
    // Log the error for debugging
    error_log('Database error fetching booking history: ' . $e->getMessage());
    echo json_encode(['error' => 'Database error fetching history.']);
} catch (Exception $e) {
    // Catch any other unexpected errors
    error_log('Unexpected error fetching booking history: ' . $e->getMessage());
    echo json_encode(['error' => 'An unexpected error occurred.']);
}
?> 