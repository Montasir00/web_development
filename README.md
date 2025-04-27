# 🌸 Bloom & Basket 🧺 - E-commerce Website

This project is the codebase for the Bloom & Basket e-commerce website. It includes features for user registration, login, browsing products, managing a shopping cart, and processing checkout. An admin dashboard is also included for managing products and users.

## 🛠️ Technologies Used

* **PHP:** For server-side logic and handling dynamic content.
* **MySQL:** For storing website data (user accounts, products, orders, etc.).
* **HTML:** For structuring the web pages.
* **CSS:** For styling the website and providing a visually appealing user interface.
* **JavaScript:** For enhancing interactivity and user experience on the front-end.
* **Docker:** For containerizing the application and its dependencies, ensuring consistent deployment across different environments.
* **Docker Compose:** For orchestrating the multi-container Docker application (web server, database).


* `Dockerfile`: Configuration for building the Docker image for the web application.
* `docker-compose.yml`: Configuration for defining and managing the Docker containers (e.g., web server and MySQL database).
* `css/`: Contains the stylesheets for the website's appearance.
* `font/`: Stores any custom fonts used in the website.
* `includes/`: Contains reusable PHP files such as database connection scripts, header, footer, or functions.
* `js/`: Holds the JavaScript files for client-side functionality.
* `image/`: Contains images used on the website.
* `users/`: Contain specific files related to user management.
* `products/`: Contain files related to product display or management.
* `admin_dashboard.php`: The main page for the administrative interface.
* `all_products.php`: Page to display all available products.
* `checkout.php`: Handles the checkout process for users.
* `db.php`: Contains database connection details and functions.
* `execute_query.php`: Uutility script to run database queries.
* `forgot_password.php`: Handles the password recovery process.
* `icon.png`: The website's favicon or a small icon.
* `index.php`: The main homepage of the Bloom & Basket website.
* `init.sql`: SQL script to initialize the database schema and possibly initial data.
* `login.php`: Handles user login functionality.
* `password.php`: Password management or updates.
* `register.php`: Handles user registration.
* `reset_password.php`: Handles the process of resetting a user's password.

## 🚀 Getting Started (Development)

1.  **Clone the repository:**
    ```bash
    git clone <repository_url>
    cd bloom_and_basket
    ```
    *(Replace `<repository_url>` with the actual URL of your repository)*

2.  **Ensure Docker and Docker Compose are installed** on your system.

3.  **Start the Docker containers:**
    ```bash
    docker-compose up -d
    ```
    This will build and start the web server and MySQL database containers.

4.  **Access the website:** Open your web browser and navigate to `http://localhost` (or the appropriate port if configured differently in `docker-compose.yml`).

5.  **Database Initialization:** The `init.sql` file should automatically create the necessary database schema and potentially seed initial data when the MySQL container starts.

## 🔑 Key Features

* User registration and login.
* Product catalog browsing.
* Shopping cart functionality.
* Secure checkout process.
* Admin dashboard for product and user management.
* Password recovery options.

## 🐳 Docker Information

This project is containerized using Docker for easy setup and deployment. The `Dockerfile` defines the environment for the PHP web application, and `docker-compose.yml` orchestrates the web server and the MySQL database.
