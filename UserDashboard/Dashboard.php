<?php
// Start 
session_start();
// Include the database connection file
require '../includes/db.php';

// Check if the user is logged in, if not, redirect to the login page
if (!isset($_SESSION['user_id'])) {
    header('Location: ../LoginPage/Userlogin.php');
    exit();
}

// Fetch products from the database
$products = $pdo_makmak1->query('SELECT * FROM products ORDER BY product_id DESC')->fetchAll(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Dashboard</title>
  <link rel="stylesheet" href="style.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>



<div id="ubeModal" class="modal">
  <div class="modal-content">
    <span class="close-btn">&times;</span>
    <img id="modal-product-image" src="" alt="Ice Cream">
    <h2 id="modal-product-name"></h2>
    <p id="modal-product-desc"></p>
    <p><strong>Price:</strong> Php <span id="modal-product-price"></span></p>

    
    <!-- Quantity Input field -->
    <label for="quantity"><strong>Quantity:</strong></label>
    <input type="number" id="quantity" name="quantity" min="1" value="1">
    


    
    <!-- Button to confirm the order -->
    <button class="btn-confirm">Buy Now</button>
  </div>
</div>










<!-- BODY of the dashboard page -->
<body>
  <!-- Container for the entire dashboard layout -->
  <div class="container">

    <!-- Sidebar navigation menu -->
    <aside class="sidebar">
      <!-- Logo area -->
      <div  class="logo"></div>
      <!-- Navigation links -->
      <nav class="nav">
        
        <!-- Link to the Home page (active) -->
        <a href="../UserDashboard/Dashboard.php" class="active">
          <i class="fas fa-home">
          </i> Home
        </a>

        <!-- Link to the Booking page -->
        <a href="../UserDashboard/Booking.php" >
          <i class="fas fa-calendar-check">
          </i> Book Ice Cream
        </a>

        <!-- Link to the Cart page -->
        <a href="../UserDashboard/Cart.php">
          <i class="fas fa-shopping-cart">
          </i> Your Cart
        </a>

        <!-- Link to the My Orders page -->
        <a href="../UserDashboard/MyOrders.php">
          <i class="fas fa-clipboard-list">
          </i> My Orders
        </a>

        <!-- Link to the Profile page -->
        <a href="../UserDashboard/profile.php">
          <i class="fas fa-user">
          </i> Profile
        </a>

        <!-- Link to logout, redirects to Userlogin page -->
        <a href="../LoginPage/Userlogin.php">
          <i class="fas fa-sign-out-alt">
          </i> Logout
        </a>

        
      </nav>
    </aside>

    <!-- Main content area -->
    <main class="main">


      <!-- Search bar and notification area -->
      <div class="search-bar" style="display: flex; align-items: center; gap: 16px; position: relative;">
        <input type="text" placeholder="Search ice cream">



        <!-- Notification Bell Icon -->
        <div class="notification-bell" id="notificationBell" style="position: relative; cursor: pointer; margin-left: 10px;">
          <i class="fas fa-bell fa-lg"></i>
          <!-- Notification badge (initially hidden) -->
          <span class="notification-badge" id="notificationBadge" style="position: absolute; top: -6px; right: -6px; background: #43e97b; color: #fff; border-radius: 50%; padding: 2px 6px; font-size: 12px; display: none;">0</span>
        </div>
        <!-- Notification Dropdown (initially hidden) -->
        <div id="notificationDropdown" style="display: none; position: absolute; top: 40px; right: 0; background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.15); border-radius: 8px; width: 340px; z-index: 1000;">
          <div style="padding: 16px; border-bottom: 1px solid #eee; font-weight: bold;">Notifications</div>
          <div style="padding: 16px; color: #888;">No new notifications</div>
        </div>
      </div>

      <!-- Categories section -->
      <section class="categories">
        <h3>Best Seller!</h3>
        <div class="category-list">
          <!-- Repeatable category items, each displaying an ice cream product -->

          <div class="categorycard1" data-product-id="1">
            <img src="../Images/Ube.png" alt="Ube Ice Cream">
            <h4>Ube Ice Cream</h4>
            <p>Php 299</p>
            <div class="category-buttons">
              <button class="cart-btn">Add to Cart</button>
              <button class="buy-btn" data-modal="ubeModal">Buy Now</button>
            </div>
          </div>

          <div class="categorycard1" data-product-id="2">
            <img src="../Images/Mango.png" alt="Mango Ice Cream">
            <h4>Mango Ice Cream</h4>
            <p>Php 299</p>
            <div class="category-buttons">
              <button class="cart-btn">Add to Cart</button>
              <button class="buy-btn" data-modal="ubeModal">Buy Now</button>
            </div>
          </div>

          
          <!-- etc -->
        </div>
      </section>
<br>











      <!-- Best Seller section -->
      <section class="popular-dishes">
        <h3>Ice Creams</h3>

        <div class="dishes">
          <!-- Repeatable dish items, each displaying a best-selling ice cream product -->
          <?php foreach ($products as $product): ?>
          <div class="dish" data-product-id="<?= $product['product_id'] ?>">
            <img src="../Images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
            <h4><?= htmlspecialchars($product['name']) ?></h4>
            <p>Php <?= htmlspecialchars(number_format($product['price'], 2)) ?></p>
            <div class="category-buttons">
              <button class="cart-btn">Add to Cart</button>
              <button class="buy-btn" data-modal="ubeModal">Buy Now</button>
            </div>
          </div>
          <?php endforeach; ?>

        </div>
      </section>






      

      
      <!-- Section displaying all ice cream products -->
      <section class="Products">
        <div class="icecream">
          
          <!-- Repeatable ice cream items -->
          <div class="cream" data-product-id="2">
            <img src="../Images/Mango.png" alt="Mango Ice Cream">
            <h4>Mango Ice Cream</h4>  
            <p>Php 299</p>
            <div class="category-buttons">
              <button class="cart-btn">Add to Cart</button>
              <button class="buy-btn" data-modal="ubeModal">Buy Now</button>
            </div>
          </div>
      
          <div class="cream" data-product-id="1">
            <img src="../Images/Ube.png" alt="Ube Ice Cream">
            <h4>Ube Ice Cream</h4>
            <p>Php 299</p>
            <div class="category-buttons">
              <button class="cart-btn">Add to Cart</button>
              <button class="buy-btn" data-modal="ubeModal">Buy Now</button>
            </div>
          </div>
      
          <div class="cream" data-product-id="3">
            <img src="../Images/chocolate.png" alt="Chocolate Ice Cream">
            <h4>Chocolate Ice Cream</h4>
            <p>Php 299</p>
            <div class="category-buttons">
              <button class="cart-btn">Add to Cart</button>
              <button class="buy-btn" data-modal="ubeModal">Buy Now</button>
            </div>
          </div>

          <div class="cream" data-product-id="4">
            <img src="../Images/cookies.png" alt="Cookies & Cream">
            <h4>Cookies & Cream</h4>
            <p>Php 299</p>
            <div class="category-buttons">
              <button class="cart-btn">Add to Cart</button>
              <button class="buy-btn" data-modal="ubeModal">Buy Now</button>
            </div>
          </div>
      
        </div>
      </section>











      
    </main>



  </div>


  <!-- JAVASCRIPT-->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Make product data available in JavaScript
      const productsData = <?= json_encode($products) ?>;

      // Add to Cart functionality for all product cards
      const addToCartButtons = document.querySelectorAll('.cart-btn');
      addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
          const productCard = this.closest('.categorycard1') || this.closest('.dish') || this.closest('.cream');
          const productId = productCard.dataset.productId;
          const productName = productCard.querySelector('h4').textContent;
          const price = productCard.querySelector('p').textContent.replace('Php ', '');
          const productImage = productCard.querySelector('img').src.split('/').pop();
          // Create form data
          const formData = new FormData();
          formData.append('product_id', productId);
          formData.append('product_name', productName);
          formData.append('price', price);
          formData.append('quantity', 1);
          formData.append('image', productImage);
          // Send to PHP
          fetch('add_to_cart.php', {
            method: 'POST',
            body: formData
          })
          .then(response => response.json())
          .then(data => {
            if (data.status === 'success') {
              showNotification(`${productName} added to cart!`, 'success');
              // Redirect to cart page after a short delay
              setTimeout(() => {
                window.location.href = 'Cart.php';
              }, 1500);
            } else {
              showNotification('Error adding item to cart', 'error');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            showNotification('Error adding item to cart', 'error');
          });
        });
      });

      // Buy Now button functionality (dynamic modal)
      const buyNowButtons = document.querySelectorAll('.buy-btn');
      buyNowButtons.forEach(button => {
        button.addEventListener('click', function() {
          const productCard = this.closest('.categorycard1') || this.closest('.dish') || this.closest('.cream');
          const productId = productCard.dataset.productId;
          const productName = productCard.querySelector('h4').textContent;
          const price = productCard.querySelector('p').textContent.replace('Php ', '');
          const productImage = productCard.querySelector('img').src;
          // Update modal content
          document.getElementById('modal-product-image').src = productImage;
          document.getElementById('modal-product-name').textContent = productName;
          document.getElementById('modal-product-price').textContent = price;
          document.getElementById('modal-product-desc').textContent = `Delicious ${productName.toLowerCase()} ice cream. Creamy, rich, and perfect for summer days!`;
          // Store product info for confirm button
          const modal = document.getElementById('ubeModal');
          modal.dataset.productId = productId;
          modal.dataset.productName = productName;
          modal.dataset.price = price;
          modal.dataset.productImage = productImage;
          // Show modal
          modal.style.display = 'block';
        });
      });

      // Confirm Order button functionality (add correct product to cart)
      const confirmBtn = document.querySelector('.btn-confirm');
      confirmBtn.addEventListener('click', function() {
        const modal = document.getElementById('ubeModal');
        const productId = modal.dataset.productId;
        const productName = modal.dataset.productName;
        const price = modal.dataset.price;
        const productImage = modal.dataset.productImage.split('/').pop();
        const quantity = document.getElementById('quantity').value;
        // Create form data
        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('product_name', productName);
        formData.append('price', price);
        formData.append('quantity', quantity);
        formData.append('image', productImage);
        // Send to PHP
        fetch('add_to_cart.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.status === 'success') {
            showNotification(`${productName} added to cart!`, 'success');
            // Redirect to cart page after a short delay
            setTimeout(() => {
              window.location.href = 'Cart.php';
            }, 1500);
          } else {
            showNotification('Error adding item to cart', 'error');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          showNotification('Error adding item to cart', 'error');
        });
        // Close modal
        modal.style.display = 'none';
      });

      // Close modal functionality
      const closeButtons = document.querySelectorAll('.close-btn');
      closeButtons.forEach(button => {
        button.addEventListener('click', function() {
          const modal = this.closest('.modal');
          if (modal) {
            modal.style.display = 'none';
          }
        });
      });

      // Close modal when clicking outside
      window.addEventListener('click', function(event) {
        if (event.target.classList.contains('modal')) {
          event.target.style.display = 'none';
        }
      });

      function showNotification(message, type) {
        const existingNotification = document.querySelector('.notification');
        if (existingNotification) {
          existingNotification.remove();
        }

        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
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
        }, 1500);
      }

      // Unified Notification Bell Toggle and Fetch
      const notificationBell = document.getElementById('notificationBell');
      const notificationDropdown = document.getElementById('notificationDropdown');
      const notificationBadge = document.getElementById('notificationBadge');
      if (notificationBell && notificationDropdown) {
        notificationBell.addEventListener('click', function(e) {
          e.stopPropagation();
          if (notificationDropdown.style.display !== 'block') {
            fetch('get_notifications.php')
              .then(res => res.json())
              .then(data => {
                let notifHtml = '';
                let unreadCount = 0;
                if (data.status === 'success' && data.notifications.length > 0) {
                  notifHtml = data.notifications.map(n => {
                    if (n.is_read == 0) unreadCount++;
                    return `<div style=\"padding: 12px 16px; border-bottom: 1px solid #eee; color: ${n.is_read == 0 ? '#222' : '#888'}; background: ${n.is_read == 0 ? '#f8f9fa' : '#fff'};\">${n.message}<br><span style='font-size:12px;color:#aaa;'>${n.created_at}</span></div>`;
                  }).join('');
                } else {
                  notifHtml = '<div style="padding: 16px; color: #888;">No new notifications</div>';
                }
                notificationDropdown.innerHTML = `<div style='padding: 16px; border-bottom: 1px solid #eee; font-weight: bold;'>Notifications</div>${notifHtml}`;
                if (unreadCount > 0) {
                  notificationBadge.textContent = unreadCount;
                  notificationBadge.style.display = 'block';
                } else {
                  notificationBadge.style.display = 'none';
                }
              });
          }
          notificationDropdown.style.display = notificationDropdown.style.display === 'block' ? 'none' : 'block';
        });
        document.addEventListener('click', function(e) {
          if (!notificationDropdown.contains(e.target) && e.target !== notificationBell) {
            notificationDropdown.style.display = 'none';
          }
        });
      }

      // SEARCH BAR FUNCTIONALITY
      const searchInput = document.querySelector('.search-bar input[type="text"]');
      searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        // All product card selectors
        const allCards = [
          ...document.querySelectorAll('.categorycard1'),
          ...document.querySelectorAll('.dish'),
          ...document.querySelectorAll('.cream')
        ];
        allCards.forEach(card => {
          const name = card.querySelector('h4').textContent.toLowerCase();
          if (name.includes(query)) {
            card.style.display = '';
          } else {
            card.style.display = 'none';
          }
        });
      });
    });
  </script>
  <div id="ubeModal" class="modal"></div>

  <!-- Notification Dropdown/Modal Placeholder -->
  <div id="notificationDropdown" style="display: none; position: absolute; top: 70px; right: 40px; background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.15); border-radius: 8px; width: 300px; z-index: 1000;">
    <div style="padding: 16px; border-bottom: 1px solid #eee; font-weight: bold;">Notifications</div>
    <div style="padding: 16px; color: #888;">No new notifications</div>
  </div>

</body>
  
</html>
