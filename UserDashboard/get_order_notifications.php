<?php
session_start();
require '../includes/db.php';
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'notifications' => []]);
    exit;
}
$user_id = $_SESSION['user_id'];
$stmt = $pdo_makmak1->prepare("SELECT * FROM order_notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
$stmt->execute([$user_id]);
$notifs = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(['status' => 'success', 'notifications' => $notifs]); 