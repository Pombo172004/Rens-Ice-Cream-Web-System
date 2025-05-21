<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'];
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

// Check if product exists and get price/name
$stmt = $pdo_makmak1->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo json_encode(['status' => 'error', 'message' => 'Product not found']);
    exit;
}
$product_name = $product['name'];

// Check if already in cart
$stmt = $pdo_makmak1->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
$stmt->execute([$user_id, $product_id]);
if ($stmt->rowCount() > 0) {
    // Update quantity
    $stmt = $pdo_makmak1->prepare("UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$quantity, $user_id, $product_id]);
} else {
    // Insert new with product name
    $stmt = $pdo_makmak1->prepare("INSERT INTO cart (user_id, product_id, product_name, quantity) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $product_id, $product_name, $quantity]);
}

echo json_encode(['status' => 'success']); 