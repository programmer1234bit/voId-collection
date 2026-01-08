# Void Food - Payment System Setup Guide

## Architecture

```
Void Food (PHP) 
    ↓
process-payment.php (PHP)
    ↓ (HTTP POST)
Aljumah Server (Node.js)
    ↓
Flutterwave API
```

## Setup Instructions

### Step 1: Install Node.js

Download from: https://nodejs.org/

### Step 2: Install Dependencies

```bash
cd "f:\xampp\htdocs\void collection\aljumah"
npm install
```

### Step 3: Verify .env File

Check `aljumah/.env` contains:
```
FLW_CLIENT_ID=572f5f0d-05e8-499f-8497-50baaebd49e6
FLW_CLIENT_SECRET=d2ViENaGmg54gnWNoNooQkAkKiQ1CB50
FLW_WEBHOOK_SECRET_HASH=My_Very_Long_And_Secure_Flutterwave_Hash_For_Samson_2025_Success
PORT=3000
```

### Step 4: Start Aljumah Server

```bash
cd "f:\xampp\htdocs\void collection\aljumah"
npm start
```

You should see:
```
🚀 Aljumah Payment Server running on http://localhost:3000
```

### Step 5: Start XAMPP

- Start Apache
- Start MySQL
- Verify PHP is working

### Step 6: Test the System

1. Open http://localhost/void%20collection/
2. Go to Menu → Add items to cart
3. Click checkout
4. Fill form and click "Create Payment Link & Pay"

## Testing the Payment Flow

### Test Mobile Money (Ghana MTN)

1. Select "📱 Mobile Money"
2. Choose "📱 MTN (Ghana)"
3. Amount: 100
4. Email: test@example.com
5. Name: Test User
6. Click "Create Payment Link & Pay"

### Test Card Payment

1. Select "💳 Card"
2. Choose "💳 Visa"
3. Amount: 100
4. Email: test@example.com
5. Name: Test User
6. Click "Create Payment Link & Pay"

## Troubleshooting

### Error: "Network error connecting to payment service"

**Solution**: Make sure Aljumah server is running:
```bash
cd "f:\xampp\htdocs\void collection\aljumah"
npm start
```

### Error: "Redirect url is invalid"

**Solution**: This is from Flutterwave. Check:
1. Credentials in `.env` are correct
2. Aljumah server is running
3. No network issues

### Error: "Failed to load resource"

**Solution**: Check browser console for CORS errors. The server includes CORS headers.

## Ports

- **PHP/XAMPP**: http://localhost/ (Port 80)
- **Aljumah Server**: http://localhost:3000 (Port 3000)
- **MySQL**: localhost:3306

## File Structure

```
void collection/
├── checkout.php              # Payment form
├── process-payment.php       # PHP wrapper
├── config.php               # Configuration
├── aljumah/
│   ├── server.js           # Node.js Aljumah server
│   ├── package.json        # Dependencies
│   ├── .env                # Credentials
│   ├── routes/
│   │   └── paymentRoutes.js
│   └── public/
│       └── index.html
```

## API Endpoints

### Create Payment Link

**Endpoint**: `POST /payments/create-link`

**Request**:
```json
{
  "amount": 100,
  "email": "user@example.com",
  "name": "John Doe",
  "paymentMethod": "mobile_money",
  "network": "MTN"
}
```

**Response**:
```json
{
  "success": true,
  "link": "https://payment-redirect...",
  "reference": "juma...",
  "currency": "GHS",
  "country": "Ghana"
}
```

## Live Mode

When ready for production:

1. Get live Flutterwave credentials
2. Update `.env` with live credentials
3. Change API endpoint from `developersandbox-api` to `api.flutterwave.com`

## Support

For issues:
1. Check console logs in Aljumah terminal
2. Check browser console (F12)
3. Check PHP error logs in XAMPP

---

**Status**: ✅ Ready for testing
