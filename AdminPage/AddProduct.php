<?php
session_start();
require_once '../includes/db.php';

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $stock = intval($_POST['stock'] ?? 0);
    $price = floatval($_POST['price'] ?? 0.0);
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $image = $_FILES['image'] ?? null;

    if (empty($name) || empty($description) || empty($category) || $stock < 0 || $price < 0 || $image === null || $image['error'] !== UPLOAD_ERR_OK) {
        $message = 'Please fill all fields and provide a valid image.';
        $message_type = 'danger';
    } else {
        // Handle image upload
        $target_dir = "../Images/"; // Directory where images will be saved
        $image_name = uniqid() . '_' . basename($image['name']); // Generate a unique name
        $target_file = $target_dir . $image_name;
        $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($image['tmp_name']);
        if($check === false) {
            $message = 'File is not an image.';
            $message_type = 'danger';
        } else if ($image['size'] > 5000000) { // 5MB max size
            $message = 'Sorry, your file is too large.';
            $message_type = 'danger';
        } else if($image_file_type != "jpg" && $image_file_type != "png" && $image_file_type != "jpeg" && $image_file_type != "gif" ) {
            $message = 'Sorry, only JPG, JPEG, PNG & GIF files are allowed.';
            $message_type = 'danger';
        } else {
            if (move_uploaded_file($image['tmp_name'], $target_file)) {
                // Insert product into database
                $stmt = $pdo_makmak1->prepare('INSERT INTO products (name, image, stock, price, description, category) VALUES (?, ?, ?, ?, ?, ?)');
                if ($stmt->execute([$name, $image_name, $stock, $price, $description, $category])) {
                    $message = 'New product added successfully!';
                    $message_type = 'success';
                    // Redirect to stocks page after a short delay
                     header("Location: Stocks.php");
                     exit();
                } else {
                    $message = 'Error adding product.';
                    $message_type = 'danger';
                }
            } else {
                $message = 'Sorry, there was an error uploading your file.';
                $message_type = 'danger';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Add Product</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Add specific styles for AddProduct.php if needed */
        .add-product-form-container {
            max-width: 500px; /* Limit form width */
            margin: 40px auto; /* Center the form */
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .add-product-form-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
        }

        .form-group input[type="file"] {
            padding-top: 12px; /* Adjust padding for file input */
        }

        .form-actions {
            text-align: center;
            margin-top: 20px;
        }

        .add-product-submit-btn {
            background-color: #28a745; /* Green color */
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1em;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.1s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .add-product-submit-btn:hover {
            background-color: #218838; /* Darker green on hover */
            transform: translateY(-2px);
        }

        .add-product-submit-btn:active {
            background-color: #1e7e34; /* Even darker green when clicked */
            transform: translateY(0);
        }

        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo"></div>
            <nav class="nav">
                <a href="./AdminDashboard.php"><i class="fas fa-chart-pie"></i> Dashboard</a>
                <a href="./BookingsOrders.php"><i class="fas fa-calendar-check"></i> Bookings / Orders</a>
                <a href="./Stocks.php" class="active"><i class="fas fa-boxes"></i> Stocks</a>
                <a href="Analytics.php"><i class="fas fa-chart-line"></i> Analytics</a>
                <a href="../LoginPage/Userlogin.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
            <div class="sidebar-footer">
                <i class="fas fa-cog"></i>
                <i class="fas fa-bell"></i>
            </div>
        </aside>
        <!-- Main Content -->
        <main class="main">
            <div class="add-product-form-container">
                <h2>Add New Product</h2>
                <?php if ($message): ?>
                    <div class="alert alert-<?= $message_type ?>"> <?= $message ?> </div>
                <?php endif; ?>
                <form action="AddProduct.php" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name">Product Name:</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="image">Product Image:</label>
                        <input type="file" id="image" name="image" accept="image/*" required>
                    </div>
                    <div class="form-group">
                        <label for="stock">Initial Stock:</label>
                        <input type="number" id="stock" name="stock" value="0" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="price">Price:</label>
                        <input type="number" id="price" name="price" value="0.00" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea id="description" name="description" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="category">Category:</label>
                        <input type="text" id="category" name="category" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="add-product-submit-btn">Add Product</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html> 