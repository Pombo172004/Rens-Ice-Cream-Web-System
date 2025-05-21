<?php
session_start();
require '../includes/db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);
    $stmt = $pdo_makmak1->prepare('DELETE FROM orders WHERE id = ?');
    if ($stmt->execute([$order_id])) {
        $_SESSION['admin_msg'] = 'Order deleted successfully!';
    } else {
        $_SESSION['admin_msg'] = 'Failed to delete order.';
    }
}
header('Location: BookingsOrders.php');
exit; 