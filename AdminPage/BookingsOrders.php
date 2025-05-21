<?php
session_start();
require '../includes/db.php';
// Optionally, add admin authentication here
// Fetch all bookings (excluding received) with user and product names
$bookings = $pdo_makmak1->query("SELECT b.*, u.firstname, u.lastname, p.name AS product_name FROM bookings b JOIN users u ON b.user_id = u.id JOIN products p ON b.product_id = p.product_id WHERE b.status != 'received'")->fetchAll(PDO::FETCH_ASSOC);

// Fetch received bookings separately
$received_bookings = $pdo_makmak1->query("SELECT b.*, u.firstname, u.lastname, p.name AS product_name FROM bookings b JOIN users u ON b.user_id = u.id JOIN products p ON b.product_id = p.product_id WHERE b.status = 'received'")->fetchAll(PDO::FETCH_ASSOC);

$bookings_table = array_filter($bookings, function($b) { return !empty($b['delivery_date']); });
$orders_table = array_filter($bookings, function($b) { return empty($b['delivery_date']); });
// Fetch orders from the new orders table (excluding received)
$orders = $pdo_makmak1->query("SELECT o.*, u.firstname, u.lastname, p.name AS product_name FROM orders o JOIN users u ON o.user_id = u.id JOIN products p ON o.product_id = p.product_id WHERE o.status != 'received'")->fetchAll(PDO::FETCH_ASSOC);

