# 🛍️ Online Store – PHP + MySQL Setup Guide

## Requirements
- PHP 8.1+
- MySQL 8.0+
- A web server (Apache / Nginx) **or** PHP's built-in server for local dev

---

## Quick Start

### 1. Clone / extract the project
```
Exp_7/
├── database/
│   └── schema.sql          ← Run this first
├── includes/
│   ├── Database.php
│   ├── UserModel.php
│   ├── OrderModel.php
│   ├── ProductModel.php
│   ├── Validator.php
│   └── session.php
├── php/
│   ├── login_handler.php
│   ├── register_handler.php
│   ├── checkout_handler.php
│   └── logout.php
├── config.sample.php       ← Copy → config.php
├── db_test.php             ← Verify setup (delete after!)
├── index.php
├── login.php
├── register.php
├── cart.php
└── order_success.php
```

### 2. Create the database
```bash
mysql -u root -p < database/schema.sql
```
This creates the `online_store` database, all 6 tables, seeds 12 products,
and adds a demo user (`demo@store.com` / `demo123`).

### 3. Configure credentials
```bash
cp config.sample.php config.php
```
Edit `config.php` and set your MySQL `user` and `pass`.

### 4. Run locally
```bash
cd Exp_7
php -S localhost:8000
```
Open http://localhost:8000

### 5. Verify DB connection
Visit http://localhost:8000/db_test.php — all checks should show green.
**Delete `db_test.php` before going to production.**

---

## Database Tables

| Table | Purpose |
|---|---|
| `users` | Registered accounts (bcrypt passwords) |
| `products` | Product catalogue (seeded with 12 items) |
| `orders` | Order headers with shipping snapshot |
| `order_items` | Line items per order |
| `login_attempts` | Brute-force rate limiting |
| `user_sessions` | Optional DB-backed session tracking |

---

## Security Features

| Feature | Implementation |
|---|---|
| Password hashing | `password_hash()` bcrypt cost 12 |
| SQL injection | PDO prepared statements throughout |
| XSS | `htmlspecialchars()` on all output |
| CSRF | Per-session token, verified on every POST |
| Brute-force | `login_attempts` table, 10 attempts / 15 min |
| Session fixation | `session_regenerate_id(true)` on login |
| Remember Me | Random token stored as SHA-256 hash in DB |
| Card validation | Luhn algorithm + expiry date check |

---

## Demo Account
- **Email:** demo@store.com  
- **Password:** demo123
