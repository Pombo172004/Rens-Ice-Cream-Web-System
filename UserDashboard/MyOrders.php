<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ../LoginPage/Login.php');
    exit();
}
$user_id = $_SESSION['user_id'];

// Fetch user orders with price and shipping fee
$stmt = $pdo_makmak1->prepare("SELECT o.*, p.name AS product_name, p.price, o.shipping_fee FROM orders o JOIN products p ON o.product_id = p.product_id WHERE o.user_id = :user_id ORDER BY o.id DESC");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch user bookings
$stmtB = $pdo_makmak1->prepare("SELECT b.*, p.name AS product_name FROM bookings b JOIN products p ON b.product_id = p.product_id WHERE b.user_id = :user_id ORDER BY b.id DESC");
$stmtB->bindParam(':user_id', $user_id);
$stmtB->execute();
$bookings = $stmtB->fetchAll(PDO::FETCH_ASSOC);

function render_stars($rating) {
    $stars = '';
    for ($i = 1; $i <= 5; $i++) {
        $stars .= '<span style="color:' . ($i <= $rating ? '#ffd600' : '#ddd') . ';font-size:1.2em;">&#9733;</span>';
    }
    return $stars;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Orders</title>
  <link rel="stylesheet" href="style.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    .orders-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 32px;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 2px 12px rgba(44,62,80,0.08);
      overflow: hidden;
    }
    .orders-table th, .orders-table td {
      padding: 12px 10px;
      text-align: center;
      border-bottom: 1px solid #f0f0f0;
    }
    .orders-table th {
      background: #f6f8fb;
      color: #888;
      font-weight: 600;
    }
    .status-pending { color: #f39c12; font-weight: bold; }
    .status-approved { color: #27ae60; font-weight: bold; }
    .status-denied { color: #e74c3c; font-weight: bold; }
    .status-received { color: #2563eb; font-weight: bold; }
    .orders-title {
      font-size: 2em;
      font-weight: bold;
      margin: 32px 0 18px 0;
      color: #222;
      text-align: center;
    }
    .details-btn, .received-btn {
      padding: 6px 16px;
      background: #007bff;
      color: #fff;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 0.98em;
      transition: background 0.2s;
      margin-bottom: 2px;
      display: inline-block;
    }
    .details-btn:hover, .received-btn:hover {
      background: #0056b3;
    }
    .received-btn[disabled] {
      background: #aaa;
      cursor: not-allowed;
    }
    /* Modal styles */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0; top: 0; width: 100vw; height: 100vh;
      background: rgba(0,0,0,0.25);
      justify-content: center;
      align-items: center;
    }
    .modal.active { display: flex; }
    .modal-content {
      background: #fff;
      border-radius: 14px;
      box-shadow: 0 2px 12px rgba(44,62,80,0.12);
      padding: 32px 28px;
      min-width: 320px;
      text-align: center;
      position: relative;
    }
    .modal-title {
      font-size: 1.3em;
      font-weight: bold;
      margin-bottom: 18px;
      color: #222;
    }
    .stars {
      display: flex;
      justify-content: center;
      gap: 8px;
      margin: 18px 0 24px 0;
    }
    .star {
      font-size: 2em;
      color: #ddd;
      cursor: pointer;
      transition: color 0.2s;
    }
    .star.selected,
    .star:hover,
    .star.selected ~ .star {
      color: #ffd600;
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
    .modal-close {
      position: absolute;
      top: 10px; right: 18px;
      font-size: 1.5em;
      color: #888;
      cursor: pointer;
    }
    .review-btn {
      padding: 6px 16px;
      background: #2563eb;
      color: #fff;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 0.98em;
      transition: background 0.2s;
      margin-bottom: 2px;
      display: inline-block;
    }
    .review-btn:hover {
      background: #1746a2;
    }
    .reviewed-label {
      color: #27ae60;
      font-weight: 600;
      font-size: 1em;
      padding: 4px 10px;
      border-radius: 6px;
      background: #eafaf1;
      display: inline-block;
    }
    #reviewModal .modal-content {
      background: #fff;
      border-radius: 14px;
      box-shadow: 0 2px 12px rgba(44,62,80,0.12);
      padding: 32px 28px;
      min-width: 320px;
      text-align: center;
      position: relative;
      max-width: 95vw;
    }
    #reviewModal .modal-title {
      font-size: 1.3em;
      font-weight: bold;
      margin-bottom: 18px;
      color: #222;
    }
    #reviewModal .stars {
      display: flex;
      justify-content: center;
      gap: 8px;
      margin: 18px 0 24px 0;
    }
    #reviewModal .star {
      font-size: 2.2em;
      color: #ddd;
      cursor: pointer;
      transition: color 0.2s;
    }
    #reviewModal .star.selected,
    #reviewModal .star:hover,
    #reviewModal .star.selected ~ .star {
      color: #ffd600;
    }
    #reviewModal .modal-btn {
      padding: 10px 28px;
      background: #2563eb;
      color: #fff;
      border-radius: 6px;
      border: none;
      font-size: 1.1em;
      font-weight: 500;
      cursor: pointer;
      transition: background 0.2s;
      margin-top: 10px;
    }
    #reviewModal .modal-btn:hover { background: #1746a2; }
    #reviewModal .modal-close {
      position: absolute;
      top: 10px; right: 18px;
      font-size: 1.5em;
      color: #888;
      cursor: pointer;
    }
    #reviewModal .thank-you {
      color: #27ae60;
      font-size: 1.2em;
      margin-top: 18px;
      font-weight: 600;
    }
    .received-btn {
      padding: 6px 16px;
      background: #007bff;
      color: #fff;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 0.98em;
      transition: background 0.2s;
    }
    .received-btn:hover {
      background: #0056b3;
    }
    .notification {
      position: fixed;
      top: 20px;
      right: 20px;
      padding: 15px 25px;
      border-radius: 5px;
      color: white;
      font-weight: 500;
      z-index: 1000;
      animation: slideIn 0.3s ease-out;
    }
    .notification.success {
      background-color: #27ae60;
    }
    .notification.error {
      background-color: #e74c3c;
    }
    @keyframes slideIn {
      from {
        transform: translateX(100%);
        opacity: 0;
      }
      to {
        transform: translateX(0);
        opacity: 1;
      }
    }
    #bookingConfirmationModal .modal-content {
      max-width: 400px;
      width: 90%;
    }
    #bookingConfirmationModal .modal-title {
      font-size: 1.3em;
      font-weight: bold;
      margin-bottom: 20px;
      color: #222;
    }
    #bookingConfirmationModal .modal-btn {
      padding: 10px 24px;
      border: none;
      border-radius: 6px;
      font-size: 1em;
      font-weight: 500;
      cursor: pointer;
      transition: background 0.2s;
    }
    #bookingConfirmationModal .modal-btn:hover {
      opacity: 0.9;
    }
    #reviewModal .modal-content,
    #feedbackModal .modal-content,
    #bookingRatingModal .modal-content {
      max-width: 400px;
      width: 90%;
    }
    #reviewModal .modal-title,
    #feedbackModal .modal-title,
    #bookingRatingModal .modal-title {
      font-size: 1.3em;
      font-weight: bold;
      margin-bottom: 18px;
      color: #222;
    }
    #reviewModal .star.selected,
    #reviewModal .star:hover,
    #reviewModal .star.selected ~ .star {
      color: #ffd600;
    }
  </style>
