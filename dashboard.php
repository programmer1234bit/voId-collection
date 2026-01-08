<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

$user_id = $_SESSION['user_id'];
$user = $conn->query("SELECT u.id, u.email, u.created_at, up.username, up.avatar FROM users u LEFT JOIN user_profiles up ON u.id = up.user_id WHERE u.id = $user_id")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard - Void Food</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'Segoe UI', sans-serif; background: #000; color: #fff; }

nav { position: fixed; top: 0; width: 100%; padding: 20px 50px; display: flex; justify-content: space-between; align-items: center; z-index: 1000; background: rgba(0,0,0,0.9); }
.logo { font-size: 24px; font-weight: bold; color: #FFD93D; }
.nav-links { display: flex; gap: 20px; list-style: none; }
.nav-links a { color: #fff; text-decoration: none; transition: 0.3s; }
.nav-links a:hover { color: #FFD93D; }
.logout-btn { background: #FF6B6B; padding: 10px 20px; border-radius: 25px; color: #fff; border: none; cursor: pointer; }

.container { max-width: 1200px; margin: 100px auto; padding: 20px; }
.header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; }
.user-info { display: flex; gap: 20px; align-items: center; }
.avatar { width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(45deg, #FF6B6B, #FFD93D); display: flex; align-items: center; justify-content: center; font-size: 40px; }
.user-details h2 { color: #FFD93D; margin-bottom: 5px; }
.user-details p { color: #ccc; font-size: 14px; }

.tabs { display: flex; gap: 20px; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 15px; flex-wrap: wrap; }
.tab-btn { background: none; border: none; color: #ccc; font-size: 16px; cursor: pointer; padding: 10px 20px; transition: 0.3s; }
.tab-btn.active { color: #FFD93D; border-bottom: 3px solid #FFD93D; }

.tab-content { display: none; }
.tab-content.active { display: block; }

.order-card { background: #111; padding: 20px; border-radius: 10px; margin-bottom: 20px; border-left: 4px solid #FFD93D; }
.order-card h4 { color: #FFD93D; margin-bottom: 10px; }
.order-details { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; font-size: 14px; }
.order-details p { color: #ccc; }

.payment-table { width: 100%; border-collapse: collapse; background: #111; border-radius: 10px; overflow: hidden; }
.payment-table th { background: #1a1a1a; padding: 15px; text-align: left; color: #FFD93D; font-weight: 600; border-bottom: 2px solid #333; }
.payment-table td { padding: 12px 15px; border-bottom: 1px solid #333; }
.payment-table tr:hover { background: #1a1a1a; }

.status-badge { display: inline-block; padding: 6px 12px; border-radius: 20px; font-weight: 600; font-size: 12px; }
.status-successful { background: #6BCF7F; color: #000; }
.status-pending { background: #FFD93D; color: #000; }
.status-failed { background: #FF6B6B; color: #fff; }

.ref-code { font-family: 'Courier New'; font-size: 11px; background: #1a1a1a; padding: 4px 8px; border-radius: 4px; }

.empty-state { text-align: center; padding: 60px 20px; }
.empty-state i { font-size: 80px; color: #FFD93D; opacity: 0.3; margin-bottom: 20px; }
.empty-state p { color: #ccc; }

.profile-form { background: #111; padding: 30px; border-radius: 10px; max-width: 500px; }
.form-group { margin-bottom: 20px; }
.form-group label { display: block; color: #FFD93D; margin-bottom: 5px; }
.form-group input { width: 100%; padding: 10px; border: none; border-radius: 5px; background: #1a1a1a; color: #fff; }
.save-btn { background: linear-gradient(45deg, #FF6B6B, #FFD93D); color: #fff; padding: 12px 30px; border: none; border-radius: 25px; cursor: pointer; font-weight: bold; }

.quick-links { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 20px; }
.quick-link { background: #111; padding: 20px; border-radius: 8px; text-align: center; transition: 0.3s; cursor: pointer; }
.quick-link:hover { background: #1a1a1a; transform: translateY(-5px); }
.quick-link i { font-size: 32px; color: #FFD93D; margin-bottom: 10px; }
.quick-link p { color: #ccc; }

@media (max-width: 768px) { .container { margin-top: 120px; } .order-details { grid-template-columns: 1fr; } }
</style>
</head>
<body>

<nav>
    <div class="logo">VOID FOOD</div>
    <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="menu.php">Menu</a></li>
        <li><a href="orders.php">Orders</a></li>
        <li><button class="logout-btn" onclick="logout()">Logout</button></li>
    </ul>
</nav>

<div class="container">
    <div class="header">
        <div class="user-info">
            <div class="avatar"><i class="fas fa-user"></i></div>
            <div class="user-details">
                <h2><?php echo htmlspecialchars($user['username'] ?? $user['email']); ?></h2>
                <p><?php echo htmlspecialchars($user['email']); ?></p>
            </div>
        </div>
    </div>

    <div class="tabs">
        <button class="tab-btn active" onclick="switchTab('profile')">👤 Profile</button>
        <button class="tab-btn" onclick="switchTab('orders')">📦 Recent Orders</button>
        <button class="tab-btn" onclick="switchTab('payments')">💳 Payment History</button>
    </div>

    <!-- Profile Tab -->
    <div id="profile" class="tab-content active">
        <div class="profile-form">
            <h3 style="color: #FFD93D; margin-bottom: 20px;">Edit Profile</h3>
            <div class="form-group">
                <label>Username</label>
                <input type="text" id="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
            </div>
            <div class="form-group">
                <label>Member Since</label>
                <input type="text" value="<?php echo date('M d, Y', strtotime($user['created_at'])); ?>" disabled>
            </div>
            <button class="save-btn" onclick="updateProfile()">Save Changes</button>
        </div>

        <div class="quick-links">
            <div class="quick-link" onclick="window.location.href='menu.php'">
                <i class="fas fa-utensils"></i>
                <p>Browse Menu</p>
            </div>
            <div class="quick-link" onclick="window.location.href='cart.php'">
                <i class="fas fa-shopping-cart"></i>
                <p>View Cart</p>
            </div>
            <div class="quick-link" onclick="window.location.href='orders.php'">
                <i class="fas fa-box"></i>
                <p>My Orders</p>
            </div>
            <div class="quick-link" onclick="window.location.href='payment-history.php'">
                <i class="fas fa-history"></i>
                <p>Payment History</p>
            </div>
        </div>
    </div>

    <!-- Orders Tab -->
    <div id="orders" class="tab-content">
        <div id="ordersList"></div>
    </div>

    <!-- Payments Tab -->
    <div id="payments" class="tab-content">
        <h3 style="color: #FFD93D; margin-bottom: 20px;">💳 Payment Transactions</h3>
        <table class="payment-table">
            <thead>
                <tr>
                    <th>Reference ID</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Method</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody id="paymentsList"></tbody>
        </table>
    </div>
</div>

<script>
function switchTab(tab) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
    document.getElementById(tab).classList.add('active');
    event.target.classList.add('active');
    
    if (tab === 'orders') loadOrders();
    if (tab === 'payments') loadPayments();
}

async function updateProfile() {
    const f = new FormData();
    f.append("action", "update_profile");
    f.append("username", document.getElementById('username').value);

    const r = await fetch("api.php", {method: "POST", body: f});
    const d = await r.json();
    alert(d.message);
    if(d.status) location.reload();
}

async function loadOrders() {
    const r = await fetch("api.php?action=get_orders");
    const d = await r.json();
    let html = '';
    
    if (d.orders && d.orders.length > 0) {
        d.orders.forEach(o => {
            html += `<div class="order-card">
                <h4>Order #${o.id}</h4>
                <div class="order-details">
                    <p><strong>Amount:</strong> TZS ${Number(o.amount).toLocaleString()}</p>
                    <p><strong>Status:</strong> <span style="color: ${o.status === 'completed' ? '#6BCF7F' : '#FFD93D'}">${o.status.toUpperCase()}</span></p>
                    <p><strong>Date:</strong> ${new Date(o.created_at).toLocaleDateString()}</p>
                </div>
            </div>`;
        });
    } else {
        html = '<div class="empty-state"><i class="fas fa-box"></i><p>No orders yet. <a href="menu.php" style="color: #FFD93D;">Start shopping!</a></p></div>';
    }
    
    document.getElementById('ordersList').innerHTML = html;
}

async function loadPayments() {
    const r = await fetch("api.php?action=get_payments");
    const d = await r.json();
    let html = '';
    
    if (d.payments && d.payments.length > 0) {
        d.payments.forEach(p => {
            const statusClass = `status-${p.status}`;
            html += `<tr>
                <td><span class="ref-code">${p.reference.substring(0, 15)}...</span></td>
                <td>${Number(p.amount).toLocaleString()} ${p.currency}</td>
                <td><span class="status-badge ${statusClass}">${p.status.toUpperCase()}</span></td>
                <td>${p.payment_type === 'mobile_money' ? '📱 Mobile Money' : '💳 Card'}</td>
                <td>${new Date(p.created_at).toLocaleDateString()}</td>
            </tr>`;
        });
    } else {
        html = '<tr><td colspan="5" style="text-align: center; padding: 40px; color: #ccc;">No payments yet</td></tr>';
    }
    
    document.getElementById('paymentsList').innerHTML = html;
}

function logout() {
    if(confirm('Are you sure you want to logout?')) window.location.href = 'logout.php';
}
</script>

</body>
</html>
