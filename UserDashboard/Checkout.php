<?php
session_start();
require '../includes/db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ../LoginPage/Userlogin.php');
    exit();
}
$user_id = $_SESSION['user_id'];

// Get selected item IDs from URL parameter
$selected_items_string = $_GET['selected_items'] ?? '';
$selected_item_ids = [];
$cart_items = [];
$subtotal = 0;
$delivery_fee = 0;
$total = 0;

if (!empty($selected_items_string)) {
    $selected_item_ids = explode(',', $selected_items_string);
    // Sanitize the IDs to ensure they are integers
    $selected_item_ids = array_map('intval', $selected_item_ids);

    // Create a string of placeholders for the SQL query
    $placeholders = rtrim(str_repeat('?, ', count($selected_item_ids)), ', ');

    // Fetch selected products from cart
    $stmt = $pdo_makmak1->prepare("SELECT c.*, p.name, p.price, p.image 
                                 FROM cart c 
                                 JOIN products p ON c.product_id = p.product_id 
                                 WHERE c.user_id = ? AND c.product_id IN ($placeholders)");
    
    // Prepare the parameters for the execute method
    $execute_params = array_merge([$user_id], $selected_item_ids);

    $stmt->execute($execute_params);
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate subtotal, delivery fee, and total for selected items
    foreach ($cart_items as $item) {
        $subtotal += $item['quantity'] * $item['price'];
    }
    $delivery_fee = $subtotal > 0 ? 50 : 0;
    $total = $subtotal + $delivery_fee;
} else {
    // Handle case where no items are selected (optional: display a message)
    $message = "No items selected for checkout.";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Makmak</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
      /* Basic Modal Styles (can be merged with existing modal styles if any) */
      .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0; top: 0; width: 100vw; height: 100vh;
        background: rgba(0,0,0,0.25);
        justify-content: center;
        align-items: center;
      }
      .modal.active {
        display: flex !important;
        justify-content: center;
        align-items: center;
      }
      .modal-content {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 2px 12px rgba(44,62,80,0.12);
        padding: 32px 28px;
        min-width: 320px;
        text-align: center;
        /* Use absolute positioning and transform for centering */
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        max-width: 95vw;
        /* Remove margin: auto as it's not needed with absolute positioning */
        /* margin: auto; */
      }
      .modal-title {
        font-size: 1.3em;
        font-weight: bold;
        margin-bottom: 18px;
        color: #222;
      }
      .modal-close {
        position: absolute;
        top: 10px; right: 18px;
        font-size: 1.5em;
        color: #888;
        cursor: pointer;
      }
      .modal-btn {
        padding: 10px 28px;
        background: #007bff;
        color: #fff;
        border-radius: 6px;
        border: none;
        font-size: 1.1em;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.2s;
        margin-top: 10px;
      }
      .modal-btn:hover { background: #0056b3; }
    </style>
</head>
<body>
<div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo"></div>
        <nav class="nav">
            <a href="Dashboard.php"><i class="fas fa-chart-pie"></i> Home</a>
            <a href="Booking.php"><i class="fas fa-calendar-check"></i> Book Ice Cream</a>
            <a href="Cart.php"><i class="fas fa-shopping-cart"></i> Your Cart</a>
            <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
            <a href="../LoginPage/Userlogin.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </aside>
    <!-- Main Content -->
    <main class="main">
        <div class="card" style="max-width:900px;margin:40px auto;">
            <h2 style="margin-bottom: 24px;">Checkout</h2>
            <?php if (empty($cart_items)): ?>
                <p style="color:#888;">No products to checkout.</p>
            <?php else: ?>
                <?php foreach ($cart_items as $item): ?>
                <div class="cart-item" style="display:flex;align-items:center;gap:24px;margin-bottom:24px;background:#fff;padding:18px 24px;border-radius:12px;box-shadow:0 2px 8px rgba(44,62,80,0.08);">
                    <img src="../Images/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" style="width:90px;height:90px;object-fit:cover;border-radius:10px;">
                    <div style="flex:1;">
                        <h3 style="margin:0 0 8px 0;"><?= htmlspecialchars($item['name']) ?></h3>
                        <div style="color:#888;">Quantity: <b><?= $item['quantity'] ?></b></div>
                    </div>
                    <div style="font-size:1.2em;font-weight:bold;">Php <?= number_format($item['price'] * $item['quantity'], 2) ?></div>
                </div>
                <?php endforeach; ?>
                <div class="order-summary" style="margin-top:32px;background:#f8f9fa;padding:24px 32px;border-radius:10px;max-width:500px;margin-left:auto;margin-right:auto;">
                    <h3 style="margin-bottom:18px;">Order Summary</h3>
                    <div style="display:flex;justify-content:space-between;margin-bottom:10px;">
                        <span>Subtotal</span>
                        <span>Php <?= number_format($subtotal, 2) ?></span>
                    </div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:10px;">
                        <span>Delivery Fee</span>
                        <span>Php <?= number_format($delivery_fee, 2) ?></span>
                    </div>
                    <div style="display:flex;justify-content:space-between;font-weight:bold;font-size:1.2em;">
                        <span>Total</span>
                        <span>Php <?= number_format($total, 2) ?></span>
                    </div>
                </div>
                <button id="confirmOrderBtn" class="checkout-btn" style="margin: 32px auto 0 auto; display: block;">Confirm Order</button>
            <?php endif; ?>
        </div>
    </main>
</div>

<!-- Checkout Confirmation Modal -->
<div class="modal" id="checkoutStatusModal">
  <div class="modal-content">
    <span class="modal-close" onclick="closeCheckoutStatusModal()">&times;</span>
    <div class="modal-title" id="checkoutModalTitle">Status</div>
    <div id="checkoutModalMessage" style="margin-bottom: 20px; color: #444;"></div>
    <button class="modal-btn" onclick="closeCheckoutStatusModal()">OK</button>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const confirmBtn = document.getElementById('confirmOrderBtn');
  if (confirmBtn) {
    confirmBtn.addEventListener('click', function() {
      fetch('place_order.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ selected_item_ids: <?= json_encode($selected_item_ids ?? []) ?> })
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          showCheckoutStatusModal('Order Placed', 'Order placed! Waiting for admin approval.', true);
        } else {
          showCheckoutStatusModal('Order Failed', data.message || 'Order failed.', false);
        }
      });
    });
  }
});

function showCheckoutStatusModal(title, message, isSuccess) {
  document.getElementById('checkoutModalTitle').textContent = title;
  document.getElementById('checkoutModalMessage').textContent = message;
  document.getElementById('checkoutStatusModal').classList.add('active');
  if (isSuccess) {
    // If success, redirect after modal is closed
    document.querySelector('#checkoutStatusModal .modal-btn').onclick = function() {
      closeCheckoutStatusModal();
      window.location.href = 'Dashboard.php';
    };
  } else {
    // If failure, just close the modal
    document.querySelector('#checkoutStatusModal .modal-btn').onclick = function() {
      closeCheckoutStatusModal();
    };
  }
}

function closeCheckoutStatusModal() {
  document.getElementById('checkoutStatusModal').classList.remove('active');
}
</script>
</body>
</html> 