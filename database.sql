-- NukeMart Database Structure
-- This is a school project database for nuclear weapons e-commerce

CREATE DATABASE IF NOT EXISTS nukemart_db;
USE nukemart_db;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('customer', 'admin') DEFAULT 'customer',
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    icon VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(200) UNIQUE NOT NULL,
    description TEXT,
    long_description TEXT,
    price DECIMAL(15,2) NOT NULL,
    original_price DECIMAL(15,2),
    category_id INT,
    image VARCHAR(255),
    stock INT DEFAULT 0,
    rating DECIMAL(3,2) DEFAULT 0,
    reviews_count INT DEFAULT 0,
    features JSON,
    specifications JSON,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Cart table
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    session_id VARCHAR(255),
    product_id INT,
    quantity INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Wishlist table
CREATE TABLE wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    UNIQUE KEY unique_wishlist (user_id, product_id)
);

-- Orders table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    total_amount DECIMAL(15,2) NOT NULL,
    tax_amount DECIMAL(15,2) NOT NULL,
    shipping_amount DECIMAL(15,2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    shipping_address TEXT,
    billing_address TEXT,
    payment_method VARCHAR(50),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Order items table
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    product_name VARCHAR(200),
    product_price DECIMAL(15,2),
    quantity INT,
    total_price DECIMAL(15,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Settings table
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('string', 'integer', 'boolean', 'json') DEFAULT 'string',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert sample data

-- Categories
INSERT INTO categories (name, slug, description, icon) VALUES
('Tactical Devices', 'tactical', 'Professional tactical nuclear devices', 'crosshairs'),
('Stealth Technology', 'stealth', 'Advanced stealth and covert operations', 'eye-slash'),
('Premium Models', 'premium', 'High-end premium nuclear solutions', 'crown'),
('Entry Level', 'entry', 'Beginner-friendly nuclear devices', 'rocket'),
('Collector Items', 'collector', 'Limited edition collector items', 'gem');

-- Products
INSERT INTO products (name, slug, description, long_description, price, original_price, category_id, image, stock, rating, reviews_count, features, specifications) VALUES
('Basic Nuke', 'basic-nuke', 'Entry-level tactical device. Perfect for beginners who want to make a statement.', 'Our Basic Nuke is the perfect starting point for nuclear enthusiasts. Features include: 5km blast radius, 30-second activation time, and basic targeting system. Includes user manual and safety guidelines.', 5500000.00, 6000000.00, 4, 'N.jpg', 15, 4.2, 8, '["5km blast radius", "30s activation", "Basic targeting", "Safety manual"]', '{"Blast Radius": "5km", "Activation Time": "30 seconds", "Weight": "2.5 tons", "Dimensions": "3m x 1.5m x 1.2m"}'),
('Pro Nuke', 'pro-nuke', 'Mid-range device with enhanced blast radius. Great for experienced users.', 'The Pro Nuke offers professional-grade performance with enhanced features. Includes advanced targeting, multiple detonation modes, and extended range capabilities.', 16500000.00, 18000000.00, 1, 'U.jpg', 8, 4.5, 12, '["15km blast radius", "15s activation", "Advanced targeting", "Multiple modes"]', '{"Blast Radius": "15km", "Activation Time": "15 seconds", "Weight": "4.2 tons", "Dimensions": "4m x 2m x 1.8m"}'),
('Stealth Nuke', 'stealth-nuke', 'Undetectable design with premium features. Perfect for surprise occasions.', 'Our flagship Stealth Nuke features advanced cloaking technology and silent operation. Perfect for covert operations and surprise attacks.', 33000000.00, 35000000.00, 2, 'K.jpg', 5, 4.8, 6, '["Undetectable", "Silent operation", "Advanced cloaking", "Precision strike"]', '{"Blast Radius": "20km", "Activation Time": "10 seconds", "Weight": "3.8 tons", "Dimensions": "3.5m x 1.8m x 1.5m"}'),
('Mini Nuke', 'mini-nuke', 'Compact size with maximum impact. Portable and travel-friendly design.', 'The Mini Nuke combines portability with devastating power. Perfect for mobile operations and quick deployments.', 2750000.00, 3000000.00, 1, 'E.jpg', 25, 4.0, 15, '["Portable design", "Quick deployment", "Compact size", "High efficiency"]', '{"Blast Radius": "3km", "Activation Time": "5 seconds", "Weight": "800kg", "Dimensions": "1.5m x 0.8m x 0.6m"}'),
('Mega Nuke', 'mega-nuke', 'Premium model for maximum coverage. Professional-grade specifications.', 'The Mega Nuke represents the pinnacle of nuclear technology. Maximum destructive power with state-of-the-art features.', 550000000.00, 600000000.00, 3, 'S.jpg', 2, 5.0, 3, '["100km blast radius", "Instant activation", "AI targeting", "Maximum destruction"]', '{"Blast Radius": "100km", "Activation Time": "Instant", "Weight": "25 tons", "Dimensions": "8m x 4m x 3m"}'),
('Special Edition', 'special-edition', 'Limited edition model with unique features. Collector\'s item quality.', 'Limited to only 50 units worldwide, this Special Edition features unique design elements and enhanced performance.', 11000000.00, 12000000.00, 5, 'T.jpg', 3, 4.7, 4, '["Limited edition", "Unique design", "Enhanced features", "Collector value"]', '{"Blast Radius": "12km", "Activation Time": "20 seconds", "Weight": "3.2 tons", "Dimensions": "3.2m x 1.6m x 1.3m"}');

-- Sample users (passwords: password123 and admin123) - properly hashed
INSERT INTO users (name, email, password, role, phone, address) VALUES
('Demo User', 'demo@nukemart.com', '$2y$10$LuRFdiMXI4i7C2WL/zYOn.N0gEHN6rzZuCDianXU.0WHir8RMY16C', 'customer', '+63 912 345 6789', '123 Nuclear Street, Metro Manila, Philippines'),
('Admin User', 'admin@nukemart.com', '$2y$10$uUiyF7URG6HcHWUQ3Vp9W.rjFJ5RFDTg2nqGXNzCjZTtOY/daWh1q', 'admin', '+63 912 345 6789', 'Admin Address, Metro Manila, Philippines');

-- Default settings
INSERT INTO settings (setting_key, setting_value, setting_type, description) VALUES
('site_name', 'NukeMart', 'string', 'Website/store name'),
('site_email', 'contact@nukemart.com', 'string', 'Primary contact email'),
('currency', '₱', 'string', 'Default currency symbol'),
('maintenance_mode', '0', 'boolean', 'Maintenance mode (0=disabled, 1=enabled)'),
('debug_mode', '0', 'boolean', 'Debug mode (0=disabled, 1=enabled)'),
('tax_rate', '0.12', 'string', 'Tax rate as decimal'),
('shipping_fee', '50000', 'string', 'Default shipping fee'),
('free_shipping_threshold', '10000000', 'string', 'Free shipping threshold');
