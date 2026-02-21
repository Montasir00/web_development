# Bloom & Basket - E-commerce Website

A full-featured e-commerce web application built with PHP and MySQL, containerized with Docker. The project includes a complete shopping experience, a Telegram-based OTP authentication system, HTTPS support via Nginx, and an admin dashboard.

## Technologies Used

- **PHP** - Server-side logic and dynamic content
- **MySQL** - Database for users, products, orders, and cart data
- **HTML / CSS / JavaScript** - Front-end structure, styling, and interactivity
- **Docker & Docker Compose** - Containerized multi-service deployment
- **Nginx** - Reverse proxy with HTTPS/TLS termination
- **Telegram Bot API** - OTP delivery for multi-factor authentication

## Project Structure

```
.
├── blog/                    # Blog detail pages
├── certs/                   # TLS certificates for HTTPS (web.crt, web.key)
├── css/                     # Stylesheets for all pages
├── font/                    # Custom fonts
├── image/                   # Static images
├── includes/                # Shared PHP includes
│   ├── init.php             # Session hardening, CSRF init, DB bootstrap
│   ├── csrf.php             # CSRF token generation and validation
│   ├── functions.php        # Shared helper functions
│   ├── header.php           # Site-wide header
│   ├── footer.php           # Site-wide footer
│   ├── add_to_cart.php      # Add-to-cart handler
│   ├── remove_from_cart.php # Remove-from-cart handler
│   ├── get_cart_details.php # Cart data retrieval
│   ├── login_handler.php    # Login processing
│   ├── logout.php           # Session logout
│   ├── products.php         # Product listing include
│   ├── reviews.php          # Product reviews include
│   ├── blogs.php            # Blog listing include
│   ├── banner.php           # Homepage banner
│   ├── features.php         # Features section
│   ├── cta.php              # Call-to-action section
│   └── update.php           # Profile/data update handler
├── js/                      # JavaScript files
├── mfa/                     # Multi-factor authentication utilities
│   ├── utils.php            # OTP generation and verification logic
│   └── verify.php           # MFA verification endpoint
├── nginx/
│   └── default.conf         # Nginx reverse proxy configuration
├── otp-service/             # Dockerized Telegram OTP microservice
│   ├── Dockerfile
│   ├── send_otp.php         # Sends OTP via Telegram bot
│   ├── telegram_config.php  # Telegram bot credentials and helpers
│   ├── chat_id_bot.php      # Resolves Telegram chat ID for a user
│   └── activate_chat_bot.php
├── otp_storage/             # File-based OTP session storage (mounted volume)
├── products/                # Product management pages
├── users/                   # User profile/account pages
├── admin_dashboard.php      # Admin panel entry point
├── all_products.php         # Full product catalog page
├── checkout.php             # Checkout form and order summary
├── payment.php              # OTP-verified payment step
├── order_confirmation.php   # Post-order confirmation page
├── otp.php                  # Telegram OTP login page
├── login.php                # Standard login page
├── register.php             # User registration
├── forgot_password.php      # Password recovery flow
├── reset_password.php       # Password reset handler
├── password.php             # Password change page
├── index.php                # Homepage
├── db.php                   # Database connection
├── init.sql                 # Database schema and seed data
├── execute_query.php        # Admin query execution utility
├── Dockerfile               # PHP/Apache web container
├── docker-compose.yml       # Multi-service orchestration
└── .env                     # Environment variables (not committed)
```

## Key Features

### Authentication & Security
- Standard email and password login with session management
- **Telegram OTP login** - users can authenticate using a one-time password delivered via Telegram bot
- **CSRF protection** on all forms using cryptographically secure per-session tokens
- **Session hardening** - sessions are bound to IP address and User-Agent; sessions expire after 30 minutes of inactivity
- Password recovery via forgot/reset password flow
- Role-based access (admin vs. regular user)

### Shopping & Orders
- Product catalog with browsing and filtering
- Shopping cart (add, remove, update quantities)
- Multi-step checkout with shipping address and payment method selection
- **OTP-verified payment** - users must verify identity via Telegram OTP before an order is finalized
- Order confirmation page with itemized order summary
- Admin dashboard for managing products and users

### Content
- Blog section with individual blog detail pages
- Homepage with banner, featured products, and call-to-action sections

### Infrastructure
- **Nginx reverse proxy** with HTTPS/TLS support on port 8443
- **Telegram OTP microservice** running as a dedicated Docker container
- **phpMyAdmin** available at `http://localhost:8082` for database management
- OTP storage mounted as a Docker volume for persistence across restarts

## Getting Started

### Prerequisites
- [Docker](https://www.docker.com/) and Docker Compose installed
- A Telegram bot token (see [BotFather](https://core.telegram.org/bots#botfather))
- TLS certificates placed in `certs/web.crt` and `certs/web.key` (self-signed is fine for local development)

### Setup

1. **Clone the repository:**
   ```bash
   git clone <repository_url>
   cd bloom-and-basket
   ```

2. **Configure environment variables:**

   Create a `.env` file in the project root:
   ```env
   TELEGRAM_BOT_TOKEN=your_telegram_bot_token_here
   ```

3. **Start all containers:**
   ```bash
   docker-compose up -d
   ```

   This will start:
   | Container     | Description                        | Port          |
   |---------------|------------------------------------|---------------|
   | `web`         | PHP/Apache application             | (internal)    |
   | `db`          | MySQL 5.7 database                 | 3307          |
   | `otp-service` | Telegram OTP delivery service      | (internal)    |
   | `nginx`       | Reverse proxy with HTTPS           | 8081, 8443    |
   | `phpmyadmin`  | Database management UI             | 8082          |

4. **Access the site:**
   - HTTP (via Nginx): `http://localhost:8081`
   - HTTPS (via Nginx): `https://localhost:8443`
   - phpMyAdmin: `http://localhost:8082`

5. **Database initialization:**

   The `init.sql` file is loaded automatically by the MySQL container on first start, creating all required tables and seeding initial data.

### Telegram OTP Setup

Each user who wants to use Telegram OTP or OTP-verified payments must link their Telegram account to their registered email. Use `otp-service/chat_id_bot.php` to retrieve and associate a user's Telegram chat ID.

## Docker Services

| Service       | Image / Build                  | Purpose                            |
|---------------|--------------------------------|------------------------------------|
| `web`         | Built from `./Dockerfile`      | PHP 8 + Apache web server          |
| `db`          | `mysql:5.7`                    | Relational database                |
| `otp-service` | Built from `./otp-service/`    | Telegram bot OTP sender            |
| `nginx`       | `nginx:alpine`                 | TLS termination and reverse proxy  |
| `phpmyadmin`  | `phpmyadmin/phpmyadmin:latest` | Browser-based DB management        |

All services share the `bloom_network` Docker bridge network.
