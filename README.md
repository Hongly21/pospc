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
