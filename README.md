# POS PC Project

A comprehensive Point of Sale (POS) system built with Laravel for managing product sales, inventory, customers, and business operations.

## 🚀 Project Overview

This POS system is designed to help businesses efficiently manage their retail operations, including product inventory, customer relationships, sales processing, and financial reporting. The system provides a user-friendly interface for both sales staff and administrators.

## 🛠️ Technology Stack

### Backend
- **Framework**: Laravel 12 (PHP 8.2+)
- **Database**: MySQL
- **Architecture**: MVC (Model-View-Controller)

### Frontend
- **HTML5** - Semantic markup and structure
- **CSS3** - Custom styling and responsive design
- **Bootstrap** - UI components and responsive grid system
- **JavaScript** - Interactive functionality
- **jQuery** - DOM manipulation and AJAX requests
- **Tailwind CSS** - Utility-first CSS framework (compiled via Vite)
- **Blade Templates** - Laravel's server-side templating engine (not API-based)

### Additional Features
- **KHQR Payment Integration** - Cambodian QR code payment system
- **Groq AI Chatbot** - AI-powered customer assistance
- **Email OTP** - Secure password reset functionality
- **Role-based Access Control** - Admin, Manager, and Staff permissions

## 📋 Key Features

### 🛒 Point of Sale (POS)
- Real-time sales processing
- Customer search and management during checkout
- Multiple payment methods (Cash, Card, KHQR)
- Receipt generation and printing
- Order history tracking
- Customer debt management

## 📘 User Guide

For a detailed usage manual, see [USAGE.md](USAGE.md).

### 📦 Inventory Management
- Product catalog with categories
- Stock level monitoring
- Low stock alerts and reorder points
- Inventory adjustments and history
- Product attributes (variants)

### 👥 Customer Management
- Customer database with contact information
- Purchase history tracking
- Credit/debt management
- Customer search and filtering

### 🏪 Supplier & Purchasing
- Supplier management
- Purchase order processing
- Stock replenishment tracking

### 💰 Financial Management
- Sales reporting
- Expense tracking
- Payment processing
- Profit/loss analysis

### 👤 User Management
- Multi-user support with role-based permissions
- User authentication and authorization
- Profile management
- Password reset with OTP

### 🤖 AI Integration
- Groq-powered chatbot for customer support
- Intelligent product recommendations

## 🏗️ System Architecture

The application follows Laravel's MVC architecture:

- **Models**: Business logic and database interactions
  - Product, Category, Customer, Order, Inventory, etc.
- **Views**: Blade templates for server-side rendering
- **Controllers**: Handle HTTP requests and responses
- **Routes**: Define application endpoints
- **Middleware**: Authentication, authorization, and request filtering

## 🗄️ Database Schema

Key tables include:
- `users` - System users with roles
- `products` - Product catalog
- `categories` - Product categories
- `customers` - Customer information
- `orders` - Sales transactions
- `order_details` - Order line items
- `inventory` - Stock levels
- `suppliers` - Supplier information
- `purchases` - Purchase orders
- `payments` - Payment records
- `expenses` - Business expenses

## 🚀 Installation & Setup

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js & npm
- MySQL database
- Git

### Installation Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd pos_pc_project
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database setup**
   - Create a MySQL database
   - Update `.env` with database credentials
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Build assets**
   ```bash
   npm run build
   ```

7. **Storage link**
   ```bash
   php artisan storage:link
   ```

8. **Start the application**
   ```bash
   php artisan serve
   ```

## 📱 Usage

### For Sales Staff
1. Login to the POS interface
2. Search and add products to cart
3. Process customer information
4. Select payment method
5. Generate receipt

### For Administrators
1. Access dashboard for overview
2. Manage products, categories, and inventory
3. Handle customer and supplier relationships
4. Generate reports and analytics
5. Configure system settings

## 🔧 Configuration

### Payment Integration
- Configure KHQR API credentials in `.env`
- Set up payment processing endpoints

### AI Chatbot
- Configure Groq API key for chatbot functionality
- Customize chatbot responses and behavior

### Email Settings
- Configure SMTP settings for OTP emails
- Set up email templates

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## 📄 License

This project is licensed under the MIT License.

## 📞 Support

For support and questions, please contact the development team.

---

**Note**: This system uses server-side rendering with Blade templates rather than a separate API. All frontend interactions are handled through traditional form submissions and AJAX calls to Laravel routes.

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).













## How to Use This System

### Welcome to Your POS System

This system is built to help retailers run a complete point-of-sale business from one platform. It combines sales, inventory, customer management, purchasing, payments, reporting, and user access control.

Use this guide like a short book: start with the role that matches your job, then follow the steps for your daily tasks.

---

## 1. System Roles and What They Do

The application supports role-based access control. Each person who logs in belongs to one of these roles:

- **Admin**
  - Full system access across dashboards, user management, settings, reports, and sales.
  - Best for business owners and system administrators.
- **Manager**
  - Access to the dashboard, sales, customers, products, inventory, suppliers, purchases, and reports.
  - Best for store managers and supervisors.
