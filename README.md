# Comprehensive POS System (Plain PHP/MySQLi)

This is a robust, user-friendly, and feature-rich Point of Sale (POS) system designed for retail operations. The system includes core POS functionalities, comprehensive inventory management with reorder level alerts, sales and purchase management (including returns), and basic accounting features.

## Technical Stack

*   **Backend:** Plain PHP (version 7.4+ recommended), MySQLi (direct queries)
*   **Database:** MySQL
*   **Frontend:** HTML5, CSS3, Bootstrap (CDN), jQuery (CDN)

## Features

*   **Point of Sale (POS):** Intuitive sales interface, cart management, payment processing, and receipt generation.
*   **Inventory Management:** Product, supplier, and category management, stock adjustments, and low-stock alerts.
*   **Purchase Management:** Purchase order creation, goods receipt, and purchase returns.
*   **Expense Tracking:** Record and categorize daily business expenses.
*   **Reporting:** A comprehensive suite of reports for sales, purchases, expenses, cash flow, and inventory.
*   **User Management:** Role-based access control for Admins and Cashiers.
*   **System Settings:** Configure store information, tax rates, and more.

## Setup Instructions

1.  **Database Setup:**
    *   Create a new database in your MySQL server (e.g., `pos_system`).
    *   Import the `database.sql` file into your newly created database. This will create all the necessary tables.

2.  **Configuration:**
    *   Open the `includes/config.php` file.
    *   Update the database credentials (`DB_HOST`, `DB_USERNAME`, `DB_PASSWORD`, `DB_NAME`) to match your environment.

3.  **Deployment:**
    *   Upload all the project files to your web server.
    *   Ensure your server is running PHP 7.4 or higher.

4.  **Initial Login:**
    *   Navigate to the project's URL in your web browser.
    *   The first time you access the system, you will need to register a new user. Click on the "Register here" link.
    *   Create an "Admin" user to have full access to all modules.
    *   After registration, you can log in with your new credentials.

## Default Credentials

There are no default credentials. You must register a new user to get started.

## File Structure

*   `/admin`: Contains all administrative modules (user management, products, suppliers, etc.).
*   `/assets`: Contains CSS, JavaScript, and image files.
*   `/includes`: Contains core files like database configuration and session management.
*   `/pos`: Contains the main POS interface and related files.
*   `/templates`: Contains reusable header and footer files.
*   `database.sql`: The database schema.
*   `index.php`: The main login page.
*   `README.md`: This file.
