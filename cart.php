<?php
// Start the session to access user_id
require 'config.php';
$current_user_id = $_SESSION['user_id'] ?? 'guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cart - Void Food</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'Segoe UI', sans-serif; background: #000; color: #fff; }

nav { position: fixed; top: 0; width: 100%; padding: 20px 50px; display: flex; justify-content: space-between; align-items: center; z-index: 1000; background: rgba(0,0,0,0.95); }
.logo { font-size: 24px; font-weight: bold; color: #FFD93D; }
.nav-links { display: flex; gap: 20px; list-style: none; }
.nav-links a { color: #fff; text-decoration: none; transition: 0.3s; }
.nav-links a:hover { color: #FFD93D; }

.container { max-width: 1200px; margin: 120px auto; padding: 20px; }
.header { margin-bottom: 40px; }
.header h1 { font-size: 42px; color: #FFD93D; }

.cart-content { display: grid; grid-template-columns: 2fr 1fr; gap: 40px; }
.cart-items { background: #111; padding: 30px; border-radius: 10px; }
.item-row { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr auto; gap: 20px; align-items: center; padding: 20px; border-bottom: 1px solid #333; }
.item-row:last-child { border-bottom: none; }
.item-name { color: #FFD93D; font-weight: bold; }
.item-price { color: #6BCF7F; }
.qty-input { width: 60px; padding: 8px; border: none; border-radius: 5px; background: #1a1a1a; color: #fff; text-align: center; }
.remove-btn { background: #FF6B6B; border: none; color: #fff; padding: 8px 15px; border-radius: 5px; cursor: pointer; }

.order-summary { background: #111; padding: 30px; border-radius: 10px; height: fit-content; }
.summary-row { display: flex; justify-content: space-between; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #333; }
.summary-row:last-child { border-bottom: none; }
.summary-total { font-size: 24px; font-weight: bold; color: #FFD93D; margin-bottom: 20px; }
.checkout-btn { width: 100%; background: linear-gradient(45deg, #FF6B6B, #FFD93D); color: #fff; padding: 15px; border: none; border-radius: 25px; font-weight: bold; font-size: 16px; cursor: pointer; transition: 0.3s; }
.checkout-btn:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(255,107,107,0.3); }
.checkout-btn:disabled { opacity: 0.5; cursor: not-allowed; }

.empty-cart { text-align: center; padding: 60px 20px; }
.empty-cart p { color: #ccc; margin-bottom: 20px; }
.continue-btn { background: #FFD93D; color: #000; padding: 12px 30px; border: none; border-radius: 25px; font-weight: bold; cursor: pointer; text-decoration: none; display: inline-block; }

@media (max-width: 768px) { .cart-content { grid-template-columns: 1fr; } .item-row { grid-template-columns: 1fr; } }
</style>
</head>
<body>

<nav>
    <div class="logo">VOID FOOD</div>
    <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="menu.php">Menu</a></li>
        <li><a href="dashboard.php">Dashboard</a></li>
    </ul>
</nav>

<div class="container">
    <div class="header">
        <h1>Your Cart 🛒</h1>
    </div>

    <div id="cartContent"></div>
</div>

<script>
// --- LOGIC FIX: USE THE SAME UNIQUE USER KEY ---
const currentUserId = <?php echo json_encode($current_user_id); ?>;
const storageKey = 'cart_' + currentUserId;

let cart = [];

function renderCart() {
    // Look for the specific user's cart (e.g., cart_1) instead of just 'cart'
    cart = JSON.parse(localStorage.getItem(storageKey)) || [];
    
    console.log('=== CART.PHP DEBUG ===');
    console.log('🔑 Using Storage Key:', storageKey);
    console.log('📦 Raw data:', localStorage.getItem(storageKey));
    
    // Filter out invalid items
    const validCart = cart.filter(item => {
        const isValid = item.id && item.name && item.price && item.qty;
        return isValid;
    });
    
    // Update storage if we cleaned anything
    if (validCart.length !== cart.length) {
        localStorage.setItem(storageKey, JSON.stringify(validCart));
        cart = validCart;
    }
    
    if (cart.length === 0) {
        document.getElementById('cartContent').innerHTML = `
            <div class="empty-cart">
                <i class="fas fa-shopping-cart" style="font-size: 60px; color: #FFD93D; margin-bottom: 20px;"></i>
                <h2 style="color: #ccc; margin-bottom: 10px;">Your cart is empty</h2>
                <p>No items in your cart yet.</p>
                <a href="menu.php" class="continue-btn">Browse Menu</a>
            </div>
        `;
        return;
    }

    let itemsHtml = '';
    let total = 0;
    let totalQty = 0;

    cart.forEach((item, idx) => {
        const price = parseFloat(item.price) || 0;
        const qty = parseInt(item.qty) || 1;
        const subtotal = price * qty;
        total += subtotal;
        totalQty += qty;
        
        itemsHtml += `<div class="item-row">
            <div><span class="item-name">${item.name}</span></div>
            <div class="item-price">TZS ${price.toLocaleString()}</div>
            <input type="number" min="1" value="${qty}" class="qty-input" onchange="updateQty(${idx}, this.value)">
            <div class="item-price">TZS ${subtotal.toLocaleString()}</div>
            <button class="remove-btn" onclick="removeItem(${idx})"><i class="fas fa-trash"></i></button>
        </div>`;
    });

    const html = `<div class="cart-content">
        <div class="cart-items"><h3 style="color: #FFD93D; margin-bottom: 20px;">Cart Items (${cart.length} types, ${totalQty} total)</h3>${itemsHtml}</div>
        <div class="order-summary">
            <h3 style="color: #FFD93D; margin-bottom: 20px;">Order Summary</h3>
            <div class="summary-row"><span>Subtotal:</span><span>TZS ${total.toLocaleString()}</span></div>
            <div class="summary-row"><span>Delivery:</span><span>TZS 500</span></div>
            <div class="summary-total">Total: TZS ${(total + 500).toLocaleString()}</div>
            <button class="checkout-btn" onclick="checkout()">Proceed to Checkout</button>
            <a href="menu.php" style="display: block; text-align: center; margin-top: 15px; color: #FFD93D; text-decoration: none;">Continue Shopping</a>
        </div>
    </div>`;

    document.getElementById('cartContent').innerHTML = html;
}

function updateQty(idx, qty) {
    const newQty = Math.max(1, parseInt(qty));
    cart[idx].qty = newQty;
    localStorage.setItem(storageKey, JSON.stringify(cart));
    renderCart();
}

function removeItem(idx) {
    cart.splice(idx, 1);
    localStorage.setItem(storageKey, JSON.stringify(cart));
    renderCart();
}

function checkout() {
    if (cart.length === 0) {
        alert('❌ Your cart is empty!');
        return;
    }
    window.location.href = 'checkout.php';
}

window.onload = () => {
    renderCart();
    window.addEventListener('storage', (e) => {
        if (e.key === storageKey) renderCart();
    });
};
</script>

</body>
</html>