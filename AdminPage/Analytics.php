<?php
session_start();
require '../includes/db.php';
// Total bookings (real)
$total_bookings = $pdo_makmak1->query("SELECT COUNT(*) FROM bookings WHERE delivery_date IS NOT NULL")->fetchColumn();
// Total orders (real)
$total_orders = $pdo_makmak1->query("SELECT COUNT(*) FROM orders")->fetchColumn();
// Most booked product (sum from bookings and orders)
$most_booked_stmt = $pdo_makmak1->query("
    SELECT p.name, SUM(qty) as total_qty FROM (
        SELECT product_id, SUM(quantity) as qty FROM bookings WHERE delivery_date IS NOT NULL GROUP BY product_id
        UNION ALL
        SELECT product_id, SUM(quantity) as qty FROM orders GROUP BY product_id
    ) all_orders
    JOIN products p ON all_orders.product_id = p.product_id
    GROUP BY all_orders.product_id
    ORDER BY total_qty DESC LIMIT 1
");
$most_booked = $most_booked_stmt->fetch(PDO::FETCH_ASSOC);
// Product sales (sum from bookings and orders)
$product_sales_stmt = $pdo_makmak1->query("
    SELECT p.name, SUM(qty) as total_qty FROM (
        SELECT product_id, SUM(quantity) as qty FROM bookings WHERE delivery_date IS NOT NULL GROUP BY product_id
        UNION ALL
        SELECT product_id, SUM(quantity) as qty FROM orders GROUP BY product_id
    ) all_orders
    JOIN products p ON all_orders.product_id = p.product_id
    GROUP BY all_orders.product_id
    ORDER BY total_qty DESC
");
$product_sales = $product_sales_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Analytics - Makmak</title>
  <link rel="stylesheet" href="../UserDashboard/style.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    .analytics-cards { display: flex; gap: 30px; margin-bottom: 40px; }
    .analytics-card { flex: 1; background: #fff; border-radius: 10px; box-shadow: 0 2px 8px rgba(44,62,80,0.08); padding: 30px 20px; text-align: center; }
    .analytics-card h2 { font-size: 2.2em; margin-bottom: 10px; color: #3498db; }
    .analytics-card span { color: #666; font-size: 1.1em; }
    .analytics-table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 10px; box-shadow: 0 2px 8px rgba(44,62,80,0.08); }
    .analytics-table th, .analytics-table td { padding: 12px; border-bottom: 1px solid #eee; text-align: center; }
    .analytics-table th { background: #f8f9fa; }
    .analytics-table tr:last-child td { border-bottom: none; }
    .chart-container {
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(44,62,80,0.08);
      padding: 20px;
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
      <a href="./AdminDashboard.php"><i class="fas fa-chart-pie"></i> Dashboard</a>
      <a href="BookingsOrders.php"><i class="fas fa-calendar-check"></i> Bookings / Orders</a>
      <a href="Stocks.php"><i class="fas fa-boxes"></i> Stocks</a>
      <a href="Analytics.php" class="active"><i class="fas fa-chart-line"></i> Analytics</a>
      <a href="../AdminLogin/AdminLogin.php?logout=1"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
    
  </aside>
  <!-- Main Content -->
  <main class="main">
    <h2 style="margin-bottom: 32px;">Analytics Overview</h2>
    <div class="analytics-cards">
      <div class="analytics-card">
        <h2><?= $total_bookings ?></h2>
        <span>Total Bookings</span>
      </div>
      <div class="analytics-card">
        <h2><?= $total_orders ?></h2>
        <span>Total Orders</span>
      </div>
      <div class="analytics-card">
        <h2><?= htmlspecialchars($most_booked['name'] ?? 'N/A') ?></h2>
        <span>Most Booked Product</span>
      </div>
    </div>
    <h3 style="margin-bottom: 18px;">Product Sales</h3>
    <div class="chart-container">
      <canvas id="salesChart"></canvas>
    </div>
  </main>
</div>

<script>
// Prepare data for the chart
const productData = <?php
  $data = [];
  foreach ($product_sales as $row) {
    $data[] = [
      'name' => $row['name'],
      'quantity' => $row['total_qty']
    ];
  }
  echo json_encode($data);
?>;

// Create the chart
const ctx = document.getElementById('salesChart').getContext('2d');
new Chart(ctx, {
  type: 'bar',
  data: {
    labels: productData.map(item => item.name),
    datasets: [{
      label: 'Total Quantity Sold',
      data: productData.map(item => item.quantity),
      backgroundColor: 'rgba(52, 152, 219, 0.7)',
      borderColor: 'rgba(52, 152, 219, 1)',
      borderWidth: 1
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
      y: {
        beginAtZero: true,
        ticks: {
          stepSize: 1
        }
      }
    },
    plugins: {
      legend: {
        display: true,
        position: 'top'
      }
    }
  }
});
</script>
</body>
</html> 