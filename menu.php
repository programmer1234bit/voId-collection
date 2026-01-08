<?php
require 'config.php';

// Fetch categories
$categories_result = $conn->query("SELECT name FROM categories ORDER BY name");
$categories = [];
if ($categories_result && $categories_result->num_rows > 0) {
    while ($row = $categories_result->fetch_assoc()) {
        $categories[] = $row['name'];
    }
}

$result = $conn->query("SELECT id, name, price, category, image_url, description FROM menu ORDER BY category, name");
$items = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
}

// Map categories to display names if needed, but keep original for filtering
foreach ($items as &$item) {
    // Set fallback image if no image_url
    if (empty($item['image_url'])) {
        $item['image_url'] = 'https://via.placeholder.com/200x150/333/fff?text=No+Image';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Menu - Void Food</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'Segoe UI', sans-serif; background: #000; color: #fff; }

nav { position: fixed; top: 0; width: 100%; padding: 20px 50px; display: flex; justify-content: space-between; align-items: center; z-index: 1000; background: rgba(0,0,0,0.95); }
.logo { font-size: 24px; font-weight: bold; color: #FFD93D; }
.nav-links { display: flex; gap: 20px; list-style: none; align-items: center; }
.nav-links a { color: #fff; text-decoration: none; transition: 0.3s; }
.nav-links a:hover { color: #FFD93D; }
.cart-icon { position: relative; cursor: pointer; font-size: 20px; }
.cart-count { position: absolute; top: -8px; right: -8px; background: #FF6B6B; width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; }

.container { max-width: 1400px; margin: 120px auto; padding: 20px; }
.header { text-align: center; margin-bottom: 50px; }
.header h1 { font-size: 48px; color: #FFD93D; margin-bottom: 10px; }

.filters { display: flex; gap: 15px; margin-bottom: 40px; justify-content: center; flex-wrap: wrap; }
.filter-btn { background: #111; border: 2px solid #333; color: #fff; padding: 10px 20px; border-radius: 25px; cursor: pointer; transition: 0.3s; }
.filter-btn.active { border-color: #FFD93D; color: #FFD93D; }

.menu-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 30px; }
.menu-card { background: #111; border-radius: 12px; overflow: hidden; transition: all 0.3s ease; border: 1px solid #333; }
.menu-card:hover { transform: translateY(-10px); border-color: #FFD93D; box-shadow: 0 10px 30px rgba(255,107,107,0.3); }
.card-image { height: 150px; overflow: hidden; background: linear-gradient(135deg, #1a1a1a, #2d1b00); display: flex; align-items: center; justify-content: center; }
.card-content { padding: 20px; }
.card-name { font-size: 20px; font-weight: bold; color: #FFD93D; margin-bottom: 5px; }
.card-desc { color: #999; font-size: 14px; margin-bottom: 15px; }
.card-price { font-size: 24px; font-weight: bold; color: #6BCF7F; margin-bottom: 15px; }
.card-actions { display: flex; gap: 10px; }
.qty-input { width: 60px; padding: 8px; border: none; border-radius: 5px; background: #1a1a1a; color: #fff; text-align: center; }
.add-btn { flex: 1; background: linear-gradient(45deg, #FF6B6B, #FFD93D); border: none; color: #fff; padding: 10px; border-radius: 5px; cursor: pointer; font-weight: bold; transition: 0.3s; }
.add-btn:hover { transform: scale(1.05); }

@media (max-width: 768px) { .container { margin-top: 140px; } .menu-grid { grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); } }</style></head>
<body>
<nav>
    <div class="logo">VOID FOOD</div>
    <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="menu.php" style="color: #FFD93D;">Menu</a></li>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="cart.php" class="cart-icon"><i class="fas fa-shopping-cart"></i><span class="cart-count" id="cartCount">0</span></a></li>
    </ul>
</nav>

<div class="container">
    <div class="header">
        <h1>Our Menu ✨</h1>
        <p style="color: #ccc;">Discover our exquisite selection of dishes</p>
    </div>

    <div class="filters">
        <button class="filter-btn active" onclick="filterMenu('All')">All</button>
        <?php foreach ($categories as $cat): ?>
            <button class="filter-btn" onclick="filterMenu('<?php echo htmlspecialchars($cat); ?>')"><?php echo htmlspecialchars($cat); ?></button>
        <?php endforeach; ?>
    </div>

    <div class="menu-grid" id="menuGrid"></div>
</div>

<script>
// --- UNIQUE USER LOGIC ---
const currentUserId = <?php echo json_encode($_SESSION['user_id'] ?? 'guest'); ?>;
const storageKey = 'cart_' + currentUserId;

const items = <?php echo json_encode($items); ?>;
let cart = [];
let currentFilter = 'All';

function renderMenu() {
    const filtered = currentFilter === 'All' ? items : items.filter(i => i.category === currentFilter);
    let html = '';
    filtered.forEach(item => {
        html += `<div class="menu-card">
            <div class="card-image"><img src="${item.image_url}" alt="${item.name}" style="width:100%; height:100%; object-fit:cover;"></div>
            <div class="card-content">
                <div class="card-name">${item.name}</div>
                <div class="card-desc">${item.description}</div>
                <div class="card-price">TZS ${item.price.toLocaleString()}</div>
                <div class="card-actions">
                    <input type="number" min="1" value="1" class="qty-input" id="qty${item.id}">
                    <button class="add-btn" onclick="addToCart(${parseInt(item.id)})">Add to Cart</button>
                </div>
            </div>
        </div>`;
    });
    document.getElementById('menuGrid').innerHTML = html;
}

function filterMenu(category) {
    currentFilter = category;
    document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
    // Fixed: Passing event target more reliably
    if (window.event) {
        window.event.target.classList.add('active');
    }
    renderMenu();
}

function addToCart(itemId) {
    console.log('🛒 Adding to user cart:', storageKey);
    
    const item = items.find(i => parseInt(i.id) === parseInt(itemId));
    
    if (!item || !item.name || !item.price) {
        alert('❌ Invalid item details');
        return;
    }
    
    const qtyInput = document.getElementById(`qty${itemId}`);
    const qty = parseInt(qtyInput.value) || 1;
    
    // Refresh cart from unique storage before adding
    cart = JSON.parse(localStorage.getItem(storageKey)) || [];
    
    const existing = cart.find(c => parseInt(c.id) === parseInt(item.id));
    
    if (existing) {
        existing.qty = (parseInt(existing.qty) || 0) + qty;
    } else {
        const cartItem = {
            id: parseInt(item.id),
            name: String(item.name),
            price: parseFloat(item.price),
            category: item.category || 'Unknown',
            description: item.description || '',
            qty: qty
        };
        cart.push(cartItem);
    }
    
    // Save to user-specific key
    localStorage.setItem(storageKey, JSON.stringify(cart));
    
    updateCartCount();
    alert(`✅ Added ${qty}x ${item.name} to your cart!`);
    qtyInput.value = 1;
}

function updateCartCount() {
    const currentCart = JSON.parse(localStorage.getItem(storageKey)) || [];
    // Calculate total quantity across all items
    const totalQty = currentCart.reduce((sum, item) => sum + (parseInt(item.qty) || 0), 0);
    
    const countBadge = document.getElementById('cartCount');
    if (countBadge) {
        countBadge.textContent = totalQty;
    }
}

window.onload = () => {
    console.log('🚀 Menu loaded for user:', currentUserId);
    renderMenu();
    updateCartCount();
    
    // Listen for storage changes in other tabs
    window.addEventListener('storage', (e) => {
        if (e.key === storageKey) {
            updateCartCount();
        }
    });
};
</script>
</body>
</html>