// Fetch received orders separately
$received_orders = $pdo_makmak1->query("SELECT o.*, u.firstname, u.lastname, p.name AS product_name FROM orders o JOIN users u ON o.user_id = u.id JOIN products p ON o.product_id = p.product_id WHERE o.status = 'received'")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - Bookings / Orders</title>
  <link rel="stylesheet" href="../UserDashboard/style.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    table { width: 100%; border-collapse: collapse; margin-top: 30px; }
    th, td { padding: 10px; border: 1px solid #ddd; text-align: center; }
    th { background: #2c3e50; color: #fff; }
    .status-pending { color: #f39c12; font-weight: bold; }
    .status-approved { color: #27ae60; font-weight: bold; }
    .status-denied { color: #e74c3c; font-weight: bold; }
    .admin-action-btn { padding: 6px 14px; border: none; border-radius: 5px; margin: 0 2px; cursor: pointer; }
    .approve-btn { background: #27ae60; color: #fff; }
    .deny-btn { background: #e74c3c; color: #fff; }
    @media print {
      body * { visibility: hidden !important; }
      #printArea, #printOrdersArea { display: none !important; }
      .admin-action-btn, .approve-btn, .deny-btn, form, .edit-btn { display: none !important; }
      th.action-col, td.action-col { display: none !important; }
      body.print-bookings #printArea {
        display: table !important;
        visibility: visible !important;
        position: absolute !important;
        left: 0; top: 0; width: 100vw; background: #fff;
      }
      body.print-bookings #printArea * {
        visibility: visible !important;
      }
      body.print-bookings #printArea tr { display: table-row !important; }
      body.print-bookings #printArea th, body.print-bookings #printArea td { display: table-cell !important; }
      body.print-orders #printOrdersArea {
        display: table !important;
        visibility: visible !important;
        position: absolute !important;
        left: 0; top: 0; width: 100vw; background: #fff;
      }
      body.print-orders #printOrdersArea * {  
        visibility: visible !important;
      }
      body.print-orders #printOrdersArea tr { display: table-row !important; }
      body.print-orders #printOrdersArea th, body.print-orders #printOrdersArea td { display: table-cell !important; }
    }
    /* Modal button hover effects */
    #deleteConfirmModal button#confirmDeleteBtn:hover {
      background: #a93226;
    }
    #deleteConfirmModal button#cancelDeleteBtn:hover {
      background: #555;
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="logo"></div>
      <nav class="nav">
        <a href="AdminDashboard.php"><i class="fas fa-chart-pie"></i> Dashboard</a>
        <a href="BookingsOrders.php" class="active"><i class="fas fa-calendar-check"></i> Bookings / Orders</a>
        <a href="Stocks.php"><i class="fas fa-boxes"></i> Stocks</a>
        <a href="Analytics.php"><i class="fas fa-chart-line"></i> Analytics</a>
        <a href="../AdminLogin/AdminLogin.php?logout=1"><i class="fas fa-sign-out-alt"></i> Logout</a>
      </nav>
      
    </aside>
    <!-- Main Content -->
    <main class="main">
      <?php if (isset($_SESSION['admin_msg'])): ?>
        <div style="background:#dff0d8;color:#3c763d;padding:10px 20px;border-radius:6px;margin-bottom:18px;">
          <?= $_SESSION['admin_msg'] ?>
        </div>
        <?php unset($_SESSION['admin_msg']); ?>
      <?php endif; ?>
      <div class="card">
        <h3>Orders</h3>
        <button onclick="printOrders()" class="edit-btn" style="margin-bottom: 18px; float: right;">Print Orders</button>
        <button id="showReceivedBtn" class="edit-btn" style="margin-bottom: 18px; float: right; margin-right: 10px;">Show Received Orders</button>
        <div id="printOrdersArea">
        <table>
          <tr>
            <th>ID</th>
            <th>User Name</th>
            <th>Product Name</th>
            <th>Quantity</th>
            <th>Status</th>
            <th class="action-col">Action</th>
          </tr>
          <?php foreach ($orders as $order): ?>
          <tr data-status="<?= htmlspecialchars(strtolower($order['status'])) ?>">
            <td><?= $order['id'] ?></td>
            <td><?= htmlspecialchars($order['firstname'] . ' ' . $order['lastname']) ?></td>
            <td><?= htmlspecialchars($order['product_name']) ?></td>
            <td><?= $order['quantity'] ?></td>
            <td class="status-<?= htmlspecialchars($order['status']) ?>"><?= ucfirst($order['status']) ?></td>
            <td class="action-col">
              <form method="post" action="update_order_status.php?redirect=BookingsOrders.php" style="display:inline">
                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                <button class="admin-action-btn approve-btn" name="action" value="approved" type="submit">Approve</button>
                <button class="admin-action-btn deny-btn" name="action" value="denied" type="submit">Deny</button>
              </form>
              <form method="post" action="delete_order.php" style="display:inline" onsubmit="return confirm('Are you sure you want to delete this order?');">
                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                <button class="admin-action-btn deny-btn" style="background:#c0392b; margin-left:4px;" type="submit">Delete</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </table>
        </div>

        <!-- Received Orders Section (hidden by default) -->
        <div id="receivedOrdersArea" style="display: none;">
            <h3>Received Orders</h3>
            <table>
                <tr>
                    <th>ID</th>
                    <th>User Name</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Status</th>
                    <!-- No action column for received orders based on previous interaction -->
                </tr>
                <?php foreach ($received_orders as $order): ?>
                <tr>
                    <td><?= $order['id'] ?></td>
                    <td><?= htmlspecialchars($order['firstname'] . ' ' . $order['lastname']) ?></td>
                    <td><?= htmlspecialchars($order['product_name']) ?></td>
                    <td><?= $order['quantity'] ?></td>
                    <td class="status-<?= htmlspecialchars($order['status']) ?>"><?= ucfirst($order['status']) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <h3 style="margin-top:40px;">Bookings</h3>
        <button onclick="printBookings()" class="edit-btn" style="margin-bottom: 18px; float: right;">Print Bookings</button>
        <button id="showReceivedBookingsBtn" class="edit-btn" style="margin-bottom: 18px; float: right; margin-right: 10px;">Show Received Bookings</button>
        <div id="printArea">
        <table>
          <tr>
            <th>ID</th>
            <th>User Name</th>
            <th>Product Name</th>
            <th>Quantity</th>
            <th>Delivery Date</th>
            <th>Delivery Time</th>
            <th>Status</th>
            <th class="action-col">Action</th>
          </tr>
          <?php foreach ($bookings as $booking): ?>
          <tr data-status="<?= htmlspecialchars(strtolower($booking['status'])) ?>">
            <td><?= $booking['id'] ?></td>
            <td><?= htmlspecialchars($booking['firstname'] . ' ' . $booking['lastname']) ?></td>
            <td><?= htmlspecialchars($booking['product_name']) ?></td>
            <td><?= $booking['quantity'] ?></td>
            <td><?= htmlspecialchars($booking['delivery_date']) ?></td>
            <td><?= htmlspecialchars($booking['delivery_time']) ?></td>
            <td class="status-<?= htmlspecialchars($booking['status']) ?>"><?= ucfirst($booking['status']) ?></td>
            <td class="action-col">
              <form method="post" action="update_booking_status.php?redirect=BookingsOrders.php" style="display:inline">
                <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                <button class="admin-action-btn approve-btn" name="action" value="approved" type="submit">Approve</button>
                <button class="admin-action-btn deny-btn" name="action" value="denied" type="submit">Deny</button>
              </form>
              <form method="post" action="delete_booking.php" style="display:inline">
                <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                <button class="admin-action-btn deny-btn delete-booking-btn" style="background:#c0392b; margin-left:4px;" type="button">Delete</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </table>
        </div>

        <!-- Received Bookings Section (hidden by default) -->
        <div id="receivedBookingsArea" style="display: none;">
            <h3>Received Bookings</h3>
            <table>
                <tr>
                    <th>ID</th>
                    <th>User Name</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Delivery Date</th>
                    <th>Delivery Time</th>
                    <th>Status</th>
                    <!-- No action column for received bookings -->
                </tr>
                <?php foreach ($received_bookings as $booking): ?>
                <tr data-status="<?= htmlspecialchars(strtolower($booking['status'])) ?>">
                    <td><?= $booking['id'] ?></td>
                    <td><?= htmlspecialchars($booking['firstname'] . ' ' . $booking['lastname']) ?></td>
                    <td><?= htmlspecialchars($booking['product_name']) ?></td>
                    <td><?= htmlspecialchars($booking['quantity']) ?></td>
                    <td><?= htmlspecialchars($booking['delivery_date']) ?></td>
                    <td><?= htmlspecialchars($booking['delivery_time']) ?></td>
                    <td class="status-<?= htmlspecialchars($booking['status']) ?>"><?= ucfirst($booking['status']) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>

      </div>
    </main>
  </div>
  <!-- Delete Confirmation Modal -->
  <div id="deleteConfirmModal" style="display:none;position:fixed;z-index:3000;left:0;top:0;width:100vw;height:100vh;background:rgba(0,0,0,0.25);justify-content:center;align-items:center;">
    <div style="background:#fff;border-radius:12px;box-shadow:0 2px 12px rgba(44,62,80,0.12);padding:32px 28px;min-width:320px;text-align:center;position:relative;max-width:95vw;">
      <span id="closeDeleteModal" style="position:absolute;top:10px;right:18px;font-size:1.5em;color:#888;cursor:pointer;">&times;</span>
      <div style="font-size:1.2em;font-weight:bold;margin-bottom:18px;color:#222;">Delete Booking</div>
      <div style="margin-bottom:18px;">Are you sure you want to delete this booking?</div>
      <button id="confirmDeleteBtn" style="padding:8px 24px;background:#c0392b;color:#fff;border:none;border-radius:6px;font-size:1em;margin-right:10px;">Delete</button>
      <button id="cancelDeleteBtn" style="padding:8px 24px;background:#888;color:#fff;border:none;border-radius:6px;font-size:1em;">Cancel</button>
    </div>
  </div>
  <script>
    function printBookings() {
      document.body.classList.add('print-bookings');
      document.body.classList.remove('print-orders');
      window.print();
      setTimeout(() => { document.body.classList.remove('print-bookings'); }, 500);
    }
    function printOrders() {
      document.body.classList.add('print-orders');
      document.body.classList.remove('print-bookings');
      window.print();
      setTimeout(() => { document.body.classList.remove('print-orders'); }, 500);
    }
    document.addEventListener('DOMContentLoaded', function() {
      let formToDelete = null;
      // Attach click event to all delete booking buttons
      document.querySelectorAll('.delete-booking-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
          formToDelete = this.closest('form');
          document.getElementById('deleteConfirmModal').style.display = 'flex';
        });
      });
      // Modal close/cancel
      document.getElementById('closeDeleteModal').onclick = function() {
        document.getElementById('deleteConfirmModal').style.display = 'none';
        formToDelete = null;
      };
      document.getElementById('cancelDeleteBtn').onclick = function() {
        document.getElementById('deleteConfirmModal').style.display = 'none';
        formToDelete = null;
      };
      // Confirm delete
      document.getElementById('confirmDeleteBtn').onclick = function() {
        if (formToDelete) {
          formToDelete.submit();
          document.getElementById('deleteConfirmModal').style.display = 'none';
          formToDelete = null;
        }
      };

      const showReceivedBtn = document.getElementById('showReceivedBtn');
      const orderTableArea = document.getElementById('printOrdersArea'); // Main orders table area
      const receivedOrderTableArea = document.getElementById('receivedOrdersArea'); // Received orders table area
      let showingReceivedOnly = false;

      showReceivedBtn.addEventListener('click', function() {
        showingReceivedOnly = !showingReceivedOnly;

        if (showingReceivedOnly) {
          orderTableArea.style.display = 'none'; // Hide main orders
          receivedOrderTableArea.style.display = ''; // Show received orders
          this.textContent = 'Show All Orders';
        } else {
          orderTableArea.style.display = ''; // Show main orders
          receivedOrderTableArea.style.display = 'none'; // Hide received orders
          this.textContent = 'Show Received Orders';
        }
      });

      // --- New JavaScript for Bookings --- //
      const showReceivedBookingsBtn = document.getElementById('showReceivedBookingsBtn');
      const bookingsTableArea = document.getElementById('printArea'); // Main bookings table area (original printArea)
      const receivedBookingsArea = document.getElementById('receivedBookingsArea'); // New received bookings table area
      let showingReceivedBookingsOnly = false;

      showReceivedBookingsBtn.addEventListener('click', function() {
          showingReceivedBookingsOnly = !showingReceivedBookingsOnly;

          if (showingReceivedBookingsOnly) {
              bookingsTableArea.style.display = 'none'; // Hide main bookings
              receivedBookingsArea.style.display = ''; // Show received bookings
              this.textContent = 'Show All Bookings';
          } else {
              bookingsTableArea.style.display = ''; // Show main bookings
              receivedBookingsArea.style.display = 'none'; // Hide received bookings
              this.textContent = 'Show Received Bookings';
          }
      });
      // --- End New JavaScript for Bookings --- //

    });
  </script>
</body>
</html> 