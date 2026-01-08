<?php
session_start(); // THIS IS ESSENTIAL TO DETECT THE USER
// ... rest of your configuration code ...
// --- CONFIGURATION & DATA ---
$site_name = "VOID FOOD COLLECTION";
$current_year = date("Y");

// Navigation Links
$nav_links = [
    "Home" => "#home",
    "Menu" => "menu.php",
    "About" => "#about",
    "Contact" => "#contact"
];

// Feature Cards Data
$features = [
    [
        "icon" => "utensils",
        "title" => "Exquisite Cuisine",
        "desc" => "Carefully crafted dishes using the finest ingredients, prepared by master chefs with passion and precision."
    ],
    [
        "icon" => "cocktail",
        "title" => "Premium Beverages",
        "desc" => "A curated selection of refreshing drinks, artisanal cocktails, and specialty beverages to complement your meal."
    ],
    [
        "icon" => "star",
        "title" => "Five-Star Service",
        "desc" => "Experience exceptional hospitality with our dedicated team committed to making your dining experience unforgettable."
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $site_name; ?> - Premium Dining Experience</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #000; color: #fff; overflow-x: hidden; }

        /* Navbar */
        nav { position: fixed; top: 0; width: 100%; padding: 20px 50px; display: flex; justify-content: space-between; align-items: center; z-index: 1000; background: rgba(0, 0, 0, 0.7); backdrop-filter: blur(10px); transition: all 0.3s ease; }
        nav.scrolled { padding: 15px 50px; background: rgba(0, 0, 0, 0.95); }
        .logo { font-size: 28px; font-weight: bold; background: linear-gradient(45deg, #FF6B6B, #FFD93D, #6BCF7F); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; letter-spacing: 2px; cursor: pointer; }
        .logo:hover { opacity: 0.8; }
        .nav-links { display: flex; gap: 30px; list-style: none; align-items: center; }
        .nav-links a { color: #fff; text-decoration: none; font-weight: 500; transition: all 0.3s ease; position: relative; padding-bottom: 5px; }
        .nav-links a:hover { color: #FFD93D; }
        .nav-links a.active { color: #FFD93D; border-bottom: 2px solid #FFD93D; }

        .login-btn { background: rgba(255, 255, 255, 0.05); padding: 10px 22px; border-radius: 50px; border: 1px solid rgba(255, 217, 61, 0.4); color: #FFD93D !important; display: flex; align-items: center; gap: 8px; font-weight: 600 !important; transition: all 0.3s ease !important; cursor: pointer; }
        .login-btn:hover { background: #FFD93D !important; color: #000 !important; box-shadow: 0 0 20px rgba(255, 217, 61, 0.4); transform: translateY(-2px); }

        .cart-icon { position: relative; font-size: 24px; color: #fff; cursor: pointer; transition: 0.3s; }
        .cart-icon:hover { color: #FFD93D; }
        .cart-count { position: absolute; top: -8px; right: -12px; background: #FF6B6B; color: #fff; font-size: 12px; width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; }

        /* Hero */
        .hero { min-height: 100vh; display: flex; align-items: center; justify-content: center; position: relative; background: linear-gradient(135deg, #1a1a1a 0%, #2d1b00 50%, #1a1a1a 100%); overflow: hidden; margin-top: 60px; }
        .hero-background { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0.15; }
        .floating-icon { position: absolute; font-size: 60px; opacity: 0.3; animation: floatIcon 20s infinite ease-in-out; }
        @keyframes floatIcon { 0%, 100% { transform: translate(0, 0) rotate(0deg); } 25% { transform: translate(30px, -30px) rotate(10deg); } 50% { transform: translate(-20px, -50px) rotate(-10deg); } 75% { transform: translate(40px, -20px) rotate(5deg); } }
        
        .hero-content { text-align: center; z-index: 10; padding: 20px; max-width: 1200px; }
        .hero-title { font-size: 80px; font-weight: bold; margin-bottom: 20px; line-height: 1.2; }
        .hero-title span { background: linear-gradient(45deg, #FF6B6B, #FFD93D, #6BCF7F); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }

        .btn { padding: 18px 40px; font-size: 18px; font-weight: bold; border-radius: 50px; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: inline-block; }
        .btn-primary { background: linear-gradient(45deg, #FF6B6B, #FFD93D); color: #fff; box-shadow: 0 10px 30px rgba(255, 107, 107, 0.4); }
        .btn-primary:hover { transform: translateY(-3px); box-shadow: 0 15px 40px rgba(255, 107, 107, 0.6); }
        .btn-secondary { background: transparent; color: #fff; border: 2px solid #fff; }
        .btn-secondary:hover { background: #fff; color: #000; }

        /* Features */
        .features { padding: 120px 50px; background: #000; display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 40px; max-width: 1400px; margin: 0 auto; }
        .feature-card { background: linear-gradient(135deg, #1a1a1a, #2d1b00); padding: 40px; border-radius: 20px; text-align: center; transition: all 0.4s ease; border: 1px solid rgba(255, 215, 61, 0.2); }
        .feature-card:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(255, 107, 107, 0.3); border-color: #FFD93D; }
        .feature-icon { font-size: 60px; margin-bottom: 20px; }

        /* About Section */
        .about { padding: 120px 50px; background: #111; }
        .about-content { max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center; }
        .about-text h2 { font-size: 42px; color: #FFD93D; margin-bottom: 20px; }
        .about-text p { color: #ccc; line-height: 1.8; margin-bottom: 20px; font-size: 16px; }
        .about-text ul { list-style: none; }
        .about-text ul li { color: #ccc; margin-bottom: 10px; padding-left: 25px; position: relative; }
        .about-text ul li:before { content: "✓"; position: absolute; left: 0; color: #FFD93D; font-weight: bold; }

        /* Contact Section */
        .contact { padding: 120px 50px; background: #000; }
        .contact-content { max-width: 1000px; margin: 0 auto; }
        .contact-content h2 { text-align: center; font-size: 42px; color: #FFD93D; margin-bottom: 60px; }
        .contact-form { background: #111; padding: 40px; border-radius: 12px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; color: #FFD93D; margin-bottom: 8px; font-weight: 500; }
        .form-group input, .form-group textarea { width: 100%; padding: 12px; background: #1a1a1a; border: 1px solid #333; border-radius: 6px; color: #fff; font-family: inherit; transition: 0.3s; }
        .form-group input:focus, .form-group textarea:focus { outline: none; border-color: #FFD93D; box-shadow: 0 0 10px rgba(255,217,61,0.3); }
        .submit-btn { width: 100%; background: linear-gradient(45deg, #FF6B6B, #FFD93D); color: #fff; padding: 15px; border: none; border-radius: 25px; font-weight: bold; font-size: 16px; cursor: pointer; transition: 0.3s; }
        .submit-btn:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(255,107,107,0.4); }

        /* Footer */
        footer { background: #111; padding: 80px 50px 30px; color: #ccc; }
        .footer-content { max-width: 1400px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 40px; }
        .footer-section h3 { color: #FFD93D; margin-bottom: 20px; font-size: 20px; }
        .footer-section ul { list-style: none; }
        .footer-section ul li { margin-bottom: 10px; }
        .footer-section ul li a { color: #ccc; text-decoration: none; transition: 0.3s; }
        .footer-section ul li a:hover { color: #FFD93D; }
        .social-links { display: flex; gap: 15px; margin-top: 20px; }
        .social-links a { font-size: 24px; color: #ccc; transition: 0.3s; }
        .social-links a:hover { color: #FFD93D; }
        .footer-bottom { text-align: center; padding-top: 50px; border-top: 1px solid #333; margin-top: 50px; font-size: 14px; }

        @media (max-width: 768px) { 
            .hero-title { font-size: 50px; } 
            nav { padding: 15px 20px; }
            .nav-links { gap: 15px; }
            .about-content { grid-template-columns: 1fr; }
        }

        .mobile-toggle { display: none; background: none; border: none; color: #fff; font-size: 24px; cursor: pointer; }
        .nav-links.mobile-open { display: flex; flex-direction: column; position: absolute; top: 70px; left: 0; width: 100%; background: rgba(0,0,0,0.95); gap: 0; padding: 20px; }

        @media (max-width: 768px) {
            .mobile-toggle { display: block; }
            .nav-links { display: none; }
            .nav-links.mobile-open { display: flex; }
        }
    </style>
</head>
<body>

    <nav id="navbar">
        <div class="logo" onclick="window.location.href='index.php'"><?php echo $site_name; ?></div>
        <button class="mobile-toggle" onclick="toggleNav()">
            <i class="fas fa-bars"></i>
        </button>
        <ul class="nav-links" id="navMenu">
            <li><a href="#home" class="nav-link active">Home</a></li>
            <li><a href="menu.php" class="nav-link">Menu</a></li>
            <li><a href="#about" class="nav-link">About</a></li>
            <li><a href="#contact" class="nav-link">Contact</a></li>
            <li>
                <a href="cart.php" class="cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count">0</span>
                </a>
            </li>
            <li><a href="login.html" class="login-btn" id="authLink"><i class="fas fa-user-circle"></i> Login</a></li>
        </ul>
    </nav>

    <section class="hero" id="home">
        <div class="hero-background">
            <div class="floating-icon" style="top:20%; left:10%;"><i class="fas fa-pizza-slice"></i></div>
            <div class="floating-icon" style="top:60%; right:15%;"><i class="fas fa-hamburger"></i></div>
            <div class="floating-icon" style="bottom:20%; left:20%;"><i class="fas fa-utensils"></i></div>
            <div class="floating-icon" style="top:30%; right:25%;"><i class="fas fa-birthday-cake"></i></div>
            <div class="floating-icon" style="bottom:30%; right:10%;"><i class="fas fa-leaf"></i></div>
        </div>
        <div class="hero-content">
            <div class="hero-subtitle" style="font-size:18px; color:#FFD93D; letter-spacing:4px; text-transform:uppercase; margin-bottom:20px;">Premium Dining Experience</div>
            <h1 class="hero-title">Welcome to<br><span><?php echo $site_name; ?></span></h1>
            <p style="font-size:20px; color:#ccc; margin-bottom:40px; line-height:1.6;">
                Indulge in an extraordinary culinary journey where taste meets artistry.<br>
                From gourmet dishes to refreshing beverages, we serve excellence on every plate.
            </p>
            <div class="hero-buttons">
                <a href="menu.php" class="btn btn-primary">Explore Menu</a>
                <a href="#contact" class="btn btn-secondary">Reserve Table</a>
            </div>
        </div>
    </section>

    <section class="features" id="features">
        <?php foreach($features as $f): ?>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-<?php echo $f['icon']; ?>"></i></div>
                <h3 style="font-size:24px; color:#FFD93D; margin-bottom:15px; font-weight:bold;"><?php echo $f['title']; ?></h3>
                <p style="color:#ccc; line-height:1.6;"><?php echo $f['desc']; ?></p>
            </div>
        <?php endforeach; ?>
    </section>

    <section class="about" id="about">
        <div class="about-content">
            <div class="about-text">
                <h2>About VOID FOOD COLLECTION 🍽️</h2>
                <p>Since our founding, we've been committed to delivering an unparalleled culinary experience. Our chefs use only the finest ingredients, sourced locally and internationally, to create dishes that tell a story.</p>
                <p>Every plate that leaves our kitchen is a masterpiece, crafted with passion, precision, and an unwavering commitment to excellence.</p>
                <ul>
                    <li>Award-winning chefs with decades of experience</li>
                    <li>Premium organic and ethically sourced ingredients</li>
                    <li>Custom catering and event services available</li>
                    <li>Private dining rooms for special occasions</li>
                    <li>Extensive wine and beverage selection</li>
                </ul>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 120px; color: #FFD93D; opacity: 0.3;">🍽️</div>
            </div>
        </div>
    </section>

    <section class="contact" id="contact">
        <div class="contact-content">
            <h2>Get In Touch 📞</h2>
            <div class="contact-form">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" id="contactName" placeholder="Your name" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" id="contactEmail" placeholder="your@email.com" required>
                </div>
                <div class="form-group">
                    <label>Phone (Optional)</label>
                    <input type="tel" id="contactPhone" placeholder="+1 (555) 123-4567">
                </div>
                <div class="form-group">
                    <label>Message</label>
                    <textarea id="contactMessage" placeholder="Your message..." rows="6" required></textarea>
                </div>
                <button class="submit-btn" onclick="sendMessage()">Send Message</button>
            </div>
        </div>
    </section>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>Void Food Collection</h3>
                <p>Experience culinary excellence in an atmosphere of sophistication and innovation.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="#home">Home</a></li>
                    <li><a href="menu.php">Menu</a></li>
                    <li><a href="#about">About Us</a></li>
                    <li><a href="#contact">Contact</a></li>
                    <li><a href="cart.php">Cart</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Opening Hours</h3>
                <ul>
                    <li>Monday - Thursday: 11:00 AM - 10:00 PM</li>
                    <li>Friday - Saturday: 11:00 AM - 11:00 PM</li>
                    <li>Sunday: 12:00 PM - 9:00 PM</li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Contact Info</h3>
                <ul>
                    <li><i class="fas fa-map-marker-alt"></i> 123 Gourmet Street, Food City</li>
                    <li><i class="fas fa-phone"></i> +1 (555) 123-4567</li>
                    <li><i class="fas fa-envelope"></i> info@voidfood.com</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; <?php echo $current_year; ?> <?php echo $site_name; ?>. All rights reserved. Designed with precision.
        </div>
    </footer>

   <script>
    // --- UNIQUE USER KEY LOGIC ---
    // Creates a unique key like "cart_5" or "cart_guest" based on session
    const currentUserId = <?php echo json_encode($_SESSION['user_id'] ?? 'guest'); ?>;
    const storageKey = 'cart_' + currentUserId;

    const navbar = document.getElementById('navbar');
    const navLinks = document.querySelectorAll('.nav-link');

    /**
     * Updates the cart count badge visually using the specific user's key
     */
    function updateCartUI() {
        // Load ONLY this specific user's cart from local storage
        const userCart = JSON.parse(localStorage.getItem(storageKey)) || [];
        
        // Sum all quantities correctly
        const totalQty = userCart.reduce((sum, item) => sum + (parseInt(item.qty) || 0), 0);
        
        const cartCountEl = document.querySelector('.cart-count');
        if (cartCountEl) {
            cartCountEl.textContent = totalQty;
            // Hide badge if cart is empty, show if items exist
            cartCountEl.style.display = totalQty > 0 ? 'flex' : 'none';
        }
    }

    // Navbar scroll effect
    window.addEventListener('scroll', () => {
        window.scrollY > 50 ? navbar.classList.add('scrolled') : navbar.classList.remove('scrolled');
        updateActiveNav();
    });

    /**
     * Highlights the correct navigation link based on scroll position
     */
    function updateActiveNav() {
        const sections = ['home', 'about', 'contact'];
        let current = 'home';

        sections.forEach(section => {
            const elem = document.getElementById(section);
            if (elem && elem.offsetTop <= window.scrollY + 100) {
                current = section;
            }
        });

        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === '#' + current) {
                link.classList.add('active');
            }
        });
    }

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href === '#') return;
            
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
                navLinks.forEach(link => link.classList.remove('active'));
                this.classList.add('active');
            }
        });
    });

    /**
     * Main Initialization on page load
     */
    window.onload = function() {
        const authLink = document.getElementById('authLink');
        
        // 1. Check if user is logged in to update Login button to Dashboard
        fetch('check-session.php')
            .then(r => r.json())
            .then(d => {
                if (d.logged_in) {
                    authLink.innerHTML = '<i class="fas fa-user-check"></i> Dashboard';
                    authLink.href = 'dashboard.php';
                }
            })
            .catch(err => console.log('Session check failed'));

        // 2. Update the cart count using the unique storage key
        updateCartUI();

        // 3. Set initial active nav state
        updateActiveNav();
    };

    /**
     * Handles Contact Form submission
     */
    async function sendMessage() {
        const name = document.getElementById('contactName').value.trim();
        const email = document.getElementById('contactEmail').value.trim();
        const phone = document.getElementById('contactPhone').value.trim();
        const message = document.getElementById('contactMessage').value.trim();

        if (!name || !email || !message) {
            alert('❌ Please fill in all required fields');
            return;
        }

        // Mocking a successful send
        alert('✅ Thank you, ' + name + '! We will contact you soon at ' + email);
        
        // Clear form
        document.getElementById('contactName').value = '';
        document.getElementById('contactEmail').value = '';
        document.getElementById('contactPhone').value = '';
        document.getElementById('contactMessage').value = '';
    }

    /**
     * Toggles mobile navigation menu
     */
    function toggleNav() {
        const navMenu = document.getElementById('navMenu');
        navMenu.classList.toggle('mobile-open');
    }
    
    // Close mobile menu automatically when a link is clicked
    document.querySelectorAll('.nav-links a').forEach(link => {
        link.addEventListener('click', () => {
            document.getElementById('navMenu').classList.remove('mobile-open');
        });
    });
</script>
</body>
</html>