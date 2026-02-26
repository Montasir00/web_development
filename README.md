<div align="center">

# 🌸 Bloom & Basket 🧺

### A full-featured PHP e-commerce platform for floral & gift products
### Containerized with Docker · Secured end-to-end · Admin-ready

[![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![Docker](https://img.shields.io/badge/Docker-Containerized-2496ED?style=for-the-badge&logo=docker&logoColor=white)](https://docker.com)
[![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)](https://developer.mozilla.org/en-US/docs/Web/HTML)
[![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)](https://developer.mozilla.org/en-US/docs/Web/CSS)
[![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
[![License: MIT](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](LICENSE)

<br/>

> *A beautifully crafted e-commerce web application for browsing, purchasing, and managing floral arrangements and gift baskets — built with PHP, MySQL, and Docker.*

</div>

---

## 📖 Table of Contents

- [Overview](#-overview)
- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [Project Structure](#-project-structure)
- [Getting Started](#-getting-started)
- [Docker Setup](#-docker-setup)
- [Database Schema](#-database-schema)
- [Pages & Routes](#-pages--routes)
- [Security](#-security)
- [Admin Dashboard](#-admin-dashboard)
- [Screenshots](#-screenshots)
- [Contributing](#-contributing)
- [Team](#-team)

---

## 🌺 Overview

**Bloom & Basket** is a PHP-based e-commerce platform designed for selling floral arrangements, gift baskets, and seasonal products. It provides a complete shopping experience — from browsing a product catalogue and managing a cart, to secure checkout and order tracking — along with a full-featured admin dashboard for store management.

The entire application is **containerized with Docker**, making it portable, reproducible, and easy to spin up in any environment with a single command.

---

## ✨ Features

### 🛍️ Customer-Facing
- **Product Catalogue** — Browse products with categories, search, and filters
- **Product Detail Pages** — Full descriptions, images, pricing, and stock info
- **User Registration & Login** — Secure account creation and authentication
- **Shopping Cart** — Add, update, and remove items with live price calculation
- **Checkout** — Streamlined order placement with address and payment details
- **Order History** — View past orders and their current status

### 🔧 Admin Panel
- **Admin Dashboard** — At-a-glance overview of sales, orders, and site activity
- **Product Management** — Create, edit, delete, and manage product inventory
- **Order Management** — View all orders and update their statuses
- **User Management** — Monitor and manage registered customers
- **Role-Based Access** — Admin routes fully separated and protected from regular users

### ⚙️ Technical
- **Dockerized** — Full stack spins up with a single `docker-compose up`
- **MySQL Database** — Relational schema with normalized tables
- **PDO** — Database abstraction with prepared statements throughout
- **Session Management** — Secure, server-side session handling
- **Responsive Design** — Mobile-friendly layouts with custom CSS

---

## 🛠️ Tech Stack

| Layer | Technology |
|---|---|
| **Backend** | PHP 8.x |
| **Database** | MySQL 8.0 |
| **Frontend** | HTML5, CSS3, JavaScript (Vanilla) |
| **Web Server** | Apache (via Docker) |
| **Containerization** | Docker & Docker Compose |
| **DB Interface** | PDO (PHP Data Objects) |
| **Styling** | Custom CSS (responsive) |

---

## 📁 Project Structure

```
web_development/
├── docker-compose.yml            # Docker services configuration
├── Dockerfile                    # PHP + Apache image definition
├── .env                          # Environment variables (not committed)
├── .env.example                  # Environment variable template
│
├── public/                       # Web root (Apache document root)
│   ├── index.php                 # Application entry point
│   ├── assets/
│   │   ├── css/
│   │   │   ├── style.css         # Global styles
│   │   │   ├── shop.css          # Product listing styles
│   │   │   └── admin.css         # Admin dashboard styles
│   │   ├── js/
│   │   │   ├── main.js           # Core JS (cart, UI interactions)
│   │   │   └── admin.js          # Admin panel scripts
│   │   └── images/               # Static product & UI images
│   │
│   ├── pages/                    # Customer-facing pages
│   │   ├── home.php              # Landing page & featured products
│   │   ├── shop.php              # Product catalogue / listing
│   │   ├── product.php           # Single product detail page
│   │   ├── cart.php              # Shopping cart
│   │   ├── checkout.php          # Order checkout
│   │   ├── orders.php            # Customer order history
│   │   ├── login.php             # User login
│   │   └── register.php          # User registration
│   │
│   └── admin/                    # Admin-only section (role-gated)
│       ├── dashboard.php         # Admin overview & stats
│       ├── products.php          # Product CRUD management
│       ├── orders.php            # Order management
│       └── users.php             # User management
│
├── src/                          # Core application logic
│   ├── config/
│   │   └── database.php          # PDO database connection
│   ├── controllers/
│   │   ├── AuthController.php    # Login, register, logout
│   │   ├── ProductController.php # Product queries & display
│   │   ├── CartController.php    # Cart logic
│   │   ├── OrderController.php   # Order creation & retrieval
│   │   └── AdminController.php   # Admin operations
│   ├── models/
│   │   ├── User.php
│   │   ├── Product.php
│   │   ├── Cart.php
│   │   └── Order.php
│   └── helpers/
│       ├── auth.php              # Session & role guard functions
│       ├── csrf.php              # CSRF token generation & validation
│       └── sanitize.php          # Input sanitization utilities
│
├── sql/
│   ├── schema.sql                # Database schema
│   └── seed_admin.sql            # Admin user seed script
│
└── apache/
    └── default.conf              # Virtual host & HTTPS configuration
```

---

## 🚀 Getting Started

### Prerequisites

- [Docker](https://docs.docker.com/get-docker/) (v20.10+)
- [Docker Compose](https://docs.docker.com/compose/install/) (v2.0+)
- Git

### Installation

**1. Clone the repository**

```bash
git clone https://github.com/Montasir00/web_development.git
cd web_development
```

**2. Set up environment variables**

```bash
cp .env.example .env
```

Edit `.env` with your configuration:

```env
# Database
DB_HOST=db
DB_PORT=3306
DB_NAME=bloom_basket
DB_USER=bloom_user
DB_PASSWORD=your_secure_password
DB_ROOT_PASSWORD=your_root_password

# Application
APP_ENV=development
APP_URL=http://localhost:8080
APP_SECRET=your_app_secret_key_here

# Session
SESSION_LIFETIME=3600
```

> ⚠️ **Never commit your `.env` file.** It is listed in `.gitignore` by default.

**3. Build and start the containers**

```bash
docker-compose up --build
```

Or in detached (background) mode:

```bash
docker-compose up -d --build
```

**4. Database initialization**

The schema is imported automatically on first run via `sql/schema.sql`. To re-import manually:

```bash
docker-compose exec db mysql -u bloom_user -p bloom_basket < sql/schema.sql
```

**5. Open in your browser**

| URL | Description |
|---|---|
| `http://localhost:8080` | Customer storefront |
| `http://localhost:8080/admin/dashboard.php` | Admin panel |
| `http://localhost:8081` | phpMyAdmin (dev only) |

### Stopping the Application

```bash
# Stop containers (data preserved)
docker-compose down

# Stop and wipe all data volumes
docker-compose down -v
```

---

## 🐳 Docker Setup

Three services are orchestrated via Docker Compose:

| Service | Image | Port | Description |
|---|---|---|---|
| `app` | `php:8-apache` | `8080` | PHP/Apache web server |
| `db` | `mysql:8.0` | `3306` | MySQL database |
| `phpmyadmin` | `phpmyadmin/phpmyadmin` | `8081` | DB admin UI (dev only) |

### Useful Docker Commands

```bash
# View running containers
docker-compose ps

# Tail application logs
docker-compose logs -f app

# Access the PHP container shell
docker-compose exec app bash

# Access MySQL CLI directly
docker-compose exec db mysql -u bloom_user -p

# Rebuild a single service
docker-compose up -d --build app

# Full reset
docker-compose down -v && docker-compose up --build
```

---

## 🗄️ Database Schema

```
┌──────────────┐          ┌──────────────┐          ┌──────────────────┐
│    users     │          │   products   │          │     orders       │
├──────────────┤          ├──────────────┤          ├──────────────────┤
│ id (PK)      │          │ id (PK)      │          │ id (PK)          │
│ name         │          │ name         │          │ user_id (FK)──┐  │
│ email        │          │ description  │          │ total_price   │  │
│ password     │          │ price        │          │ status        │  │
│ role         │          │ stock        │          │ shipping_addr │  │
│ created_at   │─────┐    │ category_id  │─────┐    │ created_at    │  │
└──────────────┘     │    │ image_url    │     │    └───────┬───────┘  │
                     │    └──────────────┘     │            │          │
                     │                         │    ┌───────▼──────────┘
              ┌──────▼──────────┐    ┌─────────▼──┐ │
              │   cart_items    │    │ categories  │ │
              ├─────────────────┤    ├─────────────┤ │  ┌─────────────────┐
              │ id (PK)         │    │ id (PK)     │ │  │   order_items   │
              │ user_id (FK)    │    │ name        │ └─►├─────────────────┤
              │ product_id (FK) │    │ slug        │    │ id (PK)         │
              │ quantity        │    │ description │    │ order_id (FK)   │
              │ added_at        │    └─────────────┘    │ product_id (FK) │
              └─────────────────┘                       │ quantity        │
                                                        │ unit_price      │
                                                        └─────────────────┘
```

**User Roles:** `customer` (default) · `admin`

**Order Statuses:** `pending` → `processing` → `shipped` → `delivered` · `cancelled`

---

## 🗺️ Pages & Routes

### Customer Pages

| Route | Page | Auth Required |
|---|---|---|
| `/` | Home — hero banner & featured products | No |
| `/pages/shop.php` | Product catalogue with filters | No |
| `/pages/product.php?id=` | Product detail page | No |
| `/pages/cart.php` | Shopping cart | ✅ Yes |
| `/pages/checkout.php` | Order checkout & confirmation | ✅ Yes |
| `/pages/orders.php` | Personal order history | ✅ Yes |
| `/pages/login.php` | Login form | No (redirects if logged in) |
| `/pages/register.php` | Registration form | No (redirects if logged in) |

### Admin Pages

| Route | Page | Auth Required |
|---|---|---|
| `/admin/dashboard.php` | Overview stats & alerts | ✅ Admin only |
| `/admin/products.php` | Product CRUD management | ✅ Admin only |
| `/admin/orders.php` | Order status management | ✅ Admin only |
| `/admin/users.php` | Customer & role management | ✅ Admin only |

---

## 🔒 Security

Security is a first-class concern in Bloom & Basket. **Eight layers of protection** are implemented across the full stack:

---

### 1. 🛡️ SQL Injection Prevention — PDO Prepared Statements

Every single database query in the application uses **PDO with parameterized inputs**. User data is never interpolated into SQL strings.

```php
// ✅ Always used — parameterized and safe
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
$stmt->execute([':email' => $email]);
$user = $stmt->fetch();

// ❌ Never used — vulnerable to injection
$result = $pdo->query("SELECT * FROM users WHERE email = '$email'");
```

---

### 2. 🔑 Password Hashing — bcrypt via `password_hash()`

Passwords are **never stored in plaintext**. Registration hashes with `PASSWORD_BCRYPT`; login uses `password_verify()` — which is constant-time, making it safe against timing attacks.

```php
// Registration — hash before storing
$hashed = password_hash($plainPassword, PASSWORD_BCRYPT);

// Login — verify against stored hash
if (password_verify($submittedPassword, $storedHash)) {
    // Authenticated
}
```

---

### 3. 🎫 CSRF Token Protection

Every **state-changing form** (login, register, checkout, cart updates, all admin actions) includes a **CSRF synchronizer token** — a unique, session-bound, single-use value that is validated server-side on every POST. Requests with a missing or mismatched token are rejected immediately.

```php
// Render in every form as a hidden field
$token = generate_csrf_token(); // Stored in $_SESSION
echo '<input type="hidden" name="csrf_token" value="' . $token . '">';

// Validate on every POST before processing
validate_csrf_token($_POST['csrf_token']); // Terminates request on failure
```

---

### 4. 🔐 Session Security

Sessions are hardened against fixation and hijacking:

- **`session_regenerate_id(true)`** — called on login, invalidating the pre-auth session ID
- **Idle timeout** — sessions expire after configurable inactivity; destroyed server-side
- **HttpOnly cookies** — session cookies are inaccessible to JavaScript, blocking XSS-based theft
- **SameSite=Strict** — cookies are not sent on cross-origin requests
- **Secure flag** — cookies are transmitted over HTTPS only in production

```php
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.use_strict_mode', 1);
session_start();

// On successful login
session_regenerate_id(true);
$_SESSION['user_id']     = $user['id'];
$_SESSION['role']        = $user['role'];
$_SESSION['last_active'] = time();
```

---

### 5. 🧹 Input Sanitization & XSS Prevention

All **user-supplied content rendered to HTML** is escaped with `htmlspecialchars()` using UTF-8 encoding, preventing reflected and stored XSS. Raw user input is also sanitized via PHP's `filter_input()` before processing.

```php
// Output escaping — required everywhere user data is rendered
echo htmlspecialchars($userInput, ENT_QUOTES, 'UTF-8');

// Input filtering before processing
$name  = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
```

---

### 6. 👥 Role-Based Access Control (RBAC)

Every admin page calls a **role guard at the very top** — before any HTML, logic, or database calls. Unauthenticated users are redirected to login; authenticated non-admins are bounced to the homepage.

```php
// Top of every admin page
require_once '../../src/helpers/auth.php';
require_admin();

// Guard function
function require_admin(): void {
    if (empty($_SESSION['user_id'])) {
        header('Location: /pages/login.php'); exit;
    }
    if ($_SESSION['role'] !== 'admin') {
        header('Location: /index.php'); exit;
    }
}
```

| Role | Access |
|---|---|
| **Guest** | Public pages only (home, shop, product detail) |
| **Customer** | + Cart, checkout, order history, account |
| **Admin** | + Full admin panel — products, orders, users |

---

### 7. ⏱️ Rate Limiting — Brute Force Protection

Login attempts are **tracked and rate-limited per IP**. After a configurable number of failures within a rolling window, the IP is locked out for a cooldown period — defending against automated brute-force and credential stuffing attacks.

```php
$attempts = get_failed_attempts($_SERVER['REMOTE_ADDR']);

if ($attempts >= MAX_LOGIN_ATTEMPTS) {
    die('Too many failed attempts. Try again in ' . LOCKOUT_DURATION . ' minutes.');
}

// Record each failure; clear on success
record_failed_attempt($_SERVER['REMOTE_ADDR']);
```

---

### 8. 🔒 HTTPS & Secure HTTP Headers

HTTPS is enforced in production at the web server level (HTTP → HTTPS redirect). The application sends the following **security headers** on every response:

```php
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Content-Security-Policy: default-src \'self\'');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
```

---

### Security At a Glance

| Threat | Protection |
|---|---|
| SQL Injection | PDO prepared statements on every query |
| Plaintext Passwords | bcrypt via `password_hash()` |
| CSRF | Synchronizer token on every form POST |
| XSS | `htmlspecialchars()` on all user-rendered output |
| Session Fixation | `session_regenerate_id(true)` on login |
| Session Hijacking | HttpOnly + SameSite + Secure cookie flags |
| Stale Sessions | Idle timeout with server-side destruction |
| Privilege Escalation | Role guard at the top of every protected route |
| Brute Force | IP-rate-limited login with lockout |
| Clickjacking | `X-Frame-Options: DENY` |
| MIME Sniffing | `X-Content-Type-Options: nosniff` |
| Insecure Transport | HTTPS enforcement + HSTS header |

---

## 🖥️ Admin Dashboard

The admin panel is accessible **only to users with the `admin` role** and is fully isolated from the customer-facing site.

### Dashboard Overview
- Total orders (today / all-time)
- Revenue summary
- Recent customer registrations
- Low-stock product alerts

### Product Management (`/admin/products.php`)
- View all products in a sortable, searchable table
- Add products: name, description, price, category, stock, image upload
- Edit existing products
- Delete products with confirmation
- Manage product categories

### Order Management (`/admin/orders.php`)
- View all orders with live status badges
- Filter by: `Pending` · `Processing` · `Shipped` · `Delivered` · `Cancelled`
- Drill into any order to see line items and customer info
- Update status with one click

### User Management (`/admin/users.php`)
- View all registered customers
- See registration date, total orders, last activity
- Promote users to admin or revoke admin privileges

### Creating the First Admin

```sql
-- Run directly in MySQL after setup
UPDATE users SET role = 'admin' WHERE email = 'your@email.com';
```

Or use the seed script:

```bash
docker-compose exec db mysql -u bloom_user -p bloom_basket < sql/seed_admin.sql
```

---

## 📸 Screenshots

> Add screenshots by placing images in `docs/screenshots/` and updating the paths below.

| Page | Preview |
|---|---|
| 🏠 Home | `docs/screenshots/home.png` |
| 🛒 Shop | `docs/screenshots/shop.png` |
| 🌸 Product Detail | `docs/screenshots/product.png` |
| 🧺 Cart | `docs/screenshots/cart.png` |
| ✅ Checkout | `docs/screenshots/checkout.png` |
| 📦 Order History | `docs/screenshots/orders.png` |
| ⚙️ Admin Dashboard | `docs/screenshots/admin-dashboard.png` |
| 📋 Admin Orders | `docs/screenshots/admin-orders.png` |

---

## 🤝 Contributing

Contributions, issues, and feature requests are welcome!

1. **Fork** the repository
2. **Create a branch**: `git checkout -b feature/your-feature`
3. **Commit**: `git commit -m "feat: describe your change"`
4. **Push**: `git push origin feature/your-feature`
5. **Open a Pull Request** against `main`

### Code Guidelines

- Follow **PSR-12** PHP coding standards
- All DB queries **must** use PDO prepared statements
- All new forms **must** include CSRF token protection
- Never commit `.env` or any credentials
- Run `docker-compose up --build` and verify before submitting a PR

---

## 👥 Team

| Name | Student ID | Country |
|---|---|---|
| Pham Gia Khiem | 551026 | 🇻🇳 Vietnam |
| Mohammed Hassan | 541140 | 🇧🇩 Bangladesh |
| Fazlur Rahman | 541927 | 🇧🇩 Bangladesh |

---

## 📄 License

This project is licensed under the **MIT License** — see the [LICENSE](LICENSE) file for details.

---

<div align="center">

Made with 🌸 by the Bloom & Basket Team

*Bachelor in Data Analysis — Web Development Project — 2025/2026*

</div>