- **Staff**
  - Access primarily to the sales / POS workflow and product view.
  - Best for checkout clerks and sales associates.

> Roles are enforced by the system. If a menu item or page is not visible, your account does not have permission for that feature.

---

## 2. First Steps: Logging In and Getting Started

1. Open the system URL in your browser.
2. Go to the login page and enter your username and password.
3. If you do not yet have an account, use the registration page or ask an Admin to create one for you.
4. After login, the menu is tailored to your role.
5. If you are an Admin or Manager, you may see a dashboard summary.

### What the Dashboard Shows

The dashboard gives you at-a-glance information for:

- Total sales and revenue
- Recent orders
- Stock warnings and inventory status
- Customer activity
- Pending user approvals (Admins only)

---

## 3. Daily Workflows by Role

### Admin Workflow

As an Admin, your daily responsibilities typically include:

- Reviewing the dashboard and daily reports.
- Managing users and role assignments.
- Configuring system settings and payment integrations.
- Adding or updating products, categories, and inventory.
- Monitoring supplier purchases and receipts.
- Checking expense entries and payment records.

### Manager Workflow

As a Manager, your main tasks are:

- Running the POS for store sales.
- Managing product and category information.
- Tracking inventory stock levels and adjustments.
- Managing customers, suppliers, and purchase orders.
- Reviewing sales and financial reports.

### Staff Workflow

As Sales Staff, focus on:

- Serving customers at checkout.
- Searching and adding products to the cart.
- Choosing payment methods and printing receipts.
- Looking up customer history and debt balances.
- Completing sales quickly and accurately.

---

## 4. How to Use the Main System Areas

### 4.1 Products and Inventory

Use this area to keep your catalog accurate:

- Add new products and assign them to categories.
- Set prices, stock quantities, and product attributes.
- Edit product details when prices or stock change.
- Monitor inventory levels and perform stock adjustments.

### 4.2 Categories

Categories organize your products by type, department, or brand.

- Create categories for easy browsing.
- Assign each product to a category.
- Use categories to filter inventory and speed up checkout.

### 4.3 Customers

Customer management helps you track loyalty and credit:

- Search for existing customers during checkout.
- Add new customer records with contact details.
- View purchase history and outstanding debt.
- Manage customer balances and payment history.

### 4.4 Suppliers and Purchases

This section supports goods procurement:

- Add or update supplier records.
- Create purchase orders to restock inventory.
- Record incoming stock and store purchase details.
- Track supplier payments and purchase status.

### 4.5 Sales / POS

The sales module is the heart of the system:

- Add products to the sales cart.
- Select a customer or complete a sale without one.
- Choose payment type: cash, card, or KHQR.
- Finalize the sale and print a receipt.
- Review order history and sales details.

### 4.6 Payments and Expenses

Use this area to track money flow:

- Record payments against orders.
- Log business expenses.
- Review reports for profit, loss, and cash flow.

### 4.7 Reports

Reports help you make decisions:

- Sales reports show daily, weekly, or monthly revenue.
- Inventory reports show stock levels and low stock alerts.
- Customer reports show buying behavior.
- Expense reports show business costs.

### 4.8 System Settings

Admins use settings to configure the system:

- Payment gateway credentials for KHQR.
- Email settings for OTP and notifications.
- General system options.
- User approval settings.

---

## 5. User Management and Role Assignments

Admins can manage users here:

- Create new users and assign the correct role.
- Update existing users and change their role if needed.
- Delete inactive or duplicate accounts.
- Approve or reject user registration requests.

> The first Admin user is usually created during installation. If you need a new Admin, use the Admin panel or database seeder to assign role permissions.

---

## 6. Step-by-Step Example: Make a Sale

1. Login with a Staff, Manager, or Admin account.
2. Open the POS page from the sidebar.
3. Search for a product by name or barcode.
4. Add the product to the cart.
5. Enter customer information if available.
6. Choose payment type.
7. Click complete sale.
8. Print or save the receipt.

---

## 7. Step-by-Step Example: Add a Product

1. Login as Admin or Manager.
2. Open the Products section.
3. Click Add Product.
4. Enter product name, category, price, and quantity.
5. Save the product.
6. If needed, open Inventory and create a stock adjustment.

---

## 8. Common Terms

- **POS**: Point of Sale, the checkout screen used to sell products.
- **Inventory**: Stock levels of each product.
- **Category**: A group that organizes products.
- **Supplier**: A vendor who provides inventory.
- **Purchase**: A record of goods bought from a supplier.
- **Role**: The access level assigned to each user.
- **Permission**: A specific action a user can perform.

---

## 9. Important Notes

- If a feature is not visible, your role likely does not allow it.
- Admins should secure the system by keeping user roles correct.
- Always confirm product prices and inventory quantities before finalizing a sale.
- Use the report section daily to monitor sales and stock.

---

## 10. Need Help?

If you are unsure about a task, ask your system Admin. They can create or update user roles, approve access, and configure the system settings.

Thank you for using the POS system. Follow this guide to get started quickly, and let your role guide what you do each day.
