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
$quantity = intval($data['quantity']);

if ($quantity < 1) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid quantity']);
    exit;
}

$stmt = $pdo_makmak1->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
if ($stmt->execute([$quantity, $user_id, $product_id])) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update quantity']);
} 