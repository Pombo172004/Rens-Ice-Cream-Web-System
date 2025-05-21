<?php
session_start();
require '../includes/db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['action'])) {
    $order_id = intval($_POST['order_id']);
    $action = $_POST['action'];
    $allowed = ['approved', 'denied', 'pending'];
    if (in_array($action, $allowed)) {
        $stmt = $pdo_makmak1->prepare('UPDATE orders SET status = ? WHERE id = ?');
        if ($stmt->execute([$action, $order_id])) {
            // If order is approved, decrement product stock
            if ($action === 'approved') {
                // Fetch products and quantities for this order from the orders table
                $stmtItems = $pdo_makmak1->prepare('SELECT product_id, quantity FROM orders WHERE id = ?');
                $stmtItems->execute([$order_id]);
                $orderItem = $stmtItems->fetch(PDO::FETCH_ASSOC);

                // Check if the order item was found before attempting to decrement stock
                if ($orderItem) {
                    $productId = $orderItem['product_id'];
                    $quantityOrdered = $orderItem['quantity'];

                    // Decrement stock in the products table
                    $stmtStock = $pdo_makmak1->prepare('UPDATE products SET stock = stock - ? WHERE product_id = ?');
                    $stmtStock->execute([$quantityOrdered, $productId]);
                }
            }

            $_SESSION['admin_msg'] = 'Order status updated successfully!';
        } else {
            $_SESSION['admin_msg'] = 'Failed to update order status.';
        }
        // Add order notification
        $stmtUser = $pdo_makmak1->prepare('SELECT user_id FROM orders WHERE id = ?');
        $stmtUser->execute([$order_id]);
        $user = $stmtUser->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $msg = 'Your order #' . $order_id . ' has been ' . $action . '.';
            $stmtNotif = $pdo_makmak1->prepare('INSERT INTO notifications (user_id, message) VALUES (?, ?)');
            $stmtNotif->execute([$user['user_id'], $msg]);
            // Debug log
            file_put_contents(__DIR__ . '/notif_debug.log', date('Y-m-d H:i:s') . " user_id={$user['user_id']} msg={$msg}\n", FILE_APPEND);
        }
    }
}
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'BookingsOrders.php';
header('Location: ' . $redirect);
exit; 