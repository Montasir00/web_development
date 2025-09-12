# Bloom & Basket - Advanced MFA E-Commerce Platform

## Project Overview

### Core Features
- User registration and login with role-based access (user/admin)
- Product browsing, shopping cart management, and checkout
- Admin dashboard for managing products and users
- Secure MFA: Traditional username/password + Telegram-based OTP for out-of-band verification
- Session management and role-based redirection (e.g., admins to `admin_dashboard.php`, users to `index.php`)
- Password recovery and reset functionality
- Expandable to IoT/MPU devices (e.g., Raspberry Pi) in future versions

### Security Enhancements
- **Password Hashing**: Uses PHP's `password_hash()` with bcrypt for secure storage and `password_verify()` for validation
- **SQL Injection Protection**: Employs prepared statements with parameter binding to separate SQL logic from user input
- **OTP Security**: 6-digit random OTPs, stored temporarily with timestamps, expire after 5 minutes, deleted after use
- **Docker Security**: Official images, secure environment variables via `.env` (not hardcoded), minimal port exposure, data persistence via volumes
- **Telegram Integration**: OTPs delivered via custom bot using Telegram API for out-of-band communication

### Core Concepts Applied
- **CIA Triad**:  
  - *Confidentiality*: Hashed passwords, encrypted OTPs  
  - *Integrity*: Exact OTP matching  
  - *Availability*: Modular Docker setup  
- **Authentication vs Authorization**:  
  - Verifies identity (email/password + OTP) before granting access based on roles  
- **MFA Importance**:  
  - Combines "something you know" (password) + "something you have" (Telegram OTP) to mitigate phishing, brute-force, and credential stuffing  

---

## Technologies Used
- **PHP**: Server-side logic, dynamic content, MFA handling  
- **MySQL**: Database for user accounts, products, orders, temporary OTP storage  
- **HTML/CSS/JavaScript**: Front-end structure, styling, interactivity  
- **Docker & Docker Compose**: Containerization for web server, database, Telegram sender  
- **Telegram Bot API**: Secure OTP delivery  
- **Other Tools**: cURL for API requests, `.env` for secure configuration  

---

## File Structure
```plaintext
css/          # Stylesheets
font/         # Custom fonts
includes/     # Reusable PHP files (DB connection, headers/footers)
js/           # Client-side JavaScript
image/        # Website images
users/        # User management files
products/     # Product-related files
admin_dashboard.php
all_products.php
checkout.php
db.php
execute_query.php
forgot_password.php
icon.png
index.php
init.sql
login.php
otp.php
password.php
register.php
reset_password.php
user_dashboard.php
Dockerfile
docker-compose.yml
.env          # Secure API tokens, DB credentials (gitignored)
```
## Setup Instructions

### Prerequisites

- Docker & Docker Compose installed  
- Telegram account and BotFather for creating a bot (API token)  

### Clone the Repository

```bash
git clone <repo-url>
cd bloom-basket
```
### Configure .env
Create a .env file:
```env
TELEGRAM_BOT_TOKEN=your_bot_token_here
DB_HOST=db
DB_USER=root
DB_PASSWORD=your_db_password
DB_NAME=bloom_basket
```
### Run with Docker
```bash
Copy code
docker-compose up -d
Access at http://localhost:8081
```

- MySQL runs internally on port 3306

### Initialize Database
- Run `init.sql` via MySQL client or phpMyAdmin

### Telegram Bot Setup
- Create bot via BotFather
- Users get Chat ID by starting the bot (e.g., via QR code in registration)

### Test OTP sending
- Verify OTP delivery and expiration functionality

---

## Implementation Flow
1. **Registration:** Enter email, password, Telegram Chat ID → hash password → store in DB  
2. **Login:** Validate credentials → redirect to OTP page if valid  
3. **OTP Verification:** Generate/send OTP via Telegram → user enters → verify & redirect by role  
4. **Error Handling:** Logs for API failures; user prompts for retries  

*For a visual flowchart, see the project report (page 10)*

---

## Testing and Results
- OTP generation/delivery/expiry/reuse: 
- Login flow with MFA: 
- Role-based redirection: 
- **Performance:** OTP delivery ~2–5 seconds, 90% success rate  

---

## Potential Vulnerabilities & Mitigations
- **MITM Attacks:** Use HTTPS, Telegram encryption  
- **Compromised Telegram:** Advise 2FA on Telegram, hash OTPs  
- **Brute Force:** Random OTPs, limit login attempts  
- **Container Risks:** Minimal privileges, official images  

---

## Future Enhancements
- Integrate MPU/IoT for physical authentication (Raspberry Pi)  
- Add ML-based recommendations or analytics  
- Biometric MFA
