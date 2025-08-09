# NukeMart - Premium Nuclear Solutions

A professional e-commerce platform for premium nuclear solutions and strategic defense systems.

## 🏗️ Project Structure

```
NukeMart/
├── 📁 admin/                    # Admin dashboard
│   ├── 📁 assets/              # Admin-specific assets
│   │   ├── 📁 css/            # Admin stylesheets
│   │   └── 📁 js/             # Admin JavaScript
│   ├── 📁 includes/           # Admin includes
│   │   ├── header.php         # Admin header
│   │   ├── sidebar.php        # Admin sidebar
│   │   └── footer.php         # Admin footer
│   ├── index.php              # Admin dashboard
│   ├── products.php           # Product management
│   ├── orders.php             # Order management
│   ├── categories.php         # Category management
│   ├── customers.php          # Customer management
│   ├── users.php              # User management
│   ├── analytics.php          # Analytics & reports
│   ├── reports.php            # Business reports
│   ├── settings.php           # System settings
│   ├── profile.php            # Admin profile
│   └── backup.php             # Database backup
│
├── 📁 pages/                   # Main application pages
│   ├── 📁 shop/               # Shopping-related pages
│   │   ├── products.php       # All products listing
│   │   ├── product.php        # Individual product details
│   │   ├── cart.php           # Shopping cart
│   │   ├── category.php       # Category pages
│   │   ├── deals.php          # Special deals
│   │   └── new-arrivals.php   # New arrivals
│   ├── 📁 user/               # User-related pages
│   │   ├── about.php          # About us page
│   │   ├── contact.php        # Contact page
│   │   ├── wishlist.php       # User wishlist
│   │   ├── profile.php        # User profile
│   │   └── orders.php         # User orders
│   ├── 📁 auth/               # Authentication pages
│   │   ├── login.php          # User login
│   │   ├── register.php       # User registration
│   │   └── logout.php         # User logout
│   └── 📁 system/             # System pages
│       ├── maintenance.php    # Maintenance page
│       └── install.php        # Installation script
│
├── 📁 includes/                # Shared includes
│   ├── header.php             # Site header
│   ├── navigation.php         # Navigation menu
│   └── footer.php             # Site footer
│
├── 📁 config/                  # Configuration files
│   ├── config.php             # Main configuration
│   └── database.php           # Database connection
│
├── 📁 assets/                  # Public assets
│   ├── 📁 css/                # Stylesheets
│   │   └── styles.css         # Main stylesheet
│   ├── 📁 js/                 # JavaScript files
│   └── 📁 img/                # Images
│       ├── logo.png           # Site logo
│       ├── 📁 nukes/          # Product images
│       └── 📁 flags/          # Flag images
│
├── 📁 ajax/                    # AJAX handlers
│   ├── cart.php               # Cart operations
│   └── wishlist.php           # Wishlist operations
│
├── index.php                   # Home page
├── database.sql               # Database structure
└── README.md                  # This file
```

## 🎯 Key Features

### 🛍️ E-commerce Features
- **Product Catalog**: Complete product management with categories
- **Shopping Cart**: Full cart functionality with real-time updates
- **Wishlist**: User wishlist management
- **User Accounts**: Customer registration and profiles
- **Order Management**: Complete order processing system

### 🔧 Admin Features
- **Dashboard**: Comprehensive admin dashboard with analytics
- **Product Management**: Add, edit, delete products
- **Order Management**: Process and track orders
- **User Management**: Manage customer accounts
- **Analytics**: Sales reports and business insights
- **Settings**: System configuration and maintenance mode

### 🎨 Design Features
- **Modern UI**: Professional dark theme design
- **Responsive**: Mobile-first responsive design
- **User-Friendly**: Intuitive navigation and user experience
- **Professional**: Clean, modern interface

## 🚀 Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/0x3EF8/Nukes.git
   cd Nukes
   ```

2. **Set up the database**
   - **Option 1: Automatic Installation (Recommended)**
     - Access the installation script: `http://localhost/Nukes/pages/system/install.php`
     - The script will automatically create the database and import all tables
     - Follow the on-screen instructions to complete the setup
   
   - **Option 2: Manual Installation**
     - Create a MySQL database named `nukemart_db`
     - Import the `database.sql` file manually
     - Update database credentials in `config/database.php`

3. **Configure the application**
   - Update settings in `config/config.php` if needed
   - Set up your web server to point to the project directory

4. **Access the application**
   - Main site: `http://localhost/Nukes/`
   - Admin panel: `http://localhost/Nukes/admin/`
   - Login: `admin@nukemart.com` / `admin123`

## 🔐 Default Accounts

### Admin Account
- **Email**: `admin@nukemart.com`
- **Password**: `admin123`
- **Role**: Administrator

### Demo Customer Account
- **Email**: `demo@nukemart.com`
- **Password**: `password123`
- **Role**: Customer

## 🛠️ Technology Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript
- **Framework**: Custom PHP framework
- **Styling**: Custom CSS with modern design
- **Icons**: Font Awesome 6.0

## 📊 Database Structure

### Core Tables
- `users` - User accounts and authentication
- `categories` - Product categories
- `products` - Product information and inventory
- `cart` - Shopping cart items
- `wishlist` - User wishlist items
- `orders` - Order management
- `order_items` - Individual order items
- `settings` - System settings and configuration

## 🎨 Design System

### Color Scheme
- **Primary Background**: `#0d0d0d` (Dark)
- **Secondary Background**: `#1a1a1a` (Lighter dark)
- **Accent Color**: `#ffcc00` (Yellow)
- **Text Color**: `#eaeaea` (Light gray)
- **Border Color**: `#333` (Dark gray)

### Typography
- **Primary Font**: System UI, sans-serif
- **Font Sizes**: 14px base, responsive scaling
- **Font Weights**: 400 (normal), 600 (semibold), 800 (bold)

## 🔧 Configuration

### Environment Variables
- `DB_HOST` - Database host
- `DB_NAME` - Database name
- `DB_USER` - Database username
- `DB_PASS` - Database password

### Site Settings
- `SITE_NAME` - Website name
- `SITE_EMAIL` - Contact email
- `CURRENCY` - Default currency
- `TAX_RATE` - Tax rate
- `SHIPPING_FEE` - Shipping fee

## 🚀 Deployment

1. **Upload files** to your web server
2. **Set permissions** for upload directories
3. **Configure database** connection
4. **Run installation** script: `http://localhost/Nukes/pages/system/install.php`
5. **Test functionality** and admin access

## 📝 License

This project is for educational purposes only. Please ensure compliance with all applicable laws and regulations.

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📞 Support

For support and questions:
- **Email**: contact@nukemart.com
- **Documentation**: See inline code comments
- **Issues**: Use [GitHub issues](https://github.com/0x3EF8/Nukes/issues)

---

**NukeMart** - Peace Was Never an Option. 🚀
