<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);
$product_id = $data['product_id'];

$stmt = $pdo_makmak1->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
if ($stmt->execute([$user_id, $product_id])) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to remove item']);
} 