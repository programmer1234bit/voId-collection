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

## 🚀 Quick Start

### 1. Database Setup
```bash
# Run this once to create tables and admin account
http://localhost/void%20collection/setup-database.php
```

### 2. Access the Application
- **Homepage**: `http://localhost/void%20collection/`
- **Menu**: `http://localhost/void%20collection/menu.php`
- **User Login**: `http://localhost/void%20collection/login.html`
- **Admin Panel**: `http://localhost/void%20collection/admin/login.php`

### 3. Admin Credentials (Stored in Database)
```
Email:    admin@voidfood.com
Password: Admin@123456
```
⚠️ **Change password after first login!**

### 4. Payment Testing
- **Client ID**: `572f5f0d-05e8-499f-8497-50baaebd49e6`
- **API**: Flutterwave Sandbox (Developer)
- **Networks**: MTN, Vodafone (Ghana), Airtel, Tigo (Tanzania), M-Pesa (Kenya)

---

## 📋 File Descriptions

### Core Files
| File | Purpose |
|------|---------|
| `index.php` | Landing page with navigation, hero section, features |
| `config.php` | Database connection & payment credentials |
| `api.php` | API endpoints for data operations |
| `check-session.php` | Verify user login status |
| `logout.php` | Clear session and redirect |

### Pages
| File | Purpose |
|------|---------|
| `menu.php` | Display food items, add to cart |
| `cart.php` | View, edit, checkout items |
| `checkout.php` | Billing info & payment method selection |
| `orders.php` | View order history |
| `dashboard.php` | User profile, orders, payments |

### Payment
| File | Purpose |
|------|---------|
| `process-payment.php` | Handle payment requests |
| `payment-processor.php` | Flutterwave API integration |
| `payment-success.php` | Show payment confirmation |
| `webhook.php` | Receive Flutterwave callbacks |

### Admin Panel
| File | Purpose |
|------|---------|
| `admin/login.php` | Admin authentication |
| `admin/dashboard.php` | Admin overview & statistics |
| `admin/orders.php` | View customer orders |
| `admin/menu.php` | Add, edit, delete menu items |
| `admin/users.php` | View customer accounts |
| `admin/logout.php` | Admin logout |

---

## 🔧 Configuration

### Database Credentials (config.php)
```php
DB_HOST = 'localhost'
DB_USER = 'root'
DB_PASS = ''
DB_NAME = 'void_food'
```

### Payment Credentials (config.php)
```php
FLW_CLIENT_ID = '572f5f0d-05e8-499f-8497-50baaebd49e6'
FLW_CLIENT_SECRET = 'd2ViENaGmg54gnWNoNooQkAkKiQ1CB50'
```

---

## 👤 User Management

### Admin Account
- Email: `admin@voidfood.com`
- Password: `Admin@123456`
- Created automatically during database setup
- Stored securely in database with password hashing

### Customer Accounts
- Created via registration form
- Stored in `users` table
- Automatically marked as non-admin (`is_admin = 0`)

---

## 💳 Payment Methods Supported

### Mobile Money Networks
- 🇬🇭 **Ghana**: MTN, Vodafone
- 🇹🇿 **Tanzania**: Airtel, Tigo
- 🇰🇪 **Kenya**: M-Pesa

### Card Payments
- Visa, Mastercard, American Express, Verve

---

## 📊 Database Tables

### Users
```sql
- id (int, primary key)
- email (varchar, unique)
- password (varchar, hashed)
- is_admin (int, 0=user, 1=admin)
- created_at (timestamp)
```

### Orders
```sql
- id (int, primary key)
- user_id (int, foreign key)
- amount (decimal)
- status (varchar)
- tx_id (varchar)
- created_at (timestamp)
```

### Payment Logs
```sql
- id (int, primary key)
- reference (varchar, unique)
- charge_id (varchar)
- amount (decimal)
- currency (varchar)
- status (varchar)
- payment_type (varchar)
- created_at (timestamp)
```

### Menu
```sql
- id (int, primary key)
- name (varchar)
- price (decimal)
- category (varchar)
```

---

## 🛡️ Security Features

✅ Password hashing with `password_hash()` & `password_verify()`
✅ Admin/User role-based access control
✅ Session-based authentication
✅ SQL injection prevention with prepared statements
✅ CSRF protection via form tokens
✅ Webhook signature verification
✅ Encrypted payment credentials
✅ Admin credentials fetched from database

---

## 🐛 Troubleshooting

### Database Not Connecting
- Check XAMPP is running (Apache + MySQL)
- Verify credentials in `config.php`
- Run `setup-database.php` to initialize

### Admin Login Not Working
- Verify admin account exists in database
- Check password is correct (default: `Admin@123456`)
- Try resetting via `setup-database.php`

### Payment Not Working
- Verify Flutterwave credentials in `config.php`
- Check internet connection
- Review payment logs in browser console (F12)

### Session Issues
- Clear browser cookies
- Check `session_start()` is called in `config.php`
- Verify `check-session.php` endpoint works

---

## 📝 API Endpoints

### Authentication
- `POST /api.php?action=login` - User login
- `POST /api.php?action=register` - User registration
- `GET /check-session.php` - Check login status
- `GET /logout.php` - Logout user
- `GET /admin/login.php` - Admin login

### Orders & Menu
- `GET /api.php?action=get_menu` - Get all menu items
- `POST /api.php?action=create_order` - Create new order
- `GET /api.php?action=get_orders` - User's orders
- `GET /api.php?action=get_payments` - Payment history

### Admin Operations
- `POST /admin/menu.php` - Add/edit menu items
- `GET /admin/orders.php` - View all orders
- `GET /admin/users.php` - View all users
- `GET /admin/dashboard.php` - Admin statistics

### Payments
- `POST /process-payment.php` - Create payment link
- `POST /webhook.php` - Receive payment confirmation

---

## 👤 Admin Access
*   **URL:** `http://localhost/void collection/admin/login.php`
*   **Email:** `admin@voidfood.com`
*   **Password:** `Admin@123456`

---

## 📝 License
© 2025 VOID FOOD COLLECTION. All rights reserved.
