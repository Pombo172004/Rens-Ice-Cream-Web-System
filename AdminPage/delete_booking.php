<?php
require '../includes/db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    $booking_id = $_POST['booking_id'];
    $stmt = $pdo_makmak1->prepare('DELETE FROM bookings WHERE id = ?');
    $stmt->execute([$booking_id]);
}
header('Location: BookingsOrders.php');
exit(); 