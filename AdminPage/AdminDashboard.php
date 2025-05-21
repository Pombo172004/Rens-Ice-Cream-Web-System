<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../AdminLogin/AdminLogin.php');
    exit();
}
$admin_username = $_SESSION['admin_username'];
require_once __DIR__ . '/../includes/db.php';

// Fetch metrics
$total_bookings = $pdo_makmak1->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
$approved_bookings = $pdo_makmak1->query("SELECT COUNT(*) FROM bookings WHERE status = 'approved'")->fetchColumn();
$pending_bookings = $pdo_makmak1->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'")->fetchColumn();
$denied_bookings = $pdo_makmak1->query("SELECT COUNT(*) FROM bookings WHERE status = 'denied'")->fetchColumn();
$approved_pct = $total_bookings ? round($approved_bookings / $total_bookings * 100) : 0;
$pending_pct = $total_bookings ? round($pending_bookings / $total_bookings * 100) : 0;
$denied_pct = $total_bookings ? round($denied_bookings / $total_bookings * 100) : 0;
$total_orders = $pdo_makmak1->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$total_users = $pdo_makmak1->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_products = $pdo_makmak1->query("SELECT COUNT(*) FROM products")->fetchColumn();


// Monthly income from bookings (current year)
$year = date('Y');
$monthly_income = array_fill(1, 12, 0);
$stmt = $pdo_makmak1->prepare("
    SELECT MONTH(delivery_date) as month, SUM(b.quantity * p.price) as income
    FROM bookings b
    JOIN products p ON b.product_id = p.product_id
    WHERE b.delivery_date IS NOT NULL AND YEAR(b.delivery_date) = ? AND b.status = 'approved'
    GROUP BY MONTH(b.delivery_date)
");
$stmt->execute([$year]);
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $monthly_income[(int)$row['month']] = (float)$row['income'];
}
$monthly_income_js = json_encode(array_values($monthly_income));
$monthly_total = array_sum($monthly_income);
$year_total = $monthly_total; // For now, same as monthly if only current year
$prev_year = $year - 1;
$stmt_prev = $pdo_makmak1->prepare("
    SELECT SUM(b.quantity * p.price) as income
    FROM bookings b
    JOIN products p ON b.product_id = p.product_id
    WHERE b.delivery_date IS NOT NULL AND YEAR(b.delivery_date) = ? AND b.status = 'approved'
");
$stmt_prev->execute([$prev_year]);
$prev_year_total = (float)($stmt_prev->fetchColumn() ?: 0);
$month = date('n');
$month_income = $monthly_income[$month];
$month_label = date('F');
$month_change = $prev_year_total ? (($monthly_income[$month] - $prev_year_total/12) / ($prev_year_total/12)) * 100 : 0;

// Monthly order counts (current year) for approved orders
$monthly_orders = array_fill(1, 12, 0);
$stmt_orders = $pdo_makmak1->prepare("
    SELECT MONTH(order_date) as month, COUNT(*) as order_count
    FROM orders
    WHERE order_date IS NOT NULL AND YEAR(order_date) = ? AND status = 'approved'
    GROUP BY MONTH(order_date)
");
$stmt_orders->execute([$year]);
foreach ($stmt_orders->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $monthly_orders[(int)$row['month']] = (int)$row['order_count'];
}
$monthly_orders_js = json_encode(array_values($monthly_orders));

// Fetch order status metrics
$total_orders_real = $pdo_makmak1->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$approved_orders = $pdo_makmak1->query("SELECT COUNT(*) FROM orders WHERE status = 'approved'")->fetchColumn();
$pending_orders = $pdo_makmak1->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
$denied_orders = $pdo_makmak1->query("SELECT COUNT(*) FROM orders WHERE status = 'denied'")->fetchColumn();
$approved_orders_pct = $total_orders_real ? round($approved_orders / $total_orders_real * 100) : 0;
$pending_orders_pct = $total_orders_real ? round($pending_orders / $total_orders_real * 100) : 0;
$denied_orders_pct = $total_orders_real ? round($denied_orders / $total_orders_real * 100) : 0;

// Monthly total order counts (current year)
$monthly_total_orders = array_fill(1, 12, 0);
$stmt_total_orders = $pdo_makmak1->prepare("
    SELECT MONTH(order_date) as month, COUNT(*) as order_count
    FROM orders
    WHERE order_date IS NOT NULL AND YEAR(order_date) = ?
    GROUP BY MONTH(order_date)
");
$stmt_total_orders->execute([$year]);
foreach ($stmt_total_orders->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $monthly_total_orders[(int)$row['month']] = (int)$row['order_count'];
}
$monthly_total_orders_js = json_encode(array_values($monthly_total_orders));

// Only count incoming/future bookings (delivery_date today or later)
$today = date('Y-m-d');
$incoming_bookings_stmt = $pdo_makmak1->prepare("SELECT status FROM bookings WHERE delivery_date >= ?");
$incoming_bookings_stmt->execute([$today]);
$incoming_bookings = $incoming_bookings_stmt->fetchAll(PDO::FETCH_COLUMN);
$total_incoming = count($incoming_bookings);
$approved_incoming = count(array_filter($incoming_bookings, fn($s) => $s === 'approved'));
$pending_incoming = count(array_filter($incoming_bookings, fn($s) => $s === 'pending'));
$denied_incoming = count(array_filter($incoming_bookings, fn($s) => $s === 'denied'));
$approved_incoming_pct = $total_incoming ? round($approved_incoming / $total_incoming * 100) : 0;
$pending_incoming_pct = $total_incoming ? round($pending_incoming / $total_incoming * 100) : 0;
$denied_incoming_pct = $total_incoming ? round($denied_incoming / $total_incoming * 100) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../UserDashboard/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background: #f6f8fb; }
        .main { background: #f6f8fb; min-height: 100vh; }
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        .dashboard-title {
            font-size: 2em;
            font-weight: bold;
            color: #222;
        }
        .dashboard-subtitle {
            color: #888;
            font-size: 1.1em;
        }
        .dashboard-content {
            display: flex;
            gap: 32px;
            flex-wrap: wrap;
        }
        .dashboard-main {
            flex: 2 1 600px;
            min-width: 350px;
        }
        .dashboard-side {
            flex: 1 1 260px;
            min-width: 260px;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        .dashboard-card, .dashboard-table, .dashboard-overview {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 12px rgba(44,62,80,0.08);
            padding: 24px 28px;
            margin-bottom: 24px;
        }
        .dashboard-card {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }
        .dashboard-card .card-value {
            font-size: 2em;
            font-weight: bold;
            color: #222;
        }
        .dashboard-card .card-label {
            color: #888;
            font-size: 1.1em;
        }
        .dashboard-table table {
            width: 100%;
            border-collapse: collapse;
        }
        .dashboard-table th, .dashboard-table td {
            padding: 10px 8px;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
        }
        .dashboard-table th {
            color: #888;
            font-weight: 600;
            background: #f6f8fb;
        }
        .dashboard-overview .overview-row {
            display: flex;
            align-items: center;
            margin-bottom: 16px;
            gap: 12px;
        }
        .dashboard-overview .overview-label {
            flex: 1 1 120px;
            color: #666;
            font-size: 1em;
        }
        .dashboard-overview .overview-bar {
            flex: 3 1 180px;
            background: #e5e7eb;
            border-radius: 8px;
            height: 12px;
            position: relative;
            overflow: hidden;
        }
        .dashboard-overview .overview-bar-inner {
            height: 100%;
            border-radius: 8px;
            position: absolute;
            left: 0; top: 0;
        }
        .dashboard-overview .overview-value {
            margin-left: 12px;
            font-weight: bold;
            color: #222;
            min-width: 32px;
        }
        @media (max-width: 1100px) {
            .dashboard-content { flex-direction: column; }
            .dashboard-main, .dashboard-side { min-width: 0; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo"></div>
            <nav class="nav">
                <a href="./AdminDashboard.php" class="active"><i class="fas fa-chart-pie"></i> Dashboard</a>
                <a href="./BookingsOrders.php"><i class="fas fa-calendar-check"></i> Bookings / Orders</a>
                <a href="./Stocks.php"><i class="fas fa-boxes"></i> Stocks</a>
                <a href="./Analytics.php"><i class="fas fa-chart-line"></i> Analytics</a>
                <a href="../AdminLogin/AdminLogin.php?logout=1"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
            
        </aside>


        <!-- Main Content -->
        <main class="main">
            <div class="dashboard-header">
                <div>
                    <div class="dashboard-title">Directory Dashboard</div>
                    <div class="dashboard-subtitle">System centralized view of key data, metrics, and performance indicators</div>
                </div>
                <div style="display:flex;align-items:center;gap:18px;">
                    <span style="color:#888;font-size:1em;">Dashboard &gt; Address Book</span>
                    <img src="../Images/Pombo Christian Dave.jpg" alt="Admin" style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
                </div>
            </div>
            <div class="dashboard-content">
                <!-- Main Left -->
                <div class="dashboard-main">
                    <div class="dashboard-card" style="padding-bottom:0;">
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <div style="font-weight:600;font-size:1.1em;">Monthly Income stats for <?= date('F Y') ?></div>
                            <div style="display:flex;gap:8px;">
                                <button style="background:#f6f8fb;border:none;padding:6px 16px;border-radius:6px;cursor:pointer;font-weight:500;">Today</button>
                                <button style="background:#f6f8fb;border:none;padding:6px 16px;border-radius:6px;cursor:pointer;font-weight:500;">Weekly</button>
                                <button style="background:#ffd600;border:none;padding:6px 16px;border-radius:6px;cursor:pointer;font-weight:500;">Monthly</button>
                            </div>
                        </div>
                        <canvas id="incomeChart" height="80"></canvas>
                        <div style="display:flex;justify-content:space-between;margin-top:18px;font-size:1em;">
                            <div><span style="font-weight:bold;">Php <?= number_format($month_income, 2) ?></span> <span style="color:#22c55e;font-size:0.95em;"><?= $month_change >= 0 ? '+' : '' ?><?= number_format($month_change, 1) ?>%</span><br><span style="color:#888;font-size:0.95em;">This month (<?= $month_label ?>)</span></div>
                            <div><span style="font-weight:bold;">Php <?= number_format($year_total, 2) ?></span><br><span style="color:#888;font-size:0.95em;">This Year</span></div>
                            <div><span style="font-weight:bold;">Php <?= number_format($prev_year_total, 2) ?></span><br><span style="color:#888;font-size:0.95em;">Previous Year</span></div>
                        </div>
                    </div>

                    <!-- New card for Orders Graph -->
                    <div class="dashboard-card" style="margin-top: 24px; padding-bottom: 0;">
                        <div style="font-weight: 600; font-size: 1.1em; margin-bottom: 10px;">Monthly Orders for <?= date('F Y') ?></div>
                        <canvas id="ordersChart" height="80"></canvas>
                    </div>

                    <div style="display:flex;gap:24px;margin-top:24px;flex-wrap:wrap;">
                        <div class="dashboard-table" style="flex:1 1 320px;min-width:260px;">
                            <div style="font-weight:600;font-size:1.1em;margin-bottom:10px;">Orders Overview</div>
                            <table>
                                <tr><th>Status</th><th>No. Of Orders</th><th>Percent</th></tr>
                                <tr><td>Approved</td><td><?= $approved_orders ?></td><td><?= $approved_orders_pct ?>%</td></tr>
                                <tr><td>Pending</td><td><?= $pending_orders ?></td><td><?= $pending_orders_pct ?>%</td></tr>
                                <tr><td>Denied</td><td><?= $denied_orders ?></td><td><?= $denied_orders_pct ?>%</td></tr>
                                <tr style="font-weight:bold;"><td>Total</td><td><?= $total_orders_real ?></td><td>100%</td></tr>
                            </table>
                        </div>
                        <div class="dashboard-overview" style="flex:1 1 320px;min-width:260px;">
                            <div style="font-weight:600;font-size:1.1em;margin-bottom:10px;">Overview</div>
                            <div class="overview-row">
                                <div class="overview-label">Approved Bookings</div>
                                <div class="overview-bar" style="background:#e5e7eb;">
                                    <div class="overview-bar-inner" style="width:<?= $approved_incoming_pct ?>%;background:#3b82f6;"></div>
                                </div>
                                <div class="overview-value"><?= $approved_incoming_pct ?>%</div>
                            </div>
                            <div class="overview-row">
                                <div class="overview-label">Pending Bookings</div>
                                <div class="overview-bar" style="background:#e5e7eb;">
                                    <div class="overview-bar-inner" style="width:<?= $pending_incoming_pct ?>%;background:#ffd600;"></div>
                                </div>
                                <div class="overview-value"><?= $pending_incoming_pct ?>%</div>
                            </div>
                            <div class="overview-row">
                                <div class="overview-label">Denied Bookings</div>
                                <div class="overview-bar" style="background:#e5e7eb;">
                                    <div class="overview-bar-inner" style="width:<?= $denied_incoming_pct ?>%;background:#e74c3c;"></div>
                                </div>
                                <div class="overview-value"><?= $denied_incoming_pct ?>%</div>
                            </div>
                            <div class="overview-row">
                                <div class="overview-label">Total Bookings</div>
                                <div class="overview-bar" style="background:#e5e7eb;">
                                    <div class="overview-bar-inner" style="width:100%;background:#888;"></div>
                                </div>
                                <div class="overview-value"><?= $total_incoming ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Side Cards -->
                <div class="dashboard-side">
                    <div class="dashboard-card">
                        <div class="card-label"><i class="fas fa-boxes"></i> Total Products</div>
                        <div class="card-value"><?= $total_products ?></div>
                    </div>
                    <div class="dashboard-card">
                        <div class="card-label"><i class="fas fa-users"></i> Total Users</div>
                        <div class="card-value"><?= $total_users ?></div>
                    </div>
                    <div class="dashboard-card">
                        <div class="card-label"><i class="fas fa-calendar-check"></i> Total Bookings</div>
                        <div class="card-value"><?= $total_bookings ?></div>
                    </div>
                    <div class="dashboard-card">
                        <div class="card-label"><i class="fas fa-clipboard-list"></i> Total Orders</div>
                        <div class="card-value"><?= $total_orders ?></div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script>
        // Chart.js example data
        const ctx = document.getElementById('incomeChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [
                    {
                        type: 'bar',
                        label: 'Income',
                        data: <?= $monthly_income_js ?>,
                        backgroundColor: '#3b82f6',
                        borderRadius: 6,
                        barThickness: 18,
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f0f0f0' } },
                    x: { grid: { color: '#f0f0f0' } }
                }
            }
        });
    </script>
    <script>
        // Chart.js for Monthly Orders
        const ordersCtx = document.getElementById('ordersChart').getContext('2d');
        new Chart(ordersCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [
                    {
                        label: 'Total Orders',
                        data: <?= $monthly_total_orders_js ?>,
                        backgroundColor: '#ffd600', // Yellow color
                        borderRadius: 6,
                        barThickness: 18,
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f0f0f0' } },
                    x: { grid: { color: '#f0f0f0' } }
                }
            }
        });
    </script>
</body>
</html> 