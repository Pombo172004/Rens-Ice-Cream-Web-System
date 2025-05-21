<?php
session_start();
require_once '../includes/db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../LoginPage/Userlogin.php');
    exit();
}

// Handle booking submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $order_type = $_POST['order_type'];
    $delivery_date = $_POST['delivery_date'];
    $delivery_time = $_POST['delivery_time'];
    $address = $_POST['address'] ?? '';
    $contact = $_POST['contact'] ?? '';
    $instructions = $_POST['instructions'] ?? '';
    $payment_method = $_POST['payment_method'] ?? '';
    // Accept multiple products
    $products = isset($_POST['products']) ? json_decode($_POST['products'], true) : [];
    $success = true;
    foreach ($products as $prod) {
        $product_id = intval($prod['product_id']);
        $quantity = intval($prod['quantity']);
        if ($quantity > 0) {
            $stmt = $pdo_makmak1->prepare("INSERT INTO bookings (user_id, order_type, product_id, quantity, delivery_date, delivery_time, address, contact, instructions, payment_method) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $ok = $stmt->execute([$user_id, $order_type, $product_id, $quantity, $delivery_date, $delivery_time, $address, $contact, $instructions, $payment_method]);
            if (!$ok) $success = false;
        }
    }
    $msg = $success ? 'Booking successful!' : 'Booking failed. Please try again.';
    exit($msg);
}

