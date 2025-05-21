<?php
session_start();
require_once '../includes/db.php';

// Handle AJAX stock update or add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = intval($data['id'] ?? 0);
    $stock = isset($data['stock']) ? intval($data['stock']) : null;
    $add = isset($data['add']) ? intval($data['add']) : null;

    if ($id > 0) {
        if ($add !== null) {
            $stmt = $pdo_makmak1->prepare('UPDATE products SET stock = stock + ? WHERE product_id = ?');
            $success = $stmt->execute([$add, $id]);
            echo json_encode(['success' => $success, 'message' => $success ? 'Stock added!' : 'Add failed.', 'newStock' => $add]);
        } elseif ($stock !== null) {
            $stmt = $pdo_makmak1->prepare('UPDATE products SET stock = ? WHERE product_id = ?');
            $success = $stmt->execute([$stock, $id]);
            echo json_encode(['success' => $success, 'message' => $success ? 'Stock updated!' : 'Update failed.', 'newStock' => $stock]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No stock value provided.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid product ID.']);
    }
    exit;
}

$products = $pdo_makmak1->query('SELECT * FROM products ORDER BY product_id DESC')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Ice Cream Stocks</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .stocks-table td, .stocks-table th { vertical-align: middle; }
        .product-img { width: 48px; height: 48px; object-fit: cover; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.07); margin-right: 10px; }
        .product-name-cell { display: flex; align-items: center; gap: 10px; }
        .save-btn, .add-btn {
            color: #fff;
            border: none;
            padding: 7px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1em;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background 0.2s;
        }
        .save-btn { background: #3498db; }
        .save-btn:hover { background: #217dbb; }
        .add-btn { background: #27ae60; }
        .add-btn:hover { background: #219150; }
        .stock-input, .add-stock-input {
            width: 60px;
            text-align: right;
            padding: 6px;
            border-radius: 4px;
            border: 1px solid #ddd;
            font-size: 1em;
        }
        .stocks-table tr.updated-row { background: #eafaf1 !important; transition: background 0.5s; }
        @media (max-width: 900px) {
            .stocks-table td, .stocks-table th { padding: 8px; font-size: 0.95em; }
            .product-img { width: 32px; height: 32px; }
        }
        /* Toast notification */
        .toast {
            position: fixed;
            top: 30px;
            right: 30px;
            background: #222;
            color: #fff;
            padding: 16px 28px;
            border-radius: 8px;
            font-size: 1.1em;
            z-index: 9999;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s, top 0.3s;
        }
        .toast.show { opacity: 1; pointer-events: auto; top: 50px; }
        .stock-input:focus {
            border: 1.5px solid #3498db;
            background: #f0f8ff;
        }
        .stocks-table td .stock-input {
            border: 1px solid #ddd;
            background: #fafbfc;
            transition: border 0.2s, background 0.2s;
        }
        .stocks-table td .stock-input[disabled] {
            background: #eee;
            color: #aaa;
        }
        .add-stock-input, .remove-stock-input {
            width: 60px;
            text-align: right;
            padding: 6px;
            border-radius: 4px;
            border: 1px solid #ddd;
            font-size: 1em;
            margin-right: 8px;
            box-sizing: border-box;
        }
        .add-btn, .remove-btn {
            color: #fff;
            border: none;
            padding: 7px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1em;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background 0.2s;
            height: 38px;
            min-width: 90px;
            justify-content: center;
        }
        .add-btn { background: #27ae60; }
        .add-btn:hover { background: #219150; }
        .remove-btn { background: #e74c3c; }
        .remove-btn:hover { background: #c0392b; }
        .stocks-table td {
            vertical-align: middle;
        }
        .stocks-table td .add-stock-input,
        .stocks-table td .remove-stock-input {
            margin-bottom: 0;
        }
        .stocks-table td .add-btn,
        .stocks-table td .remove-btn {
            margin-bottom: 0;
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
                <a href="../AdminLogin/AdminLogin.php?logout=1"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
            
        </aside>
        <!-- Main Content -->
        <main class="main">
            <section class="stocks-section">
                <h2>Ice Cream Stocks Admin</h2>
                <div class="card">
                    <table class="stocks-table">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Stock</th>
                            <th>Add Stock</th>
                            <th>Remove Stock</th>
                        </tr>
                        <?php foreach ($products as $prod): ?>
                        <tr data-id="<?= $prod['product_id'] ?>">
                            <td><?= $prod['product_id'] ?></td>
                            <td class="product-name-cell">
                                <img class="product-img" src="../Images/<?= htmlspecialchars($prod['image']) ?>" alt="<?= htmlspecialchars($prod['name']) ?>">
                                <?= htmlspecialchars($prod['name']) ?>
                            </td>
                            <td class="current-stock">
                                <?php
                                    $stock = (int)$prod['stock'];
                                    if ($stock == 0) {
                                        echo '<span class="out-of-stock">0</span>';
                                    } elseif ($stock <= 5) {
                                        echo '<span class="low-stock">' . $stock . '</span>';
                                    } else {
                                        echo '<span class="in-stock">' . $stock . '</span>';
                                    }
                                ?>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0;">
                                    <input type="number" class="add-stock-input" value="1" min="1">
                                    <button class="add-btn"><i class="fas fa-plus"></i> Add</button>
                                </div>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0;">
                                    <input type="number" class="remove-stock-input" value="1" min="1">
                                    <button class="remove-btn"><i class="fas fa-minus"></i> Remove</button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <!-- Add Products Button -->
                <div class="button-container">
                    <button class="add-product-btn" onclick="redirectToAddProduct()">Add Product</button>
                </div>
            </section>
        </main>
    </div>
    <div id="toast" class="toast"></div>
    <div id="stockConfirmModal" style="display:none;position:fixed;z-index:3000;left:0;top:0;width:100vw;height:100vh;background:rgba(0,0,0,0.25);justify-content:center;align-items:center;">
      <div style="background:#fff;border-radius:12px;box-shadow:0 2px 12px rgba(44,62,80,0.12);padding:32px 28px;min-width:320px;text-align:center;position:relative;max-width:95vw;">
        <span id="closeStockModal" style="position:absolute;top:10px;right:18px;font-size:1.5em;color:#888;cursor:pointer;">&times;</span>
        <div id="stockModalTitle" style="font-size:1.2em;font-weight:bold;margin-bottom:18px;color:#222;">Confirm Action</div>
        <div id="stockModalMsg" style="margin-bottom:18px;"></div>
        <button id="confirmStockBtn" style="padding:8px 24px;background:#27ae60;color:#fff;border:none;border-radius:6px;font-size:1em;margin-right:10px;">Confirm</button>
        <button id="cancelStockBtn" style="padding:8px 24px;background:#888;color:#fff;border:none;border-radius:6px;font-size:1em;">Cancel</button>
      </div>
    </div>
<script>
function showToast(msg, success) {
    var toast = document.getElementById('toast');
    toast.textContent = msg;
    toast.style.background = success ? '#27ae60' : '#e74c3c';
    toast.classList.add('show');
    setTimeout(() => { toast.classList.remove('show'); }, 2000);
}
let stockAction = null;
let stockActionArgs = null;
function showStockModal(action, amount, product, callback) {
    document.getElementById('stockModalTitle').textContent = (action === 'add' ? 'Add Stock' : 'Remove Stock');
    document.getElementById('stockModalMsg').innerHTML = `Are you sure you want to <b>${action === 'add' ? 'add' : 'remove'}</b> <b>${amount}</b> stock for <b>${product}</b>?`;
    document.getElementById('stockConfirmModal').style.display = 'flex';
    stockAction = callback;
}
document.getElementById('closeStockModal').onclick = function() {
    document.getElementById('stockConfirmModal').style.display = 'none';
    stockAction = null;
};
document.getElementById('cancelStockBtn').onclick = function() {
    document.getElementById('stockConfirmModal').style.display = 'none';
    stockAction = null;
};
document.getElementById('confirmStockBtn').onclick = function() {
    document.getElementById('stockConfirmModal').style.display = 'none';
    if (stockAction) stockAction();
    stockAction = null;
};
document.querySelectorAll('.stock-input').forEach(function(input) {
    input.addEventListener('change', saveStock);
    input.addEventListener('blur', saveStock);
    input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            saveStock.call(this);
            this.blur();
        }
    });
    function saveStock() {
        var row = input.closest('tr');
        var id = row.getAttribute('data-id');
        var stock = input.value;
        input.disabled = true;
        fetch('Stocks.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({id: id, stock: stock})
        })
        .then(res => res.json())
        .then(data => {
            input.disabled = false;
            showToast(data.message, data.success);
            if (data.success) {
                var stockCell = row.querySelector('.current-stock');
                var s = parseInt(stock);
                if (s == 0) {
                    stockCell.innerHTML = '<span class="out-of-stock">0</span>';
                } else if (s <= 5) {
                    stockCell.innerHTML = '<span class="low-stock">' + s + '</span>';
                } else {
                    stockCell.innerHTML = '<span class="in-stock">' + s + '</span>';
                }
                row.classList.add('updated-row');
                setTimeout(() => { row.classList.remove('updated-row'); }, 800);
            }
        })
        .catch(() => {
            input.disabled = false;
            showToast('Network error.', false);
        });
    }
});
document.querySelectorAll('.add-btn').forEach(function(btn) {
    btn.onclick = function() {
        var row = btn.closest('tr');
        var id = row.getAttribute('data-id');
        var add = row.querySelector('.add-stock-input').value;
        var product = row.querySelector('.product-name-cell').textContent.trim();
        btn.disabled = true;
        showStockModal('add', add, product, function() {
            fetch('Stocks.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({id: id, add: add})
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                showToast(data.message, data.success);
                if (data.success) {
                    var stockCell = row.querySelector('.current-stock');
                    var current = parseInt(stockCell.textContent) || 0;
                    var newStock = current + parseInt(add);
                    if (newStock == 0) {
                        stockCell.innerHTML = '<span class="out-of-stock">0</span>';
                    } else if (newStock <= 5) {
                        stockCell.innerHTML = '<span class="low-stock">' + newStock + '</span>';
                    } else {
                        stockCell.innerHTML = '<span class="in-stock">' + newStock + '</span>';
                    }
                    row.classList.add('updated-row');
                    setTimeout(() => { row.classList.remove('updated-row'); }, 800);
                }
            })
            .catch(() => {
                btn.disabled = false;
                showToast('Network error.', false);
            });
        });
        btn.disabled = false;
    };
});
document.querySelectorAll('.remove-btn').forEach(function(btn) {
    btn.onclick = function() {
        var row = btn.closest('tr');
        var id = row.getAttribute('data-id');
        var remove = parseInt(row.querySelector('.remove-stock-input').value);
        var stockCell = row.querySelector('.current-stock');
        var current = parseInt(stockCell.textContent) || 0;
        var product = row.querySelector('.product-name-cell').textContent.trim();
        if (remove > current) {
            showToast('Cannot remove more than current stock.', false);
            return;
        }
        btn.disabled = true;
        showStockModal('remove', remove, product, function() {
            fetch('Stocks.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({id: id, add: -remove})
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                showToast(data.message, data.success);
                if (data.success) {
                    var newStock = current - remove;
                    if (newStock == 0) {
                        stockCell.innerHTML = '<span class="out-of-stock">0</span>';
                    } else if (newStock <= 5) {
                        stockCell.innerHTML = '<span class="low-stock">' + newStock + '</span>';
                    } else {
                        stockCell.innerHTML = '<span class="in-stock">' + newStock + '</span>';
                    }
                    row.classList.add('updated-row');
                    setTimeout(() => { row.classList.remove('updated-row'); }, 800);
                }
            })
            .catch(() => {
                btn.disabled = false;
                showToast('Network error.', false);
            });
        });
        btn.disabled = false;
    };
});

// Function to redirect to AddProduct.php
function redirectToAddProduct() {
    window.location.href = "./AddProduct.php";
}
</script>
</body>
</html>