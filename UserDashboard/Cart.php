<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../LoginPage/Userlogin.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Makmak</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
   
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
                <a href="../UserDashboard/Booking.php">
                    <i class="fas fa-calendar-check"></i> Book Ice Cream
                </a>
                <a href="../UserDashboard/Cart.php" class="active"> 
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
            <!-- Empty Cart Message -->
            <div class="empty-cart-container" id="emptyCartMessage">
                <i class="fas fa-shopping-cart empty-cart-icon"></i>
                <h2 class="empty-cart-message">Your cart is empty</h2>
                <p style="color: #7f8c8d; margin-bottom: 30px;">Looks like you haven't added any items to your cart yet.</p>
                <a href="Dashboard.php" class="start-shopping-btn">
                    <i class="fas fa-ice-cream"></i> Start Shopping
                </a>
            </div>

            <!-- Cart Content -->
            <div class="cart-container" id="cartContent">
                <div class="cart-header">
                    <h2>Your Shopping Cart</h2>
                    <p class="cart-summary">You have <span id="item-count">0</span> items in your cart</p>
                </div>

                <div id="cartItems">
                    <!-- Cart items will be dynamically added here -->
                </div>

                <!-- Cart Summary -->
                <div class="cart-summary-section">
                    <h3>Order Summary</h3>
                    <div class="summary-details">
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span id="subtotal">Php 0</span>
                        </div>
                        <div class="summary-row">
                            <span>Delivery Fee</span>
                            <span>Php 50</span>
                        </div>
                        <div class="summary-row total">
                            <span>Total</span>
                            <span id="total">Php 50</span>
                        </div>
                    </div>
                    <button id="checkout-selected-btn" class="checkout-btn">Checkout Selected</button>
                    <a href="Dashboard.php" class="continue-shopping-btn">Continue Shopping</a>
                </div>
            </div>
        </main>
    </div>

    <div id="notification" class="notification"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadCartItems();
        });

        function loadCartItems() {
            fetch('get_cart_items.php')
                .then(response => response.json())
                .then(data => {
                    const cartItems = document.getElementById('cartItems');
                    const emptyCartMessage = document.getElementById('emptyCartMessage');
                    const cartContent = document.getElementById('cartContent');
                    const itemCount = document.getElementById('item-count');

                    if (data.items && data.items.length > 0) {
                        emptyCartMessage.style.display = 'none';
                        cartContent.style.display = 'block';
                        
                        cartItems.innerHTML = '';
                        let subtotal = 0;

                        data.items.forEach(item => {
                            const itemTotal = item.price * item.quantity;
                            subtotal += itemTotal;

                            cartItems.innerHTML += `
                                <div class="cart-item" data-product-id="${item.product_id}">
                                    <input type="checkbox" class="checkout-checkbox" value="${item.product_id}">
                                    <div class="item-image">
                                        <img src="../Images/${item.image}" alt="${item.product_name}">
                                    </div>
                                    <div class="item-details">
                                        <h3>${item.product_name}</h3>
                                        <p class="item-type">Container Type</p>
                                        <div class="quantity-controls">
                                            <button class="quantity-btn minus" onclick="updateQuantity(${item.product_id}, -1)">-</button>
                                            <input type="number" value="${item.quantity}" min="1" class="quantity-input" 
                                                onchange="updateQuantity(${item.product_id}, 0, this.value)">
                                            <button class="quantity-btn plus" onclick="updateQuantity(${item.product_id}, 1)">+</button>
                                        </div>
                                    </div>
                                    <div class="item-price">
                                        <p class="price">Php ${itemTotal}</p>
                                        <button class="remove-btn" onclick="removeItem(${item.product_id})">
                                            <i class="fas fa-trash"></i> Remove
                                        </button>
                                    </div>
                                </div>
                            `;
                        });

                        itemCount.textContent = data.items.length;
                        updateOrderSummary();
                        attachOrderSummaryListeners();
                    } else {
                        emptyCartMessage.style.display = 'block';
                        cartContent.style.display = 'none';
                        itemCount.textContent = '0';
                    }
                })
                .catch(error => {
                    console.error('Error loading cart items:', error);
                    showNotification('Error loading cart items', 'error');
                });
        }

        function updateQuantity(productId, change, newValue) {
            const quantity = newValue || parseInt(document.querySelector(`.cart-item[data-product-id="${productId}"] .quantity-input`).value) + change;
            
            if (quantity < 1) return;

            fetch('update_cart_quantity.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ product_id: productId, quantity: quantity })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    loadCartItems();
                    showNotification('Cart updated successfully', 'success');
                } else {
                    showNotification('Error updating cart', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error updating cart', 'error');
            });
        }

        function removeItem(productId) {
            fetch('remove_cart_item.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ product_id: productId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    loadCartItems();
                    showNotification('Item removed from cart', 'success');
                } else {
                    showNotification('Error removing item', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error removing item', 'error');
            });
        }

        function updateOrderSummary() {
            const allCartItems = document.querySelectorAll('.cart-item');
            let subtotal = 0;
            let itemCount = 0;
            allCartItems.forEach(item => {
                const checkbox = item.querySelector('.checkout-checkbox');
                if (checkbox && checkbox.checked) {
                    const price = parseInt(item.querySelector('.price').textContent.replace('Php ', ''));
                    const quantity = parseInt(item.querySelector('.quantity-input').value);
                    subtotal += price;
                    itemCount += quantity;
                }
            });
            const deliveryFee = subtotal > 0 ? 50 : 0;
            const total = subtotal + deliveryFee;
            document.getElementById('subtotal').textContent = `Php ${subtotal}`;
            document.getElementById('total').textContent = `Php ${total}`;
            document.getElementById('item-count').textContent = itemCount;
        }

        function attachOrderSummaryListeners() {
            document.querySelectorAll('.checkout-checkbox').forEach(cb => {
                cb.addEventListener('change', updateOrderSummary);
            });
            document.querySelectorAll('.quantity-input').forEach(input => {
                input.addEventListener('change', updateOrderSummary);
            });
            document.querySelectorAll('.quantity-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    setTimeout(updateOrderSummary, 100); // Wait for quantity update
                });
            });
        }

        function showNotification(message, type) {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.className = `notification ${type}`;
            notification.style.display = 'block';

            setTimeout(() => {
                notification.style.display = 'none';
            }, 3000);
        }

        // Add event listener for Checkout Selected button
        const checkoutSelectedBtn = document.getElementById('checkout-selected-btn');
        if (checkoutSelectedBtn) {
            checkoutSelectedBtn.addEventListener('click', function() {
                const selectedItems = [];
                document.querySelectorAll('.checkout-checkbox:checked').forEach(checkbox => {
                    selectedItems.push(checkbox.value); // Collect product IDs of checked items
                });

                if (selectedItems.length > 0) {
                    // Redirect to checkout page with selected item IDs as URL parameters
                    window.location.href = 'Checkout.php?selected_items=' + selectedItems.join(',');
                } else {
                    showNotification('Please select at least one item to checkout.', 'error');
                }
            });
        }
    </script>
</body>
</html> 