// Fetch products for dropdown
$products = $pdo_makmak1->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Ice Cream - Makmak</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
      /* Basic Modal Styles (can be merged with existing modal styles if any) */
      .modal {
        display: none;
        /* Use fixed positioning for full viewport coverage */
        position: fixed;
        z-index: 1000;
        left: 0; top: 0; width: 100vw; height: 100vh;
        background: rgba(0,0,0,0.25);
        /* Use flexbox for centering modal content */
        justify-content: center;
        align-items: center;
      }
      .modal.active { display: flex; }
      .modal-content {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 2px 12px rgba(44,62,80,0.12);
        padding: 32px 28px;
        /* min-width: 320px; Adjust as needed */
        text-align: center;
        /* Use fixed positioning and transform for centering */
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        max-width: 95vw; /* Allow modal to take up most of screen width */
        max-height: 95vh; /* Allow modal to take up most of screen height */
        overflow-y: auto; /* Add scroll if content overflows */
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
      /* Styling for the history table within the modal */
      #bookingHistoryModal table {
         width: 100%;
         border-collapse: collapse;
         margin-top: 20px; /* Adjusted margin */
      }
      #bookingHistoryModal th, #bookingHistoryModal td {
        padding: 10px;
        border: 1px solid #ddd;
        text-align: center;
      }
      #bookingHistoryModal th {
        background: #f2f2f2; /* Lighter background for modal header */
        color: #333; /* Darker text color */
        font-weight: bold;
      }
      #bookingHistoryModal tbody tr:nth-child(even) {
         background-color: #f9f9f9;
      }
      #bookingHistoryModal tbody tr:hover {
         background-color: #f1f1f1;
      }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo"></div>
            <nav class="nav">
                <a href="../UserDashboard/Dashboard.php">
                    <i class="fas fa-home"></i> Home
                </a>
                <a href="../UserDashboard/Booking.php" class="active">
                    <i class="fas fa-calendar-check"></i> Book Ice Cream
                </a>
                <a href="../UserDashboard/Cart.php">
                    <i class="fas fa-shopping-cart"></i> Your Cart
                </a>
                
                <a href="../UserDashboard/MyOrders.php">
          <i class="fas fa-clipboard-list">
          </i> My Orders
        </a>

                <a href="../UserDashboard/profile.php">
                    <i class="fas fa-user"></i> Profile
                </a>
                <a href="../LoginPage/Userlogin.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
            
        </aside>

        <!-- Main Content -->
        <main class="main">
        <div class="search-bar" style="display: flex; align-items: center; gap: 16px; position: relative;">
        <input type="text" placeholder="Search ice cream">
        <!-- Notification Bell Icon -->
        <div class="notification-bell" id="notificationBell" style="position: relative; cursor: pointer; margin-left: 10px;">
          <i class="fas fa-bell fa-lg"></i>
          <!-- Badge (optional, static for now) -->
          <span class="notification-badge" style="position: absolute; top: -6px; right: -6px; background: #ff3b3b; color: #fff; border-radius: 50%; padding: 2px 6px; font-size: 12px; display: none;">0</span>
        </div>
        <!-- Notification Dropdown/Modal Placeholder -->
        <div id="notificationDropdown" style="display: none; position: absolute; top: 40px; right: 0; background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.15); border-radius: 8px; width: 300px; z-index: 1000;">
          <div style="padding: 16px; border-bottom: 1px solid #eee; font-weight: bold;">Notifications</div>
          <div style="padding: 16px; color: #888;">No new notifications</div>
        </div>
      </div>
            <div class="booking-header" style="display: flex; align-items: center; gap: 16px;">
                <div style="flex:1;">
                  <h2>Book Your Ice Cream</h2>
                  <p class="booking-subtitle">Schedule your ice cream delivery or pickup</p>
                </div>
                <button id="toggleHistoryBtn" style="padding: 6px 14px; font-size: 0.95em; border-radius: 6px; background: #3498db; color: #fff; border: none; cursor: pointer;">Booking History</button>
            </div>

            <div class="booking-container">
                <!-- Order Type Selection -->
                <div class="booking-section">
                    <h3>Select Order Type</h3>
                    <div class="order-type-options">
                        <div class="order-type-card" data-type="delivery">
                            <i class="fas fa-truck"></i>
                            <h4>Delivery</h4>
                            <p>Get your ice cream delivered to your doorstep</p>
                        </div>
                        <div class="order-type-card" data-type="pickup">
                            <i class="fas fa-store"></i>
                            <h4>Pickup</h4>
                            <p>Pick up your order from our store</p>
                        </div>
                    </div>
                </div>

                <!-- Date and Time Selection -->
                <div class="booking-section">
                    <h3>Select Date & Time</h3>
                    <div class="datetime-selection">
                        <div class="date-selection">
                            <label for="delivery-date">Select Date:</label>
                            <input type="date" id="delivery-date" name="delivery-date" min="">
                        </div>
                        <div class="time-selection">
                            <label for="delivery-time">Select Time:</label>
                            <input type="time" id="delivery-time" name="delivery-time">
                        </div>
                    </div>
                </div>

                <!-- Ice Cream Selection -->
                <div class="booking-section">
                    <h3>Select Ice Cream</h3>
                    <div class="ice-cream-selection">
                        <div class="ice-cream-card">
                            <img src="../Images/Ube.png" alt="Ube Ice Cream">
                            <h4>Ube Ice Cream</h4>
                            <p>Php 299</p>
                            <div class="quantity-controls">
                                <button class="quantity-btn minus">-</button>
                                <input type="number" value="0" min="0" class="quantity-input">
                                <button class="quantity-btn plus">+</button>
                            </div>
                        </div>

                        <div class="ice-cream-card">
                            <img src="../Images/Mango.png" alt="Mango Ice Cream">
                            <h4>Mango Ice Cream</h4>
                            <p>Php 299</p>
                            <div class="quantity-controls">
                                <button class="quantity-btn minus">-</button>
                                <input type="number" value="0" min="0" class="quantity-input">
                                <button class="quantity-btn plus">+</button>
                            </div>
                        </div>

                        <div class="ice-cream-card">
                            <img src="../Images/chocolate.png" alt="Chocolate Ice Cream">
                            <h4>Chocolate Ice Cream</h4>
                            <p>Php 299</p>
                            <div class="quantity-controls">
                                <button class="quantity-btn minus">-</button>
                                <input type="number" value="0" min="0" class="quantity-input">
                                <button class="quantity-btn plus">+</button>
                            </div>
                        </div>

                        <div class="ice-cream-card">
                            <img src="../Images/cookies.png" alt="Cookies & Cream">
                            <h4>Cookies & Cream</h4>
                            <p>Php 299</p>
                            <div class="quantity-controls">
                                <button class="quantity-btn minus">-</button>
                                <input type="number" value="0" min="0" class="quantity-input">
                                <button class="quantity-btn plus">+</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Delivery Details (Shown when delivery is selected) -->
                <div class="booking-section delivery-details">
                    <h3>Delivery Details</h3>
                    <div class="delivery-form">
                        <div class="form-group">
                            <label for="address">Delivery Address:</label>
                            <textarea id="address" name="address" rows="3" placeholder="Enter your complete delivery address"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="contact">Contact Number:</label>
                            <input type="tel" id="contact" name="contact" placeholder="Enter your contact number">
                        </div>
                        <div class="form-group">
                            <label for="instructions">Special Instructions:</label>
                            <textarea id="instructions" name="instructions" rows="2" placeholder="Any special instructions for delivery"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Payment and Summary Row -->
                <div class="booking-summary-row">
                    <div class="booking-section card-flex">
                        <div class="payment-method-section">
                            <h3>Payment Method</h3>
                            <div class="payment-method-options">
                                <label><input type="radio" name="payment_method" value="Gcash"> Gcash</label>
                                <label><input type="radio" name="payment_method" value="Cash"> Cash</label>
                            </div>
                        </div>
                        <div class="order-summary">
                            <h3>Order Summary</h3>
                            <div class="summary-details">
                                <div class="summary-row">
                                    <span>Subtotal</span>
                                    <span>Php 0</span>
                                </div>
                                <div class="summary-row">
                                    <span>Delivery Fee</span>
                                    <span>Php 50</span>
                                </div>
                                <div class="summary-row total">
                                    <span>Total</span>
                                    <span>Php 50</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="booking-actions">
                    <button class="btn-cancel">Cancel</button>
                    <button class="btn-confirm">Confirm Booking</button>
                </div>
            </div>

        </main>
    </div>

    <!-- Order Summary Modal -->
    <div id="orderSummaryModal" class="modal" style="display:none;">
      <div class="modal-content">
        <span class="modal-close" id="closeOrderSummaryModal">&times;</span>
        <div class="modal-title">Order Summary</div>
        <div id="orderSummaryDetails"></div>
        <button id="finalConfirmBtn" class="modal-btn" style="padding:10px 28px;background:#27ae60;color:#fff;border-radius:6px;border:none;font-size:1.1em;font-weight:500;cursor:pointer;transition:background 0.2s;margin-top:10px;">Confirm Booking</button>
      </div>
    </div>

    <!-- Booking History Modal -->
    <div class="modal" id="bookingHistoryModal">
      <div class="modal-content">
        <span class="modal-close">&times;</span>
        <div class="modal-title">Booking History</div>
        <div style="overflow-x:auto;">
          <table id="bookingHistoryTable" style="width:100%; border-collapse: collapse; background: #fff; border-radius: 10px; box-shadow: 0 2px 8px rgba(44,62,80,0.08);">
            <thead>
              <tr style="background: #f8f9fa;">
                <th style="padding: 10px; border-bottom: 1px solid #eee;">#</th>
                <th style="padding: 10px; border-bottom: 1px solid #eee;">Product Name</th>
                <th style="padding: 10px; border-bottom: 1px solid #eee;">Quantity</th>
                <th style="padding: 10px; border-bottom: 1px solid #eee;">Order Type</th>
                <th style="padding: 10px; border-bottom: 1px solid #eee;">Delivery Date</th>
                <th style="padding: 10px; border-bottom: 1px solid #eee;">Delivery Time</th>
                <th style="padding: 10px; border-bottom: 1px solid #eee;">Status</th>
              </tr>
            </thead>
            <tbody>
              <!-- Booking history data will be loaded here by JavaScript -->
              <tr>
                <td colspan="7" style="text-align:center;">Loading history...</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <script src="script.js"></script>
    <script src="notification.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let selectedOrderType = null;

        // Set min date to tomorrow for delivery date input
        const dateInput = document.getElementById('delivery-date');
        const today = new Date();
        today.setDate(today.getDate() + 1); // Move to tomorrow
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        const minDate = `${yyyy}-${mm}-${dd}`;
        dateInput.setAttribute('min', minDate);

        // Set min and max time for delivery time input (8 AM to 6 PM)
        const timeInput = document.getElementById('delivery-time');
        timeInput.setAttribute('min', '08:00');
        timeInput.setAttribute('max', '18:00');

        // Order type selection
        document.querySelectorAll('.order-type-card').forEach(card => {
            card.addEventListener('click', function() {
                document.querySelectorAll('.order-type-card').forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                selectedOrderType = this.getAttribute('data-type');

                // Show/hide delivery details
                if (selectedOrderType === 'delivery') {
                    document.querySelector('.delivery-details').style.display = 'block';
                } else {
                    document.querySelector('.delivery-details').style.display = 'none';
                }
            });
        });

        // Set default to delivery
        document.querySelector('.order-type-card[data-type="delivery"]').click();

        // Quantity controls
        document.querySelectorAll('.quantity-controls').forEach(qc => {
            const minus = qc.querySelector('.minus');
            const plus = qc.querySelector('.plus');
            const input = qc.querySelector('.quantity-input');
            minus.addEventListener('click', () => {
                input.value = Math.max(0, parseInt(input.value) - 1);
            });
            plus.addEventListener('click', () => {
                input.value = parseInt(input.value) + 1;
            });
        });

        // Time validation for custom picker (supports both 24h and 12h formats)
        function isValidTimeRange(timeStr) {
            if (!timeStr) return false;
            let [hour, minute] = timeStr.split(':');
            let ampm = '';
            if (minute && minute.includes(' ')) {
                [minute, ampm] = minute.split(' ');
                hour = parseInt(hour, 10);
                if (ampm.toUpperCase() === 'PM' && hour !== 12) hour += 12;
                if (ampm.toUpperCase() === 'AM' && hour === 12) hour = 0;
            } else {
                hour = parseInt(hour, 10);
            }
            return hour >= 8 && hour < 18;
        }

        // Confirm Booking (show modal first)
        document.querySelector('.btn-confirm').addEventListener('click', function() {
            // Gather all selected products and quantities
            let products = [];
            let summaryHtml = '';
            document.querySelectorAll('.ice-cream-card').forEach((card, idx) => {
                const qty = parseInt(card.querySelector('.quantity-input').value);
                if (qty > 0) {
                    products.push({ product_id: idx + 1, name: card.querySelector('h4').textContent, quantity: qty, price: 299 });
                }
            });
            if (!selectedOrderType) {
                alert('Please select Delivery or Pickup.');
                return;
            }
            if (products.length === 0) {
                alert('Please select at least one ice cream and quantity.');
                return;
            }
            // Get date, time, and delivery details
            const delivery_date = document.getElementById('delivery-date').value;
            const delivery_time = document.getElementById('delivery-time').value;
            const address = document.getElementById('address').value;
            const contact = document.getElementById('contact').value;
            const instructions = document.getElementById('instructions').value;
            // Date validation: must not be today or any past date
            if (!delivery_date || delivery_date < minDate) {
                alert('Please select a valid delivery date (not today or any past date).');
                return;
            }
            // Time validation: must be between 08:00 and 18:00 (supports custom picker)
            if (!isValidTimeRange(delivery_time)) {
                alert('Please select a delivery time between 8:00 AM and 6:00 PM.');
                return;
            }
            // Require delivery details if delivery is selected
            if (selectedOrderType === 'delivery') {
                if (!address.trim()) {
                    alert('Delivery address is required.');
                    return;
                }
                if (!contact.trim()) {
                    alert('Contact number is required.');
                    return;
                }
                if (!instructions.trim()) {
                    alert('Special instructions are required.');
                    return;
                }
            }
            // Get payment method
            const payment_method = document.querySelector('input[name="payment_method"]:checked');
            if (!payment_method) {
                alert('Please select a payment method.');
                return;
            }
            // Build summary HTML
            let subtotal = 0;
            summaryHtml += '<table style="width:100%;margin-bottom:18px;"><tr><th style="text-align:left;">Product</th><th>Qty</th><th>Price</th></tr>';
            products.forEach(p => {
                summaryHtml += `<tr><td>${p.name}</td><td>${p.quantity}</td><td>Php ${p.price * p.quantity}</td></tr>`;
                subtotal += p.price * p.quantity;
            });
            summaryHtml += '</table>';
            summaryHtml += `<div style='margin-bottom:8px;'><b>Order Type:</b> ${selectedOrderType.charAt(0).toUpperCase() + selectedOrderType.slice(1)}</div>`;
            summaryHtml += `<div style='margin-bottom:8px;'><b>Date:</b> ${delivery_date}</div>`;
            summaryHtml += `<div style='margin-bottom:8px;'><b>Time:</b> ${delivery_time}</div>`;
            if (selectedOrderType === 'delivery') {
                summaryHtml += `<div style='margin-bottom:8px;'><b>Address:</b> ${address}</div>`;
                summaryHtml += `<div style='margin-bottom:8px;'><b>Contact:</b> ${contact}</div>`;
                summaryHtml += `<div style='margin-bottom:8px;'><b>Instructions:</b> ${instructions}</div>`;
            }
            summaryHtml += `<div style='margin-bottom:8px;'><b>Payment Method:</b> ${payment_method.value}</div>`;
            const deliveryFee = (selectedOrderType === 'delivery' && subtotal > 0) ? 50 : 0;
            summaryHtml += `<div style='margin-bottom:8px;'><b>Subtotal:</b> Php ${subtotal}</div>`;
            summaryHtml += `<div style='margin-bottom:8px;'><b>Delivery Fee:</b> Php ${deliveryFee}</div>`;
            summaryHtml += `<div style='margin-bottom:8px;'><b>Total:</b> Php ${subtotal + deliveryFee}</div>`;
            document.getElementById('orderSummaryDetails').innerHTML = summaryHtml;
            document.getElementById('orderSummaryModal').style.display = 'flex';
            // Store booking data for final confirm
            window._bookingData = {
                products,
                selectedOrderType,
                delivery_date,
                delivery_time,
                address,
                contact,
                instructions,
                payment_method: payment_method.value
            };
        });
        // Modal close
        document.getElementById('closeOrderSummaryModal').onclick = function() {
            document.getElementById('orderSummaryModal').style.display = 'none';
        };
        // Final confirm booking
        document.getElementById('finalConfirmBtn').onclick = function() {
            const d = window._bookingData;
            fetch('Booking.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `order_type=${d.selectedOrderType}&delivery_date=${d.delivery_date}&delivery_time=${d.delivery_time}&address=${encodeURIComponent(d.address)}&contact=${encodeURIComponent(d.contact)}&instructions=${encodeURIComponent(d.instructions)}&payment_method=${d.payment_method}&products=${encodeURIComponent(JSON.stringify(d.products))}`
            })
            .then(res => res.text())
            .then(data => {
                showNotification(data, data.includes('success') ? 'success' : 'error');
                setTimeout(() => window.location.reload(), 1800);
            });
        };

        // Hide delivery details if pickup is selected by default
        document.querySelector('.delivery-details').style.display = 'block';

        // Order Summary Calculation
        function updateOrderSummary() {
            let subtotal = 0;
            document.querySelectorAll('.ice-cream-card').forEach(card => {
                const qty = parseInt(card.querySelector('.quantity-input').value);
                if (qty > 0) {
                    subtotal += qty * 299; // Assuming all products are Php 299
                }
            });
            const deliveryFee = (selectedOrderType === 'delivery' && subtotal > 0) ? 50 : 0;
            const total = subtotal + deliveryFee;
            document.querySelector('.summary-row span:nth-child(2)').textContent = `Php ${subtotal}`;
            document.querySelector('.summary-row:nth-child(2) span:nth-child(2)').textContent = `Php ${deliveryFee}`;
            document.querySelector('.summary-row.total span:nth-child(2)').textContent = `Php ${total}`;
        }

        // Attach listeners for summary update
        function attachSummaryListeners() {
            document.querySelectorAll('.quantity-input').forEach(input => {
                input.addEventListener('input', updateOrderSummary);
            });
            document.querySelectorAll('.quantity-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    setTimeout(updateOrderSummary, 100);
                });
            });
            document.querySelectorAll('.order-type-card').forEach(card => {
                card.addEventListener('click', function() {
                    setTimeout(updateOrderSummary, 100);
                });
            });
        }

        // Call after DOM loaded
        updateOrderSummary();
        attachSummaryListeners();

        // Toggle Booking History
        const toggleHistoryBtn = document.getElementById('toggleHistoryBtn');
        const bookingHistoryModal = document.getElementById('bookingHistoryModal');
        const closeHistoryModal = bookingHistoryModal.querySelector('.modal-close');

        toggleHistoryBtn.onclick = function() {
          bookingHistoryModal.classList.add('active');
          fetchBookingHistory(); // Call function to fetch and display history
        }

        closeHistoryModal.onclick = function() {
          bookingHistoryModal.classList.remove('active');
        }

        // Close modal if clicked outside
        window.onclick = function(event) {
          if (event.target === bookingHistoryModal) {
            bookingHistoryModal.classList.remove('active');
          }
        }

        // Function to fetch booking history (will be implemented in the next step)
        function fetchBookingHistory() {
          const tbody = document.querySelector('#bookingHistoryTable tbody');
          tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;">Loading history...</td></tr>'; // Show loading message
          // TODO: Implement actual fetch call to server-side script
          console.log('Fetching booking history...');
          // Example of where fetch would go:
          /*
          fetch('fetch_booking_history.php')
            .then(response => response.json())
            .then(data => {
              // Clear loading message
              tbody.innerHTML = '';
              if (data.length > 0) {
                data.forEach(booking => {
                  const row = `
                    <tr>
                      <td>${booking.id}</td>
                      <td>${escapeHTML(booking.product_name)}</td>
                      <td>${booking.quantity}</td>
                      <td>${booking.order_type === 'delivery' ? 'Delivery' : 'Pickup'}</td>
                      <td>${escapeHTML(booking.delivery_date)}</td>
                      <td>${escapeHTML(booking.delivery_time)}</td>
                      <td>${escapeHTML(booking.status)}</td>
                    </tr>
                  `;
                  tbody.innerHTML += row;
                });
              } else {
                tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;">No booking history found.</td></tr>';
              }
            })
            .catch(error => {
              console.error('Error fetching booking history:', error);
              tbody.innerHTML = '<tr><td colspan="7" style="text-align:center; color:red;">Failed to load booking history.</td></tr>';
            });
          */
        }

        // Basic HTML escaping function to prevent XSS (consider a more robust library in production)
        function escapeHTML(str) {
            if (!str) return '';
            const div = document.createElement('div');
            div.appendChild(document.createTextNode(str));
            return div.innerHTML;
        }
    });

    function showNotification(message, type = 'success') {
        const existingNotification = document.querySelector('.notification');
        if (existingNotification) {
            existingNotification.remove();
        }
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.style = `
            position: fixed;
            top: 30px;
            left: 50%;
            transform: translateX(-50%);
            background: ${type === 'success' ? '#27ae60' : '#e74c3c'};
            color: #fff;
            padding: 18px 32px;
            border-radius: 8px;
            font-size: 1.1em;
            z-index: 9999;
            box-shadow: 0 2px 12px rgba(44,62,80,0.12);
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 220px;
            text-align: center;
        `;
        notification.innerHTML = `
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
            ${message}
        `;
        document.body.appendChild(notification);
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease-in forwards';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 1800);
    }

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