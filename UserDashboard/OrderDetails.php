<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ../LoginPage/Login.php');
    exit();
}
$user_id = $_SESSION['user_id'];
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Try to fetch from orders table first
$stmt = $pdo_makmak1->prepare("SELECT o.*, p.name AS product_name, p.price, p.description FROM orders o JOIN products p ON o.product_id = p.product_id WHERE o.user_id = :user_id AND o.id = :order_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->bindParam(':order_id', $order_id);
$stmt->execute();
$order = $stmt->fetch(PDO::FETCH_ASSOC);
// If not found, try bookings (for bookings section)
if (!$order) {
    $stmt = $pdo_makmak1->prepare("SELECT b.*, p.name AS product_name, p.price, p.description FROM bookings b JOIN products p ON b.product_id = p.product_id WHERE b.user_id = :user_id AND b.id = :order_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':order_id', $order_id);
    $stmt->execute();
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
}
if (!$order) {
    echo '<p style="color:red;text-align:center;margin-top:40px;">Order not found or access denied.</p>';
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Details</title>
  <link rel="stylesheet" href="style.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    .details-container {
      max-width: 500px;
      margin: 40px auto;
      background: #fff;
      border-radius: 14px;
      box-shadow: 0 2px 12px rgba(44,62,80,0.08);
      padding: 32px 28px;
    }
    .details-title {
      font-size: 1.7em;
      font-weight: bold;
      margin-bottom: 18px;
      color: #222;
      text-align: center;
    }
    .details-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 14px;
      font-size: 1.08em;
    }
    .details-label {
      color: #888;
      font-weight: 500;
    }
    .details-value {
      color: #222;
      font-weight: 600;
    }
    .status-pending { color: #f39c12; font-weight: bold; }
    .status-approved { color: #27ae60; font-weight: bold; }
    .status-denied { color: #e74c3c; font-weight: bold; }
    .back-btn-wrapper {
      display: flex;
      justify-content: center;
      margin-top: 32px;
    }
    .back-btn {
      padding: 8px 22px;
      background: #007bff;
      color: #fff;
      border-radius: 6px;
      text-decoration: none;
      font-weight: 500;
      transition: background 0.2s;
      border: none;
      font-size: 1em;
    }
    .back-btn:hover { background: #0056b3; }
  </style>
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="logo"></div>
      <nav class="nav">
        <a href="Dashboard.php"><i class="fas fa-home"></i> Home</a>
        <a href="Booking.php"><i class="fas fa-calendar-check"></i> Book Ice Cream</a>
        <a href="Cart.php"><i class="fas fa-shopping-cart"></i> Your Cart</a>
        <a href="MyOrders.php" class="active"><i class="fas fa-clipboard-list"></i> My Orders</a>
        <a href="Profile.php"><i class="fas fa-user"></i> Profile</a>
        <a href="../LoginPage/Login.php?logout=1"><i class="fas fa-sign-out-alt"></i> Logout</a>
      </nav>
    </aside>
    <main class="main">
      <div class="details-container">
        <div class="details-title">Order Details</div>
        <div class="details-row"><span class="details-label">Order ID:</span> <span class="details-value">#<?= $order['id'] ?></span></div>
        <div class="details-row"><span class="details-label">Product:</span> <span class="details-value"><?= htmlspecialchars($order['product_name']) ?></span></div>
        <div class="details-row"><span class="details-label">Description:</span> <span class="details-value"><?= htmlspecialchars($order['description']) ?></span></div>
        <div class="details-row"><span class="details-label">Quantity:</span> <span class="details-value"><?= $order['quantity'] ?></span></div>
        <div class="details-row"><span class="details-label">Price per item:</span> <span class="details-value">Php <?= number_format($order['price'], 2) ?></span></div>
        
        <?php if (!empty($order['shipping_fee'])): // Display shipping fee if available ?>
          <div class="details-row"><span class="details-label">Shipping Fee:</span> <span class="details-value">Php <?= number_format($order['shipping_fee'], 2) ?></span></div>
        <?php endif; ?>

        <div class="details-row"><span class="details-label">Total Payment:</span> <span class="details-value">Php <?= number_format(($order['quantity'] * $order['price']) + ($order['shipping_fee'] ?? 0), 2) ?></span></div>
        <div class="details-row"><span class="details-label">Order Date:</span> <span class="details-value"><?php
          // Use 'order_date' for orders, fallback to 'created_at' or 'booking_date' for others
          $orderDate = $order['order_date'] ?? $order['created_at'] ?? $order['booking_date'] ?? '';
          if (!empty($orderDate) && strtotime($orderDate) !== false) {
            echo date('Y-m-d', strtotime($orderDate));
          } else {
            echo 'N/A';
          }
        ?></span></div>
        <div class="details-row"><span class="details-label">Status:</span> <span class="details-value status-<?= htmlspecialchars($order['status']) ?>"><?= ucfirst($order['status']) ?></span></div>
        <?php if (!empty($order['delivery_date'])): ?>
          <div class="details-row"><span class="details-label">Delivery Date:</span> <span class="details-value"><?= htmlspecialchars($order['delivery_date']) ?></span></div>
        <?php endif; ?>
        <?php if (!empty($order['delivery_time'])): ?>
          <div class="details-row"><span class="details-label">Delivery Time:</span> <span class="details-value"><?= htmlspecialchars($order['delivery_time']) ?></span></div>
        <?php endif; ?>
        <div class="back-btn-wrapper">
          <a class="back-btn" href="MyOrders.php">&larr; Back to My Orders</a>
        </div>
      </div>
    </main>
  </div>
</body>
</html> 