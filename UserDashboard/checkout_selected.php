<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);
$product_ids = $data['product_ids'] ?? [];

if (empty($product_ids)) {
    echo json_encode(['status' => 'error', 'message' => 'No products selected']);
    exit;
}

// Here you would process the checkout for the selected products
// For now, just return success

echo json_encode(['status' => 'success']); 