</head>
<body>
  <div class="container">
    <aside class="sidebar">
      <div class="logo"></div>
      <nav class="nav">
        <a href="Dashboard.php"><i class="fas fa-home"></i> Home</a>
        <a href="Booking.php"><i class="fas fa-calendar-check"></i> Book Ice Cream</a>
        <a href="Cart.php">
          <i class="fas fa-shopping-cart">
          </i> Your Cart
        </a>
        <a href="MyOrders.php" class="active"><i class="fas fa-clipboard-list"></i> My Orders</a>
        <a href="Profile.php"><i class="fas fa-user"></i> Profile</a>
        <a href="../LoginPage/Login.php?logout=1"><i class="fas fa-sign-out-alt"></i> Logout</a>
      </nav>
    </aside>
    <main class="main">
      <div class="orders-title">My Orders</div>
      <table class="orders-table">
        <thead>
          <tr>
            <th>Order ID</th>
            <th>Product</th>
            <th>Quantity</th>
            <th>Grand Total</th>
            <th>Order Date</th>
            <th>Status</th>
            <th>Details</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($orders)): ?>
            <tr><td colspan="8">No orders found.</td></tr>
          <?php else: ?>
            <?php foreach ($orders as $order): ?>
              <tr id="order-row-<?= $order['id'] ?>">
                <td><?= $order['id'] ?></td>
                <td><?= htmlspecialchars($order['product_name']) ?></td>
                <td><?= htmlspecialchars($order['quantity']) ?></td>
                <td>Php <?= number_format(($order['price'] * $order['quantity']) + $order['shipping_fee'], 2) ?></td>
                <td><?= date('Y-m-d', strtotime($order['order_date'])) ?></td>
                <td class="status-<?= htmlspecialchars($order['status']) ?>">
                  <?php if ($order['status'] === 'approved'): ?>
                    <span style="color:#27ae60;font-weight:bold;">Approved</span>
                  <?php elseif ($order['status'] === 'denied'): ?>
                    <span style="color:#e74c3c;font-weight:bold;">Denied</span>
                  <?php elseif ($order['status'] === 'pending'): ?>
                    <span style="color:#f39c12;font-weight:bold;">Pending</span>
                  <?php else: ?>
                    <?= ucfirst($order['status']) ?>
                  <?php endif; ?>
                </td>
                <td><a class="details-btn" href="OrderDetails.php?id=<?= $order['id'] ?>">Details</a></td>
                <td>
                  <?php
                  if ($order['status'] === 'approved' && (empty($order['received_at']) || $order['received_at'] == '0000-00-00 00:00:00')): ?>
                    <button class="received-btn" onclick="markOrderReceived(<?= $order['id'] ?>)">Mark as Received</button>
                  <?php elseif ($order['status'] === 'received' && is_null($order['rating'])): ?>
                    <button class="review-btn" onclick="openReviewModal(<?= $order['id'] ?>)">Review</button>
                  <?php elseif (!is_null($order['rating'])): // Order has been reviewed ?>
                    <span class="reviewed-label">Reviewed</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
      <div class="orders-title" style="margin-top:48px;">My Bookings</div>
      <table class="orders-table">
        <tr>
          <th>Booking ID</th>
          <th>Product</th>
          <th>Quantity</th>
          <th>Order Type</th>
          <th>Delivery Date</th>
          <th>Delivery Time</th>
          <th>Status</th>
          <th>Details</th>
        </tr>
        <?php if (empty($bookings)): ?>
          <tr><td colspan="8">No bookings found.</td></tr>
        <?php else: ?>
          <?php foreach ($bookings as $booking): ?>
            <tr id="booking-row-<?= $booking['id'] ?>">
              <td><?= $booking['id'] ?></td>
              <td><?= htmlspecialchars($booking['product_name']) ?></td>
              <td><?= $booking['quantity'] ?></td>
              <td><?= (empty($booking['delivery_date']) || empty($booking['delivery_time'])) ? 'Pickup' : 'Delivery' ?></td>
              <td><?= htmlspecialchars($booking['delivery_date']) ?></td>
              <td><?= htmlspecialchars($booking['delivery_time']) ?></td>
              <td class="status-<?= htmlspecialchars($booking['status']) ?>">
                <?php if ($booking['status'] === 'approved'): ?>
                  <span style="color:#27ae60;font-weight:bold;">Approved</span>
                <?php elseif ($booking['status'] === 'denied'): ?>
                  <span style="color:#e74c3c;font-weight:bold;">Denied</span>
                <?php elseif ($booking['status'] === 'pending'): ?>
                  <span style="color:#f39c12;font-weight:bold;">Pending</span>
                <?php else: ?>
                  <?= ucfirst($booking['status']) ?>
                <?php endif; ?>
              </td>
              <td><a class="details-btn" href="BookingDetails.php?id=<?= $booking['id'] ?>">Details</a></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </table>
    </main>
  </div>

  <!-- Feedback Modal -->
  <div class="modal" id="feedbackModal">
    <div class="modal-content">
      <span class="modal-close" onclick="closeFeedbackModal()">&times;</span>
      <div class="modal-title">How was your order?</div>
      <form id="feedbackForm">
        <input type="hidden" name="order_id" id="modal_order_id" value="">
        <input type="hidden" name="rating" id="modal_rating" value="0">
        <div class="stars">
          <span class="star" data-value="1">&#9733;</span>
          <span class="star" data-value="2">&#9733;</span>
          <span class="star" data-value="3">&#9733;</span>
          <span class="star" data-value="4">&#9733;</span>
          <span class="star" data-value="5">&#9733;</span>
        </div>
        <button type="submit" class="modal-btn">Submit Feedback</button>
      </form>
    </div>
  </div>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      let selectedOrderId = null;
      window.openFeedbackModal = function(orderId) {
        selectedOrderId = orderId;
        document.getElementById('modal_order_id').value = orderId;
        document.getElementById('feedbackModal').classList.add('active');
        // Reset stars
        document.querySelectorAll('#feedbackModal .star').forEach(star => star.classList.remove('selected'));
        document.getElementById('modal_rating').value = 0;
      };
      window.closeFeedbackModal = function() {
        document.getElementById('feedbackModal').classList.remove('active');
      };
      // Star selection logic
      document.querySelectorAll('#feedbackModal .star').forEach((el, idx) => {
        el.onclick = function() {
          document.getElementById('modal_rating').value = idx + 1;
          document.querySelectorAll('#feedbackModal .star').forEach((star, i) => {
            if (i <= idx) star.classList.add('selected');
            else star.classList.remove('selected');
          });
        };
      });
      // AJAX submit feedback and mark as received
      document.getElementById('feedbackForm').onsubmit = function(e) {
        e.preventDefault();
        const orderId = document.getElementById('modal_order_id').value;
        const rating = document.getElementById('modal_rating').value;
        if (rating < 1 || rating > 5) {
          alert('Please select a star rating.');
          return;
        }
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'submit_review.php', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
          if (xhr.status === 200) {
            const row = document.getElementById('order-row-' + orderId);
            // This part might need adjustment depending on the final flow for reviews
            // For now, assuming the review modal handles updating the UI upon successful review submission.
            // row.querySelector('.status-approved').outerHTML = '<span class="status-received">Received<br>' + render_stars(rating) + '</span>';
            // row.querySelector('.received-btn').remove();
            closeFeedbackModal();
            showNotification('Feedback submitted successfully!', 'success');
          } else {
            showNotification('Failed to submit feedback.', 'error');
          }
        };
        xhr.send('order_id=' + encodeURIComponent(orderId) + '&rating=' + encodeURIComponent(rating));
      };
    });

    function openReviewModal(orderId) {
      document.getElementById('review_order_id').value = orderId;
      document.getElementById('review_rating').value = 0;
      document.querySelectorAll('#reviewStars .star').forEach(star => star.classList.remove('selected'));
      document.getElementById('reviewModal').classList.add('active');
      document.getElementById('reviewForm').style.display = '';
      document.getElementById('reviewThankYou').style.display = 'none';
    }

    function closeReviewModal() {
      document.getElementById('reviewModal').classList.remove('active');
      if (reviewThankYouTimeout) clearTimeout(reviewThankYouTimeout);
    }

    document.querySelectorAll('#reviewStars .star').forEach((el, idx) => {
      el.onclick = function() {
        document.getElementById('review_rating').value = idx + 1;
        document.querySelectorAll('#reviewStars .star').forEach((star, i) => {
          if (i <= idx) star.classList.add('selected');
          else star.classList.remove('selected');
        });
      };
    });

    document.getElementById('reviewForm').onsubmit = function(e) {
      e.preventDefault();
      const orderId = document.getElementById('review_order_id').value;
      const rating = document.getElementById('review_rating').value;
      if (rating < 1 || rating > 5) {
        showNotification('Please select a star rating.', 'error');
        return;
      }
      fetch('submit_review.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `order_id=${encodeURIComponent(orderId)}&rating=${encodeURIComponent(rating)}`
      })
      .then(res => res.text())
      .then(data => {
        if (data === 'success') {
          closeReviewModal();
          const btn = document.querySelector(`#order-row-${orderId} .review-btn`);
          if (btn) btn.outerHTML = '<span class="reviewed-label">Reviewed</span>';
          // Show thank you message in modal
          document.getElementById('reviewForm').style.display = 'none';
          document.getElementById('reviewThankYou').style.display = 'block';
          document.getElementById('reviewModal').classList.add('active');
          reviewThankYouTimeout = setTimeout(closeReviewModal, 1800);
          showNotification('Thank you for your review!', 'success');
        } else {
          showNotification('Failed to submit review.', 'error');
        }
      });
    };

    // Existing showNotification function
    function showNotification(message, type) {
      const notification = document.createElement('div');
      notification.className = `notification ${type}`;
      notification.textContent = message;
      document.body.appendChild(notification);
      setTimeout(() => notification.remove(), 3000);
    }

    

    let currentBookingIdToReceive = null;

    
    

    let currentBookingIdToReview = null;
    let reviewBookingThankYouTimeout = null; // New timeout variable for booking review

    

    
  </script>

  <!-- Review Modal -->
  <div class="modal" id="reviewModal">
    <div class="modal-content">
      <span class="modal-close" onclick="closeReviewModal()">&times;</span>
      <div class="modal-title">Rate your order</div>
      <form id="reviewForm">
        <input type="hidden" name="order_id" id="review_order_id" value="">
        <div class="stars" id="reviewStars">
          <span class="star" data-value="1">&#9733;</span>
          <span class="star" data-value="2">&#9733;</span>
          <span class="star" data-value="3">&#9733;</span>
          <span class="star" data-value="4">&#9733;</span>
          <span class="star" data-value="5">&#9733;</span>
        </div>
        <input type="hidden" name="rating" id="review_rating" value="0">
        <button type="submit" class="modal-btn">Submit Review</button>
      </form>
      <div id="reviewThankYou" class="thank-you" style="display:none;">Thank you for your review!</div>
    </div>
  </div>
  <script>
    let reviewThankYouTimeout = null;
    function openReviewModal(orderId) {
      document.getElementById('review_order_id').value = orderId;
      document.getElementById('review_rating').value = 0;
      document.querySelectorAll('#reviewStars .star').forEach(star => star.classList.remove('selected'));
      document.getElementById('reviewModal').classList.add('active');
      document.getElementById('reviewForm').style.display = '';
      document.getElementById('reviewThankYou').style.display = 'none';
    }
    function closeReviewModal() {
      document.getElementById('reviewModal').classList.remove('active');
      if (reviewThankYouTimeout) clearTimeout(reviewThankYouTimeout);
    }
    document.querySelectorAll('#reviewStars .star').forEach((el, idx) => {
      el.onclick = function() {
        document.getElementById('review_rating').value = idx + 1;
        document.querySelectorAll('#reviewStars .star').forEach((star, i) => {
          if (i <= idx) star.classList.add('selected');
          else star.classList.remove('selected');
        });
      };
    });
    document.getElementById('reviewForm').onsubmit = function(e) {
      e.preventDefault();
      const orderId = document.getElementById('review_order_id').value;
      const rating = document.getElementById('review_rating').value;
      if (rating < 1 || rating > 5) {
        showNotification('Please select a star rating.', 'error');
        return;
      }
      fetch('submit_review.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `order_id=${encodeURIComponent(orderId)}&rating=${encodeURIComponent(rating)}`
      })
      .then(res => res.text())
      .then(data => {
        if (data === 'success') {
          closeReviewModal();
          const btn = document.querySelector(`#order-row-${orderId} .review-btn`);
          if (btn) btn.outerHTML = '<span class="reviewed-label">Reviewed</span>';
          // Show thank you message in modal
          document.getElementById('reviewForm').style.display = 'none';
          document.getElementById('reviewThankYou').style.display = 'block';
          document.getElementById('reviewModal').classList.add('active');
          reviewThankYouTimeout = setTimeout(closeReviewModal, 1800);
          showNotification('Thank you for your review!', 'success');
        } else {
          showNotification('Failed to submit review.', 'error');
        }
      });
    };
  </script>

  <!-- Order Confirmation Modal -->
  <div class="modal" id="orderConfirmationModal">
    <div class="modal-content">
      <span class="modal-close" onclick="closeOrderConfirmationModal()">&times;</span>
      <div class="modal-title">Confirm Order Receipt</div>
      <div style="margin-bottom: 20px; color: #444;">Are you sure you have received this order?</div>
      <div style="display: flex; gap: 10px; justify-content: center;">
        <button class="modal-btn" style="background: #6c757d;" onclick="closeOrderConfirmationModal()">Cancel</button>
        <button class="modal-btn" id="confirmReceiveOrderBtn">Confirm</button>
      </div>
    </div>
  </div>

  <script>
    let currentOrderIdToReceive = null;

    function markOrderReceived(orderId) {
      // Store the orderId and open the confirmation modal
      document.getElementById('confirmReceiveOrderBtn').setAttribute('data-order-id', orderId);
      document.getElementById('orderConfirmationModal').classList.add('active');

      // Disable the clicked button immediately to prevent double clicks
      const button = document.querySelector(`#order-row-${orderId} .received-btn`);
      if (button) {
          button.disabled = true;
          button.textContent = 'Processing...'; // Optional: Provide feedback
      }
    }

    function closeOrderConfirmationModal() {
      document.getElementById('orderConfirmationModal').classList.remove('active');
      const orderId = document.getElementById('confirmReceiveOrderBtn').getAttribute('data-order-id');
      // Re-enable the button if the modal is closed without confirming (optional - depends on desired UX)
      // For now, we assume confirmation is intended if modal was opened via button click.
      document.getElementById('confirmReceiveOrderBtn').removeAttribute('data-order-id');
    }

    // Event listener for the Confirm button in the confirmation modal
    document.getElementById('confirmReceiveOrderBtn').onclick = function() {
      const orderId = this.getAttribute('data-order-id');
      if (!orderId) return; // Should not happen if opened correctly

      // Close modal after confirmation
      closeOrderConfirmationModal();

      // Proceed with marking the order as received via AJAX
      fetch('mark_order_received.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `order_id=${orderId}`
      })
      .then(response => response.text())
      .then(data => {
        if (data === 'success') {
          const row = document.getElementById(`order-row-${orderId}`);

          // Update the status text to Received
          const statusCell = row.querySelector('td:nth-child(6)'); // Assuming status is the 6th column
          if (statusCell) {
              statusCell.innerHTML = '<span class="status-received">Received</span>';
              statusCell.classList.remove('status-approved', 'status-denied', 'status-pending'); // Ensure old status classes are removed
              statusCell.classList.add('status-received');
          }

          // Replace the "Mark as Received" button with the "Review" button
          const actionCell = row.querySelector('td:last-child');
          if (actionCell) {
              actionCell.innerHTML = '<button class="review-btn" onclick="openReviewModal(' + orderId + ')">Review</button>';
              // Re-add the review-btn class to the new button if needed (already included in innerHTML)
          }

          showNotification('Order marked as received!', 'success');

          // Open the review modal
          openReviewModal(orderId);

        } else {
          // Re-enable the button if the server returns an error
          const button = document.querySelector(`#order-row-${orderId} .received-btn`);
          if (button) {
              button.disabled = false;
              button.textContent = 'Mark as Received'; // Restore button text
          }
          // Show the error message returned by the PHP script
          showNotification(`Failed to mark order as received: ${data}`, 'error');
        }
      })
      .catch(error => {
        // Re-enable the button on network errors
        const button = document.querySelector(`#order-row-${orderId} .received-btn`);
        if (button) {
            button.disabled = false;
            button.textContent = 'Mark as Received'; // Restore button text
        }
        // Handle potential network errors
        showNotification('An error occurred while marking order as received.', 'error');
        console.error('Fetch error:', error);
      });
    };

    // Existing openReviewModal and closeReviewModal functions...

    // Existing showNotification function...

  </script>
</body>
</html> 