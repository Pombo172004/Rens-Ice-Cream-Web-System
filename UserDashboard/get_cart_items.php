<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['items' => []]);
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo_makmak1->prepare("
    SELECT c.product_id, c.quantity, p.name as product_name, p.price, p.image
    FROM cart c
    JOIN products p ON c.product_id = p.product_id
    WHERE c.user_id = ?
");
$stmt->execute([$user_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['items' => $items]); 