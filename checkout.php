<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_email = $_SESSION['user_email'] ?? 'user@email.com';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Checkout - Void Food</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
/* ... (Styles remain the same as your provided code) ... */
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'Segoe UI', sans-serif; background: #000; color: #fff; }

nav { position: fixed; top: 0; width: 100%; padding: 20px 50px; display: flex; justify-content: space-between; align-items: center; z-index: 1000; background: rgba(0,0,0,0.95); }
.logo { font-size: 24px; font-weight: bold; color: #FFD93D; }
.nav-links { display: flex; gap: 20px; list-style: none; }
.nav-links a { color: #fff; text-decoration: none; transition: 0.3s; }
.nav-links a:hover { color: #FFD93D; }

.container { max-width: 1000px; margin: 120px auto; padding: 20px; }
.header { text-align: center; margin-bottom: 40px; }
.header h1 { font-size: 42px; color: #FFD93D; margin-bottom: 10px; }

.checkout-wrapper { display: grid; grid-template-columns: 2fr 1fr; gap: 40px; }

.payment-form { background: #111; padding: 40px; border-radius: 10px; }
.form-section { margin-bottom: 40px; }
.form-section h3 { color: #FFD93D; margin-bottom: 20px; font-size: 18px; }
.form-group { margin-bottom: 20px; }
.form-group label { display: block; color: #ccc; margin-bottom: 8px; font-weight: 500; }
.form-group input, .form-group select { width: 100%; padding: 12px; border: none; border-radius: 6px; background: #1a1a1a; color: #fff; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }

.payment-methods { display: flex; gap: 10px; margin-bottom: 20px; }
.method-btn { flex: 1; padding: 12px; border: 2px solid #333; background: transparent; color: #fff; border-radius: 6px; cursor: pointer; transition: all 0.3s; font-weight: 600; }
.method-btn.active { border-color: #FFD93D; background: #FFD93D; color: #000; }

.networks-grid, .cards-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-top: 10px; }
.network-btn, .card-btn { padding: 10px; border: 2px solid #333; background: transparent; color: #fff; border-radius: 6px; cursor: pointer; transition: all 0.3s; font-weight: 500; font-size: 12px; }
.network-btn.active, .card-btn.active { border-color: #FFD93D; background: #FFD93D; color: #000; }

.network-info { margin-top: 8px; padding: 8px; background: #1a1a1a; border-left: 3px solid #FFD93D; border-radius: 4px; font-size: 12px; color: #ccc; }

.hidden { display: none; }

.order-summary { background: #1a1a1a; padding: 20px; border-radius: 8px; height: fit-content; }
.summary-item { display: flex; justify-content: space-between; margin-bottom: 10px; color: #ccc; }
.summary-total { display: flex; justify-content: space-between; margin-top: 15px; padding-top: 15px; border-top: 1px solid #333; font-size: 18px; font-weight: bold; color: #FFD93D; }

.pay-btn { width: 100%; background: linear-gradient(45deg, #FF6B6B, #FFD93D); color: #fff; padding: 15px; border: none; border-radius: 25px; font-weight: bold; font-size: 16px; cursor: pointer; transition: 0.3s; margin-top: 20px; }
.pay-btn:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(255,107,107,0.3); }
.pay-btn:disabled { opacity: 0.5; cursor: not-allowed; }

.result { margin-top: 25px; padding: 15px; border-radius: 6px; display: none; animation: slideIn 0.3s ease; }
.result.success { background: #d4edda; border: 2px solid #28a745; color: #155724; display: block; }
.result.error { background: #f8d7da; border: 2px solid #dc3545; color: #721c24; display: block; }
.result.info { background: #d1ecf1; border: 2px solid #17a2b8; color: #0c5460; display: block; }

.payment-link { display: inline-block; margin-top: 10px; padding: 10px 16px; background: #28a745; color: white; border-radius: 6px; text-decoration: none; font-weight: 600; }
.payment-link:hover { background: #218838; }

@keyframes slideIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }

@media (max-width: 768px) { .checkout-wrapper { grid-template-columns: 1fr; } .form-row { grid-template-columns: 1fr; } }
</style>
</head>
<body>
<nav>
    <div class="logo">VOID FOOD</div>
    <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="menu.php">Menu</a></li>
        <li><a href="cart.php">Cart</a></li>
    </ul>
</nav>

<div class="container">
    <div class="header">
        <h1>Checkout 🛍️</h1>
        <p style="color: #ccc;">Complete your order</p>
    </div>

    <div class="checkout-wrapper">
        <form id="checkoutForm" class="payment-form">
            <div class="form-section">
                <h3>Delivery Address</h3>
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" id="fullName" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" id="email" value="<?php echo htmlspecialchars($user_email); ?>" required>
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="tel" id="phone" required>
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <input type="text" id="address" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>City</label>
                        <input type="text" id="city" required>
                    </div>
                    <div class="form-group">
                        <label>State</label>
                        <input type="text" id="state" required>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3>Payment Method</h3>
                <div class="payment-methods">
                    <button type="button" class="method-btn active" data-method="mobile_money">📱 Mobile Money</button>
                </div>
                <p style="color: #ccc; margin-top: 10px;">Card payment not available</p>

                <div id="mobilemoneySection">
                    <label>Select Network</label>
                    <div class="networks-grid">
                        <button type="button" class="network-btn active" data-network="MTN" data-country="Ghana" data-currency="GHS">🇬🇭 MTN<br><small>Ghana</small></button>
                        <button type="button" class="network-btn" data-network="VODAFONE" data-country="Ghana" data-currency="GHS">🇬🇭 Vodafone<br><small>Ghana</small></button>
                        <button type="button" class="network-btn" data-network="AIRTEL" data-country="Tanzania" data-currency="TZS">🇹🇿 Airtel<br><small>Tanzania</small></button>
                        <button type="button" class="network-btn" data-network="TIGO" data-country="Tanzania" data-currency="TZS">🇹🇿 Tigo<br><small>Tanzania</small></button>
                        <button type="button" class="network-btn" data-network="MPESA" data-country="Kenya" data-currency="KES">🇰🇪 M-Pesa<br><small>Kenya</small></button>
                        <button type="button" class="network-btn" data-network="HALOTEL" data-country="Tanzania" data-currency="TZS">🇹🇿 Halotel<br><small>Tanzania</small></button>
                    </div>
                    <div class="network-info">
                        <strong>Selected:</strong> <span id="networkDisplay">MTN (Ghana - GHS)</span>
                    </div>
                    <input type="hidden" id="selectedNetwork" value="MTN">
                </div>
            </div>

            <button type="button" class="pay-btn" onclick="processPayment()">Create Payment Link & Pay</button>
        </form>

        <div class="order-summary" id="orderSummary"></div>
    </div>

    <div class="result" id="result"></div>
</div>
<script>
// --- UNIQUE USER KEY LOGIC ---
// Uses the session user_id to ensure we only clear the correct user's cart
const currentUserId = <?php echo json_encode($_SESSION['user_id'] ?? 'guest'); ?>;
const storageKey = 'cart_' + currentUserId;

let cart = [];
let totalAmount = 0;
const deliveryFee = 500;
let selectedMethod = 'mobile_money';
let selectedNetwork = 'MTN';

/**
 * Renders the order summary based on the user's specific storage key
 */
function renderOrderSummary() {
    cart = JSON.parse(localStorage.getItem(storageKey)) || [];
    console.log('📋 Rendering order summary for key:', storageKey, cart);
    
    let html = '';
    if (cart.length === 0) {
        html = '<p style="color: #ccc;">Your cart is empty</p>';
        totalAmount = 0;
    } else {
        totalAmount = 0;
        cart.forEach((item, i) => {
            const itemPrice = parseFloat(item.price) || 0;
            const itemQty = parseInt(item.qty) || 1;
            const subtotal = itemPrice * itemQty;
            totalAmount += subtotal;
            
            html += `
                <div class="summary-item">
                    <span>${itemQty}x ${item.name}</span>
                    <span>TZS ${subtotal.toLocaleString()}</span>
                </div>`;
        });
        
        totalAmount += deliveryFee;
        html += `<div class="summary-item"><span>Delivery Fee</span><span>TZS ${deliveryFee}</span></div>`;
        html += `<div class="summary-total"><span>Total Amount</span><span>TZS ${totalAmount.toLocaleString()}</span></div>`;
    }
    
    document.getElementById('orderSummary').innerHTML = html;
}

// UI Event Listeners for Mobile Money Networks
document.querySelectorAll('.network-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
        e.preventDefault();
        document.querySelectorAll('.network-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        selectedNetwork = btn.dataset.network;
        document.getElementById('selectedNetwork').value = selectedNetwork;
        document.getElementById('networkDisplay').textContent = `${selectedNetwork} (${btn.dataset.country} - ${btn.dataset.currency})`;
    });
});

/**
 * Main payment process: Creates link and saves order record
 */
async function processPayment() {
    const fullName = document.getElementById('fullName').value.trim();
    const email = document.getElementById('email').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const address = document.getElementById('address').value.trim();
    const city = document.getElementById('city').value.trim();
    const state = document.getElementById('state').value.trim();
    const resultDiv = document.getElementById('result');

    // Validation
    if (!fullName || !email || !phone || !address || !city || !state) {
        resultDiv.className = 'error';
        resultDiv.innerHTML = '❌ Please fill all required fields';
        return;
    }

    if (cart.length === 0) {
        resultDiv.className = 'error';
        resultDiv.innerHTML = '❌ Your cart is empty';
        return;
    }

    const payBtn = document.querySelector('.pay-btn');
    payBtn.disabled = true;
    payBtn.textContent = 'Preparing payment...';
    resultDiv.className = 'info';
    resultDiv.innerHTML = '⏳ Creating your secure payment link...';

    try {
        const payload = {
            amount: totalAmount,
            email: email,
            name: fullName,
            paymentMethod: selectedMethod,
            network: selectedNetwork
        };

        const response = await fetch('Payment/process-payment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const data = await response.json();

        if (response.ok && data.link) {
            // STEP 1: Save order to DB immediately (Pending status)
            // We 'await' this to ensure the record is created before moving on
            await saveOrder(data.reference, fullName, email, phone, address, city, state);

            // STEP 2: Show the success UI with the payment link
            // We REMOVED the auto-redirect timer so the user can click at their own pace
            resultDiv.className = 'success';
            resultDiv.innerHTML = `
                <div style="text-align: center; padding: 10px;">
                    <strong style="font-size: 18px;">✅ Order Prepared Successfully!</strong><br><br>
                    <p>Your order is recorded as <strong>Pending</strong>. Click the button below to complete payment via Flutterwave.</p><br>
                    <a href="${data.link}" target="_blank" class="payment-link" style="display: block; background: #FFD93D; color: #000; padding: 15px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 18px;">
                        🚀 CLICK HERE TO PAY NOW
                    </a>
                    <p style="margin-top: 20px; font-size: 13px; color: #555;">
                        After paying, you can view your status in the <a href="orders.php" style="color: #FF6B6B;">My Orders</a> page.
                    </p>
                </div>
            `;
            payBtn.textContent = 'Order Ready ✅';
        } else {
            resultDiv.className = 'error';
            resultDiv.innerHTML = `❌ Error: ${data.message || 'Failed to create payment link'}`;
            payBtn.disabled = false;
            payBtn.textContent = 'Try Again';
        }
    } catch (error) {
        resultDiv.className = 'error';
        resultDiv.innerHTML = `❌ Connection Error: ${error.message}`;
        payBtn.disabled = false;
        payBtn.textContent = 'Create Payment Link & Pay';
    }
}

/**
 * Saves the transaction record to the MySQL database via api.php
 */
async function saveOrder(txId, fullName, email, phone, address, city, state) {
    const f = new FormData();
    f.append("action", "create_order");
    f.append("amount", totalAmount);
    f.append("fullName", fullName);
    f.append("phone", phone);
    f.append("address", address);
    f.append("city", city);
    f.append("state", state);
    f.append("tx_id", txId);
    f.append("items", JSON.stringify(cart));

    try {
        const r = await fetch("api.php", {method: "POST", body: f});
        const d = await r.json();
        
        if (d.status) {
            console.log('✅ Order saved successfully to database');
            // Clear only this specific user's cart from local storage
            localStorage.removeItem(storageKey); 
            console.log('🧹 Cart cleared for key:', storageKey);
        } else {
            console.error('❌ Database save failed:', d.message);
        }
    } catch (error) {
        console.error('❌ Database connection error:', error);
    }
}

window.onload = () => {
    renderOrderSummary();
};
</script>
</script>
</body>
</html>