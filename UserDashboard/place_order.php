<?php
session_start();
require '../includes/db.php';
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}
$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);

// Get selected item IDs from the request body
$selected_item_ids = $data['selected_item_ids'] ?? [];

if (empty($selected_item_ids)) {
    echo json_encode(['status' => 'error', 'message' => 'No items selected for order.']);
    exit;
}

// Sanitize the IDs to ensure they are integers
$selected_item_ids = array_map('intval', $selected_item_ids);

// Create a string of placeholders for the SQL query
$placeholders = rtrim(str_repeat('?, ', count($selected_item_ids)), ', ');

// Fetch selected cart items for this user
$stmt = $pdo_makmak1->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id IN ($placeholders)");

// Prepare the parameters for the execute method
$execute_params = array_merge([$user_id], $selected_item_ids);

$stmt->execute($execute_params);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($cart_items)) {
    echo json_encode(['status' => 'error', 'message' => 'Selected items not found in cart.']);
    exit;
}

$success = true;
$subtotal = 0;

// Calculate subtotal first
foreach ($cart_items as $item) {
    // Fetch price for the product
    $stmtP = $pdo_makmak1->prepare('SELECT price FROM products WHERE product_id = ?');
    $stmtP->execute([$item['product_id']]);
    $product = $stmtP->fetch(PDO::FETCH_ASSOC);
    $price = $product ? $product['price'] : 0;
    $subtotal += $item['quantity'] * $price;
}

// Determine shipping fee
$shipping_fee = $subtotal > 0 ? 50 : 0;

// Now insert into orders table with shipping fee
foreach ($cart_items as $item) {
    // Fetch price again to get individual item total_price for the order item row
    $stmtP = $pdo_makmak1->prepare('SELECT price FROM products WHERE product_id = ?');
    $stmtP->execute([$item['product_id']]);
    $product = $stmtP->fetch(PDO::FETCH_ASSOC);
    $price = $product ? $product['price'] : 0;
    $item_total_price = $item['quantity'] * $price;

    $stmtB = $pdo_makmak1->prepare('INSERT INTO orders (user_id, product_id, quantity, total_price, shipping_fee, status) VALUES (?, ?, ?, ?, ?, ?)');
    $ok = $stmtB->execute([$user_id, $item['product_id'], $item['quantity'], $item_total_price, $shipping_fee, 'pending']);
    if (!$ok) $success = false;
}

if ($success) {
    // Delete only the selected items from the cart
    $delete_placeholders = rtrim(str_repeat('?, ', count($selected_item_ids)), ', ');
    $stmtDelete = $pdo_makmak1->prepare("DELETE FROM cart WHERE user_id = ? AND product_id IN ($delete_placeholders)");
    $delete_params = array_merge([$user_id], $selected_item_ids);
    $stmtDelete->execute($delete_params);

    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to place order']);
} 