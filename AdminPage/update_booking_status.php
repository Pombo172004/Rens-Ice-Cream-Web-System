<?php
require '../includes/db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = $_POST['booking_id'];
    $action = $_POST['action']; // 'approved' or 'denied'
    $stmt = $pdo_makmak1->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    $stmt->execute([$action, $booking_id]);

    // Fetch user_id, product_id, and quantity for this booking
    $stmtUser = $pdo_makmak1->prepare("SELECT user_id, product_id, quantity FROM bookings WHERE id = ?");
    $stmtUser->execute([$booking_id]);
    $user = $stmtUser->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        $user_id = $user['user_id'];
        $message = $action === 'approved' ? 'Your booking has been approved!' : 'Your booking has been denied.';
        $stmtNotif = $pdo_makmak1->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
        $stmtNotif->execute([$user_id, $message]);
        // Only decrease stock if approved
        if ($action === 'approved') {
            $stmtStock = $pdo_makmak1->prepare("UPDATE products SET stock = stock - ? WHERE product_id = ? AND stock >= ?");
            $stmtStock->execute([$user['quantity'], $user['product_id'], $user['quantity']]);
        }
    }
    $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'BookingsOrders.php';
    header('Location: ' . $redirect);
    exit();
} 