# VOID FOOD COLLECTION - E-Commerce Platform

A premium food ordering and payment system built with PHP, MySQL, and Flutterwave payment gateway.

---

## 🚀 Key Features

*   **Secure Payment Integration:** Seamless mobile money and card payments via Flutterwave.
*   **Tamper-Proof Pricing:** Server-side price calculation prevents clients from manipulating order totals.
*   **Robust User Logic:** Complete flow for Registration, Login, Shopping Cart, Checkout, and Order History.
*   **Payment Verification:** Automated checking of payment status with database persistence.
*   **Localhost Development Support:** customized workflow using `lvh.me` to support Flutterwave callbacks on local XAMPP servers.
*   **Admin Panel:** Manage menu items, orders, and users.

---

## 📁 Project Structure

```
void collection/
│
├── 📄 index.php                 # Homepage
├── 📄 config.php               # Database & API configuration
├── 📄 api.php                  # Central REST API for frontend interactions
├── 📄 README.md                # This file
│
├── 🍽️ CORE PAGES
│   ├── menu.php                # Browse food items
│   ├── cart.php                # Manage shopping cart
│   ├── checkout.php            # Secure checkout form
│   ├── orders.php              # User order history
│   └── dashboard.php           # User profile
│
├── 💳 PAYMENT SYSTEM (In `Payment/` directory)
│   ├── process-payment.php     # Logic to create Flutterwave payment links
│   ├── payment-processor.php   # Class for handling API communication
│   ├── payment-success.php     # Landing page after successful payment
│   ├── test-webhook.php        # Tool for testing webhooks locally
│   └── test_flw_connection.php # Diagnostic script for API connection
│
├── 🛠️ ADMIN PANEL (In `admin/` directory)
│   ├── dashboard.php, orders.php, menu.php, users.php
│
└── 🗄️ DATABASE
    └── void table.sql          # SQL schema for setting up the database
```

---

## ⚙️ Logic & Workflows

### 1. Payment Security Flow
To prevent fraud, this project uses a **Server-Side Price Authority** model:
1.  **Frontend:** Sends a list of Item IDs and Quantities (not prices) to the server.
2.  **Backend (`process-payment.php`):**
    *   Receives the item list.
    *   Queries `menu` table to get the *actual* current price of each item.
    *   Calculates `Total = (Item Price * Qty) + Delivery Fee`.
    *   Ignores any total amount sent by the client.
3.  **Result:** Users are charged the correct amount regardless of frontend manipulation.

### 2. Localhost Verification Flow
Flutterwave requires a public URL for callbacks, which `localhost` supports poorly. We solved this with **lvh.me**:
1.  **Payment Request:** Application detects `localhost` and swaps the return URL to `http://lvh.me/...`.
    *   *Note: `lvh.me` is a public domain that DNS resolves to 127.0.0.1 (your computer).*
2.  **Redirect:** Flutterwave accepts `lvh.me` and redirects the user there after payment.
3.  **Session Restoration:** The `payment-success.php` page handles the verification and provides links explicitly pointing back to `http://localhost/...` to restore the user's session cookies.

---

## 🔧 Setup & Configuration

### Prerequisites
*   **XAMPP** (Apache + MySQL)
*   **PHP 7.4+**

### 1. Database Setup
1.  Open phpMyAdmin (`http://localhost/phpmyadmin`).
2.  Create a database named `void_food`.
3.  Import `void table.sql` (or `void2 tables.sql`) to create the structure.
4.  Run `setup-database.php` (if available) to initialize admin accounts.

### 2. Configuration (`config.php`)
Ensure your database and API keys are set:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'void_food');

// Flutterwave Keys (Sandbox/Test Mode)
define('FLW_CLIENT_ID', '...');
define('FLW_CLIENT_SECRET', '...');
```

---

## 🛡️ Security & Production Notes

### Current Status: **Presentation Ready**
The project is fully functional for demonstrations, final year projects, and local testing.

### Deployment / "Going Live" Requirements
Before hosting this on a real server for actual money, you **MUST** address the following:
1.  **Disable Verification Bypass:** currently, `payment-success.php` allows a bypass for 404 Sandbox errors. Uncomment the strict verification logic for production.
2.  **Secure Database:** Do not use `root` with no password in a live environment.
3.  **Environment Variables:** Move API keys from `config.php` to a hidden `.env` file.
4.  **HTTPS:** Ensure the server runs on SSL to protect user data.

---

## 👤 Admin Access
*   **URL:** `http://localhost/void collection/admin/login.php`
*   **Email:** `admin@voidfood.com`
*   **Password:** `Admin@123456`

---

## 📝 License
© 2025 VOID FOOD COLLECTION. All rights reserved.