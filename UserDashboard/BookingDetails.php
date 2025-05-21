<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../LoginPage/Login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$booking = null;
$product = null;

if ($booking_id > 0) {
    try {
        // Fetch booking details for the logged-in user
        $stmt = $pdo_makmak1->prepare("SELECT b.*, p.name AS product_name, p.price FROM bookings b JOIN products p ON b.product_id = p.product_id WHERE b.id = :booking_id AND b.user_id = :user_id");
        $stmt->bindParam(':booking_id', $booking_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        // If booking found, product details are already in $booking
        if ($booking) {
            // Calculate total price (quantity * price)
            $booking['total_price'] = $booking['quantity'] * $booking['price'];
        }

    } catch (PDOException $e) {
        // Log the error for debugging
        error_log('Database error fetching booking details: ' . $e->getMessage());
        $error_message = 'Database error fetching booking details.';
    } catch (Exception $e) {
        // Catch any other unexpected errors
        error_log('Unexpected error fetching booking details: ' . $e->getMessage());
        $error_message = 'An unexpected error occurred.';
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Details</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .details-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 40px auto;
        }
        .details-container h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .detail-item span:first-child {
            font-weight: bold;
            color: #555;
        }
        .detail-item span:last-child {
            color: #777;
        }
        .detail-item:last-child {
            border-bottom: none;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 30px;
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .error-message {
            color: #e74c3c;
            text-align: center;
            margin-top: 20px;
        }
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
                <?php if ($booking): ?>
                    <h2>Booking Details #<?= $booking['id'] ?></h2>
                    <div class="detail-item">
                        <span>Product Name:</span>
                        <span><?= htmlspecialchars($booking['product_name']) ?></span>
                    </div>
                    <div class="detail-item">
                        <span>Quantity:</span>
                        <span><?= $booking['quantity'] ?></span>
                    </div>
                     <div class="detail-item">
                        <span>Order Type:</span>
                        <span><?= (empty($booking['delivery_date']) || empty($booking['delivery_time'])) ? 'Pickup' : 'Delivery' ?></span>
                    </div>
                    <?php if (!empty($booking['delivery_date'])): ?>
                        <div class="detail-item">
                            <span>Delivery Date:</span>
                            <span><?= htmlspecialchars($booking['delivery_date']) ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($booking['delivery_time'])): ?>
                         <div class="detail-item">
                            <span>Delivery Time:</span>
                            <span><?= htmlspecialchars($booking['delivery_time']) ?></span>
                        </div>
                    <?php endif; ?>
                     <?php if (!empty($booking['address'])): ?>
                        <div class="detail-item">
                            <span>Delivery Address:</span>
                            <span><?= htmlspecialchars($booking['address']) ?></span>
                        </div>
                    <?php endif; ?>
                     <?php if (!empty($booking['contact'])): ?>
                        <div class="detail-item">
                            <span>Contact Number:</span>
                            <span><?= htmlspecialchars($booking['contact']) ?></span>
                        </div>
                    <?php endif; ?>
                     <?php if (!empty($booking['instructions'])): ?>
                        <div class="detail-item">
                            <span>Special Instructions:</span>
                            <span><?= htmlspecialchars($booking['instructions']) ?></span>
                        </div>
                    <?php endif; ?>
                     <?php if (!empty($booking['payment_method'])): ?>
                         <div class="detail-item">
                            <span>Payment Method:</span>
                            <span><?= htmlspecialchars($booking['payment_method']) ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="detail-item">
                        <span>Status:</span>
                        <span><?= ucfirst(htmlspecialchars($booking['status'])) ?></span>
                    </div>
                    <?php if (!is_null($booking['feedback_rating'])): ?>
                         <div class="detail-item">
                            <span>Rating:</span>
                            <span><?= render_stars($booking['feedback_rating']) ?></span>
                        </div>
                    <?php endif; ?>
                     <div class="detail-item">
                        <span>Total Price:</span>
                        <span>Php <?= number_format($booking['total_price'], 2) ?></span>
                    </div>
                <?php elseif (isset($error_message)): ?>
                    <div class="error-message"><?= $error_message ?></div>
                <?php else: ?>
                    <div class="error-message">Booking not found.</div>
                <?php endif; ?>
                <a href="MyOrders.php" class="back-link">Back to My Orders</a>
            </div>
        </main>
    </div>
    <script src="script.js"></script>
</body>
</html> 