<div align="center">

# Bloom & Basket
## E-Commerce Platform

### A full-featured PHP e-commerce system  
### Dockerized · Secure · Admin-ready

[![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php&logoColor=white)]
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)]
[![Docker](https://img.shields.io/badge/Docker-Containerized-2496ED?style=for-the-badge&logo=docker&logoColor=white)]
[![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)]
[![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)]
[![JavaScript](https://img.shields.io/badge/JavaScript-Vanilla-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)]
[![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)]

---

A production-style PHP e-commerce application for selling foods, featuring secure authentication, role-based access control, and a fully containerized Docker environment.

</div>

---

## Contents

- Overview
- Features
- Tech Stack
- Project Structure
- Getting Started
- Docker Architecture
- Database Design
- Pages & Routes
- Security Architecture
- Admin Dashboard
- Screenshots
- Contributing
- License

---

## Overview

**Bloom & Basket** is a PHP-based e-commerce platform designed for real-world deployment.  
It delivers a complete shopping workflow — product browsing, cart management, checkout, and order tracking — alongside a protected administrative dashboard for inventory, users, and orders.

The entire stack is containerized using **Docker**, enabling deterministic builds and easy environment replication.

---

## Features

### Customer-Facing
- Product catalog with categories, search, and filters
- Detailed product pages with pricing and availability
- Secure user registration and authentication
- Session-based shopping cart
- Checkout with address and order confirmation
- Personal order history and status tracking

### Administrative
- Sales and order overview dashboard
- Product and inventory management (CRUD)
- Order lifecycle management
- User and role administration
- Fully isolated admin routes

### Technical
- Docker-based deployment
- MySQL relational database
- PDO with prepared statements
- Server-side session management
- Responsive frontend layout

---

## Tech Stack

| Layer | Technology |
|------|-----------|
| Backend | PHP 8.x |
| Database | MySQL 8.0 |
| Frontend | HTML5, CSS3, Vanilla JavaScript |
| Web Server | Apache (Docker) |
| Containerization | Docker & Docker Compose |
| Database Interface | PDO |
| Styling | Custom responsive CSS |

---
## Project Structure

```text
web_development/
├── docker-compose.yml
├── Dockerfile
├── .env.example
│
├── public/
│   ├── index.php
│   ├── assets/
│   ├── pages/
│   └── admin/
│
├── src/
│   ├── config/
│   ├── controllers/
│   ├── models/
│   └── helpers/
│
├── sql/
│   ├── schema.sql
│   └── seed_admin.sql
│
└── apache/
    └── default.conf
```
## Getting Started

### Requirements
- Docker 20.10+
- Docker Compose 2.0+
- Git

### Installation

```bash
git clone https://github.com/Montasir00/web_development.git
cd web_development
cp .env.example .env
docker-compose up --build
```

## Access Points

| URL | Purpose |
|----|--------|
| http://localhost:8080 | Customer storefront |
| http://localhost:8080/admin/dashboard.php | Admin panel |
| http://localhost:8081 | phpMyAdmin (development) |

---

## Docker Architecture

| Service | Image | Port | Role |
|-------|-------|------|-----|
| app | php:8-apache | 8080 | Web server |
| db | mysql:8.0 | 3306 | Database |
| phpmyadmin | phpmyadmin/phpmyadmin | 8081 | DB admin UI |

---

## Database Design

- Normalized relational schema  
- Foreign key enforcement  
- Order → Order Items mapping  
- Role-based user model  

**Roles:** `customer`, `admin`  
**Order States:** `pending`, `processing`, `shipped`, `delivered`, `cancelled`

---

## Pages & Routes

### Customer

| Route | Description |
|------|------------|
| `/` | Home |
| `/pages/shop.php` | Product catalog |
| `/pages/product.php` | Product detail |
| `/pages/cart.php` | Shopping cart |
| `/pages/checkout.php` | Checkout |
| `/pages/orders.php` | Order history |

### Admin

| Route | Description |
|------|------------|
| `/admin/dashboard.php` | Dashboard |
| `/admin/products.php` | Product management |
| `/admin/orders.php` | Order management |
| `/admin/users.php` | User management |

---

## Security Architecture

Implemented as a defense-in-depth model:

- PDO prepared statements (SQL injection prevention)
- bcrypt password hashing
- CSRF synchronizer tokens
- Session regeneration and idle timeout
- HttpOnly, Secure, SameSite cookies
- Output escaping against XSS
- Role-based access control
- Login rate limiting
- HTTPS and hardened security headers

---

## Admin Dashboard

Admin functionality is fully isolated and role-gated.

**Capabilities include:**
- Sales analytics
- Inventory management
- Order status control
- User role promotion

---

## Screenshots

Add screenshots under `docs/screenshots/` and reference them here.

---

## Contributing

1. Fork the repository  
2. Create a feature branch  
3. Commit following conventional commits  
4. Open a pull request against `main`

---

## License

MIT License — see `LICENSE` for details.

---

<div align="center">

Bloom & Basket  
Bachelor in Data Analysis — Web Development Project  
Academic Year 2025 / 2026

</div>
