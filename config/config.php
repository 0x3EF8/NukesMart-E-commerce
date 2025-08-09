<?php
// Include database connection
require_once 'database.php';

// Site Configuration - Default values
define('SITE_NAME', 'NukeMart');
define('SITE_TAGLINE', 'Peace Was Never an Option.');
define('SITE_OWNER', '- Cano Jay Patrick (Nuclear Entrepreneur Extraordinaire)');
define('SITE_EMAIL', 'contact@nukemart.com');
define('SITE_YEAR', '2025');

// Base URL Configuration
define('BASE_URL', './');
define('ASSETS_URL', BASE_URL . 'assets/');
define('CSS_URL', ASSETS_URL . 'css/');
define('IMG_URL', ASSETS_URL . 'img/');

// E-commerce Configuration - Default values
define('CURRENCY', '₱');
define('TAX_RATE', 0.12); // 12% tax
define('SHIPPING_FEE', 50000); // ₱50,000 shipping
define('FREE_SHIPPING_THRESHOLD', 10000000); // ₱10M for free shipping

// Load settings from database if available
function loadSettingsFromDatabase() {
    try {
        $settings = [];
        $sql = "SELECT setting_key, setting_value, setting_type FROM settings";
        $results = fetchAll($sql);
        
        foreach ($results as $result) {
            $key = $result['setting_key'];
            $value = $result['setting_value'];
            $type = $result['setting_type'];
            
            switch ($type) {
                case 'boolean':
                    $settings[$key] = (bool)$value;
                    break;
                case 'integer':
                    $settings[$key] = (int)$value;
                    break;
                case 'json':
                    $settings[$key] = json_decode($value, true);
                    break;
                default:
                    $settings[$key] = $value;
            }
        }
        
        return $settings;
    } catch (Exception $e) {
        // If database is not available, return empty array
        return [];
    }
}

// Function to get current site name (with database override)
function getCurrentSiteName() {
    static $site_name = null;
    if ($site_name === null) {
        $db_settings = loadSettingsFromDatabase();
        $site_name = $db_settings['site_name'] ?? SITE_NAME;
    }
    return $site_name;
}

// Function to get current site email (with database override)
function getCurrentSiteEmail() {
    static $site_email = null;
    if ($site_email === null) {
        $db_settings = loadSettingsFromDatabase();
        $site_email = $db_settings['site_email'] ?? SITE_EMAIL;
    }
    return $site_email;
}

// Function to get current currency (with database override)
function getCurrentCurrency() {
    static $currency = null;
    if ($currency === null) {
        $db_settings = loadSettingsFromDatabase();
        $currency = $db_settings['currency'] ?? CURRENCY;
    }
    return $currency;
}

// Function to check if maintenance mode is enabled
function isMaintenanceMode() {
    static $maintenance_mode = null;
    if ($maintenance_mode === null) {
        $db_settings = loadSettingsFromDatabase();
        $maintenance_mode = $db_settings['maintenance_mode'] ?? false;
    }
    return $maintenance_mode;
}

// Function to check if debug mode is enabled
function isDebugMode() {
    static $debug_mode = null;
    if ($debug_mode === null) {
        $db_settings = loadSettingsFromDatabase();
        $debug_mode = $db_settings['debug_mode'] ?? false;
    }
    return $debug_mode;
}

// Function to get the correct base path for assets
function getBasePath() {
    static $base_path = null;
    if ($base_path === null) {
        $current_file = $_SERVER['SCRIPT_NAME'] ?? '';
        if (strpos($current_file, '/pages/') !== false) {
            $base_path = '../../';
        } elseif (strpos($current_file, '/admin/') !== false) {
            $base_path = '../';
        } else {
            $base_path = './';
        }
    }
    return $base_path;
}

// Function to get the correct image URL
function getImageUrl($path = '') {
    return getBasePath() . 'assets/img/' . $path;
}

// Function to get the correct CSS URL
function getCssUrl($path = '') {
    return getBasePath() . 'assets/css/' . $path;
}

// Function to get the correct JS URL
function getJsUrl($path = '') {
    return getBasePath() . 'assets/js/' . $path;
}

// Flag Configuration
$flags = [
    'us' => [
        'name' => 'United States',
        'image' => 'us.png',
        'description' => 'Government agencies trust NukeMart for strategic defense solutions',
        'rating' => 5.0,
        'testimonial' => '"NukeMart delivered exceptional quality. Our defense systems have never been more reliable."',
        'customer' => 'Department of Defense'
    ],
    'jp' => [
        'name' => 'Japan',
        'image' => 'jp.png',
        'description' => 'Advanced technology partnerships with leading defense institutions',
        'rating' => 5.0,
        'testimonial' => '"Cutting-edge technology and professional service. Highly recommended for strategic operations."',
        'customer' => 'Japan Self-Defense Forces'
    ],
    'ph' => [
        'name' => 'Philippines',
        'image' => 'ph.png',
        'description' => 'Strategic defense cooperation with military organizations',
        'rating' => 5.0,
        'testimonial' => '"NukeMart\'s precision and reliability exceeded our expectations. Outstanding partnership."',
        'customer' => 'Armed Forces of the Philippines'
    ],
    'gb' => [
        'name' => 'United Kingdom',
        'image' => 'gb.png',
        'description' => 'Royal defense forces rely on our premium nuclear solutions',
        'rating' => 5.0,
        'testimonial' => '"Superior quality and unmatched expertise. NukeMart is our trusted defense partner."',
        'customer' => 'British Armed Forces'
    ],
    'de' => [
        'name' => 'Germany',
        'image' => 'de.png',
        'description' => 'Bundeswehr trusts NukeMart for advanced tactical systems',
        'rating' => 5.0,
        'testimonial' => '"Professional service and innovative solutions. NukeMart sets the industry standard."',
        'customer' => 'Bundeswehr'
    ],
    'fr' => [
        'name' => 'France',
        'image' => 'fr.png',
        'description' => 'French military institutions choose our cutting-edge technology',
        'rating' => 5.0,
        'testimonial' => '"Excellence in every aspect. NukeMart provides world-class defense technology."',
        'customer' => 'French Armed Forces'
    ]
];

// Page Configuration
$site_pages = [
    'home' => [
        'title' => SITE_NAME . ' - Premium Nuclear Solutions',
        'file' => 'index.php'
    ],
    'about' => [
        'title' => 'About - ' . SITE_NAME,
        'file' => 'about.php'
    ],
    'contact' => [
        'title' => 'Contact - ' . SITE_NAME,
        'file' => 'contact.php'
    ],
    'cart' => [
        'title' => 'Shopping Cart - ' . SITE_NAME,
        'file' => 'cart.php'
    ],
    'checkout' => [
        'title' => 'Checkout - ' . SITE_NAME,
        'file' => 'checkout.php'
    ],
    'login' => [
        'title' => 'Login - ' . SITE_NAME,
        'file' => 'login.php'
    ],
    'register' => [
        'title' => 'Register - ' . SITE_NAME,
        'file' => 'register.php'
    ],
    'profile' => [
        'title' => 'My Profile - ' . SITE_NAME,
        'file' => 'profile.php'
    ],
    'orders' => [
        'title' => 'My Orders - ' . SITE_NAME,
        'file' => 'orders.php'
    ],
    'wishlist' => [
        'title' => 'My Wishlist - ' . SITE_NAME,
        'file' => 'wishlist.php'
    ],
    'products' => [
        'title' => 'All Products - ' . SITE_NAME,
        'file' => 'products.php'
    ]
];

// Helper functions
function formatPrice($price) {
    return getCurrentCurrency() . number_format($price, 0, '.', ',');
}

function getProductById($id) {
    $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.id = ? AND p.is_active = 1";
    return fetchOne($sql, [$id]);
}

function getProductsByCategory($category_slug) {
    $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE c.slug = ? AND p.is_active = 1 
            ORDER BY p.created_at DESC";
    return fetchAll($sql, [$category_slug]);
}

function getAllProducts() {
    $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.is_active = 1 
            ORDER BY p.created_at DESC";
    return fetchAll($sql);
}

function getAllCategories() {
    $sql = "SELECT * FROM categories ORDER BY name";
    return fetchAll($sql);
}

function getCategoryBySlug($slug) {
    $sql = "SELECT * FROM categories WHERE slug = ?";
    return fetchOne($sql, [$slug]);
}

function getUserById($id) {
    $sql = "SELECT * FROM users WHERE id = ?";
    return fetchOne($sql, [$id]);
}

function getUserByEmail($email) {
    $sql = "SELECT * FROM users WHERE email = ?";
    return fetchOne($sql, [$email]);
}

function getCartItems($user_id = null, $session_id = null) {
    if ($user_id) {
        $sql = "SELECT c.*, p.name, p.price, p.image, p.stock 
                FROM cart c 
                JOIN products p ON c.product_id = p.id 
                WHERE c.user_id = ?";
        return fetchAll($sql, [$user_id]);
    } elseif ($session_id) {
        $sql = "SELECT c.*, p.name, p.price, p.image, p.stock 
                FROM cart c 
                JOIN products p ON c.product_id = p.id 
                WHERE c.session_id = ?";
        return fetchAll($sql, [$session_id]);
    }
    return [];
}

function getWishlistItems($user_id) {
    $sql = "SELECT w.*, p.name, p.price, p.original_price, p.image, p.stock, p.rating, p.reviews_count 
            FROM wishlist w 
            JOIN products p ON w.product_id = p.id 
            WHERE w.user_id = ?";
    return fetchAll($sql, [$user_id]);
}

function isInWishlist($user_id, $product_id) {
    $sql = "SELECT COUNT(*) as count FROM wishlist WHERE user_id = ? AND product_id = ?";
    $result = fetchOne($sql, [$user_id, $product_id]);
    return $result['count'] > 0;
}

function getCartCount($user_id = null, $session_id = null) {
    if ($user_id) {
        $sql = "SELECT SUM(quantity) as count FROM cart WHERE user_id = ?";
        $result = fetchOne($sql, [$user_id]);
    } elseif ($session_id) {
        $sql = "SELECT SUM(quantity) as count FROM cart WHERE session_id = ?";
        $result = fetchOne($sql, [$session_id]);
    } else {
        return 0;
    }
    return $result['count'] ?? 0;
}

function getWishlistCount($user_id) {
    $sql = "SELECT COUNT(*) as count FROM wishlist WHERE user_id = ?";
    $result = fetchOne($sql, [$user_id]);
    return $result['count'] ?? 0;
}

// Session management
session_start();

// Generate session ID if not exists
if (!isset($_SESSION['session_id'])) {
    $_SESSION['session_id'] = uniqid();
}

// Home Page Content
$home_content = [
    'hero' => [
        'title' => 'Premium Nuclear Solutions',
        'subtitle' => 'Discover our exclusive collection of tactical devices',
        'button_text' => 'Shop Now',
        'button_link' => '#products',
        'video_src' => 'assets/video/bg.mp4'
    ],
    'flag_banner' => [
        'title' => 'Satisfied Customers Worldwide',
        'subtitle' => 'Trusted by government agencies and defense organizations across the globe'
    ],
    'about_section' => [
        'title' => 'Why Choose NukeMart?',
        'description' => 'As the industry leader in advanced nuclear technology, NukeMart delivers cutting-edge solutions with uncompromising quality and precision engineering. Our commitment to excellence, innovation, and customer satisfaction distinguishes us as the premier choice for strategic defense systems.',
        'button_text' => 'Learn More About Us',
        'button_link' => 'about.php'
    ],
    'features' => [
        'left_column' => [
            [
                'icon' => 'fas fa-award',
                'title' => 'Industry Excellence',
                'description' => 'Certified to meet the highest international standards and regulatory requirements'
            ],
            [
                'icon' => 'fas fa-rocket',
                'title' => 'Advanced Technology',
                'description' => 'State-of-the-art systems with precision engineering and cutting-edge innovation'
            ],
            [
                'icon' => 'fas fa-user-shield',
                'title' => 'Expert Consultation',
                'description' => 'Specialized technical support and strategic planning from industry professionals'
            ]
        ],
        'right_column' => [
            [
                'icon' => 'fas fa-globe',
                'title' => 'Global Reach',
                'description' => 'Worldwide deployment capabilities with strategic partnerships across 50+ countries'
            ],
            [
                'icon' => 'fas fa-clock',
                'title' => '24/7 Support',
                'description' => 'Round-the-clock technical assistance and emergency response capabilities'
            ],
            [
                'icon' => 'fas fa-shield-alt',
                'title' => 'Security First',
                'description' => 'Top-level security protocols and confidential handling of all operations'
            ]
        ]
    ],
    'stats' => [
        [
            'number' => '25+',
            'label' => 'Years Experience'
        ],
        [
            'number' => '50+',
            'label' => 'Countries Served'
        ],
        [
            'number' => '1000+',
            'label' => 'Successful Deployments'
        ],
        [
            'number' => '99.9%',
            'label' => 'Success Rate'
        ]
    ],
    'what_we_offer' => [
        'title' => 'What We Offer',
        'subtitle' => 'Comprehensive nuclear solutions for strategic defense requirements',
        'offerings' => [
            [
                'icon' => 'fas fa-rocket',
                'title' => 'Tactical Devices',
                'description' => 'Advanced tactical nuclear devices designed for precision strikes and strategic operations. Our tactical solutions provide unmatched accuracy and reliability.',
                'features' => [
                    'Precision targeting systems',
                    'Advanced detonation technology',
                    'Portable deployment options'
                ]
            ],
            [
                'icon' => 'fas fa-eye-slash',
                'title' => 'Stealth Technology',
                'description' => 'Undetectable nuclear solutions with advanced cloaking capabilities. Perfect for covert operations and surprise strategic deployments.',
                'features' => [
                    'Advanced cloaking systems',
                    'Silent operation technology',
                    'Radar evasion capabilities'
                ]
            ],
            [
                'icon' => 'fas fa-crown',
                'title' => 'Premium Models',
                'description' => 'High-end nuclear devices with cutting-edge technology and maximum destructive power. Our premium models represent the pinnacle of nuclear engineering.',
                'features' => [
                    'Maximum destructive power',
                    'State-of-the-art technology',
                    'Extended range capabilities'
                ]
            ],
            [
                'icon' => 'fas fa-shield-alt',
                'title' => 'Defense Systems',
                'description' => 'Comprehensive nuclear defense systems for strategic protection and deterrence. Our defense solutions ensure national security and strategic superiority.',
                'features' => [
                    'Strategic defense networks',
                    'Deterrence capabilities',
                    'Protection systems'
                ]
            ],
            [
                'icon' => 'fas fa-cogs',
                'title' => 'Custom Solutions',
                'description' => 'Tailored nuclear solutions designed to meet specific strategic requirements. Our custom engineering ensures perfect fit for unique operational needs.',
                'features' => [
                    'Custom engineering',
                    'Specialized modifications',
                    'Bespoke configurations'
                ]
            ],
            [
                'icon' => 'fas fa-headset',
                'title' => 'Expert Support',
                'description' => 'Comprehensive technical support and strategic consultation services. Our expert team provides guidance for optimal deployment and operation.',
                'features' => [
                    '24/7 technical support',
                    'Strategic consultation',
                    'Training programs'
                ]
            ]
        ]
    ],
    'cta_section' => [
        'title' => 'Ready to Enhance Your Strategic Capabilities?',
        'description' => 'Join government agencies and defense organizations worldwide who trust NukeMart for their advanced technology requirements',
        'buttons' => [
            [
                'text' => 'Browse Products',
                'link' => 'products.php',
                'class' => 'btn-primary'
            ],
            [
                'text' => 'Create Account',
                'link' => 'register.php',
                'class' => 'btn-secondary'
            ]
        ]
    ]
];

// About Page Content
$about_content = [
    'page_header' => [
        'title' => 'About ' . SITE_NAME,
        'subtitle' => 'Leading the future of strategic defense technology with innovation, precision, and unwavering commitment to excellence'
    ],
    'hero_section' => [
        'title' => 'Strategic Defense Excellence',
        'description' => 'Since our establishment, ' . SITE_NAME . ' has been at the forefront of nuclear technology innovation, serving government agencies and defense organizations worldwide with cutting-edge solutions that redefine strategic capabilities.',
        'stats' => [
            [
                'number' => '15+',
                'label' => 'Years Experience'
            ],
            [
                'number' => '50+',
                'label' => 'Countries Served'
            ],
            [
                'number' => '1000+',
                'label' => 'Successful Deployments'
            ]
        ]
    ],
    'mission_vision' => [
        'mission' => [
            'icon' => 'fas fa-bullseye',
            'title' => 'Our Mission',
            'description' => 'To provide world-class nuclear defense solutions that ensure strategic superiority and national security for our global partners, while maintaining the highest standards of safety, reliability, and technological innovation.'
        ],
        'vision' => [
            'icon' => 'fas fa-eye',
            'title' => 'Our Vision',
            'description' => 'To be the premier global leader in advanced nuclear technology, setting industry standards for excellence, innovation, and strategic defense capabilities that protect nations and secure the future.'
        ]
    ],
    'core_values' => [
        'title' => 'Core Values',
        'subtitle' => 'The principles that guide our every decision and action',
        'values' => [
            [
                'icon' => 'fas fa-shield-alt',
                'title' => 'Excellence',
                'description' => 'Uncompromising commitment to quality and precision in every product and service we deliver.'
            ],
            [
                'icon' => 'fas fa-lightbulb',
                'title' => 'Innovation',
                'description' => 'Pioneering cutting-edge technology and breakthrough solutions that advance strategic capabilities.'
            ],
            [
                'icon' => 'fas fa-handshake',
                'title' => 'Integrity',
                'description' => 'Maintaining the highest ethical standards and transparent relationships with all stakeholders.'
            ],
            [
                'icon' => 'fas fa-globe',
                'title' => 'Global Reach',
                'description' => 'Serving defense organizations worldwide with reliable, secure, and effective solutions.'
            ],
            [
                'icon' => 'fas fa-users',
                'title' => 'Partnership',
                'description' => 'Building lasting relationships based on trust, collaboration, and mutual success.'
            ],
            [
                'icon' => 'fas fa-rocket',
                'title' => 'Performance',
                'description' => 'Delivering superior results that exceed expectations and drive strategic advantage.'
            ]
        ]
    ],
    'technology_section' => [
        'title' => 'Advanced Technology & Innovation',
        'description' => 'Our research and development team works tirelessly to push the boundaries of nuclear technology, creating solutions that provide strategic advantages in an ever-evolving global landscape.',
        'features' => [
            [
                'icon' => 'fas fa-microchip',
                'title' => 'Precision Engineering',
                'description' => 'State-of-the-art manufacturing processes ensuring unmatched accuracy and reliability.'
            ],
            [
                'icon' => 'fas fa-satellite',
                'title' => 'Advanced Targeting',
                'description' => 'Cutting-edge guidance systems with pinpoint accuracy and real-time tracking capabilities.'
            ],
            [
                'icon' => 'fas fa-lock',
                'title' => 'Security Protocols',
                'description' => 'Multi-layered security systems ensuring complete protection and operational safety.'
            ]
        ]
    ],
    'global_presence' => [
        'title' => 'Global Presence',
        'subtitle' => 'Trusted by defense organizations across the world',
        'stats' => [
            [
                'number' => '50+',
                'title' => 'Countries',
                'description' => 'Strategic partnerships worldwide'
            ],
            [
                'number' => '24/7',
                'title' => 'Support',
                'description' => 'Round-the-clock technical assistance'
            ],
            [
                'number' => '99.9%',
                'title' => 'Reliability',
                'description' => 'Proven track record of excellence'
            ],
            [
                'number' => '15+',
                'title' => 'Years',
                'description' => 'Industry leadership and expertise'
            ]
        ]
    ],
    'cta_section' => [
        'title' => 'Ready to Experience Strategic Excellence?',
        'description' => 'Join the ranks of world-class defense organizations that trust ' . SITE_NAME . ' for their strategic needs.',
        'buttons' => [
            [
                'text' => 'Explore Products',
                'link' => 'products.php',
                'class' => 'btn-primary'
            ],
            [
                'text' => 'Contact Us',
                'link' => 'contact.php',
                'class' => 'btn-secondary'
            ]
        ]
    ]
];

// Contact Page Content
$contact_content = [
    'page_header' => [
        'title' => 'Contact Us',
        'subtitle' => 'Get in touch with our strategic defense experts'
    ],
    'contact_form' => [
        'title' => 'Send Message',
        'subtitle' => 'Our team will respond within 24 hours',
        'form_fields' => [
            'name' => 'Full Name *',
            'email' => 'Email *',
            'organization' => 'Organization',
            'phone' => 'Phone',
            'subject' => 'Subject *',
            'message' => 'Message *',
            'confidential' => 'I agree to secure communication terms *'
        ],
        'subject_options' => [
            'product-inquiry' => 'Product Inquiry',
            'technical-support' => 'Technical Support',
            'partnership' => 'Partnership',
            'consultation' => 'Consultation',
            'training' => 'Training',
            'other' => 'Other'
        ],
        'submit_text' => 'Send Message'
    ],
    'contact_info' => [
        'title' => 'Contact Information',
        'subtitle' => 'Multiple ways to reach our team',
        'cards' => [
            [
                'icon' => 'fas fa-map-marker-alt',
                'title' => 'Headquarters',
                'content' => 'Defense Technology Complex<br>Secure Location'
            ],
            [
                'icon' => 'fas fa-phone',
                'title' => 'Phone',
                'content' => '+63 00000000<br>Emergency: +63 00000000'
            ],
            [
                'icon' => 'fas fa-envelope',
                'title' => 'Email',
                'content' => SITE_EMAIL . '<br>support@nukemart.com'
            ],
            [
                'icon' => 'fas fa-clock',
                'title' => 'Hours',
                'content' => '24/7 Support<br>Global Operations'
            ]
        ]
    ],
    'emergency_contact' => [
        'title' => 'Emergency Contact',
        'description' => 'For urgent matters requiring immediate attention:',
        'items' => [
            [
                'icon' => 'fas fa-exclamation-triangle',
                'text' => 'Emergency: +63 00000000'
            ],
            [
                'icon' => 'fas fa-shield-alt',
                'text' => '24/7 Secure Line'
            ]
        ]
    ]
];

// Admin Dashboard Functions
function getTotalProducts() {
    $sql = "SELECT COUNT(*) as count FROM products WHERE is_active = 1";
    $result = fetchOne($sql);
    return $result['count'] ?? 0;
}

function getTotalOrders() {
    $sql = "SELECT COUNT(*) as count FROM orders";
    $result = fetchOne($sql);
    return $result['count'] ?? 0;
}

function getTotalRevenue() {
    $sql = "SELECT SUM(total_amount) as total FROM orders WHERE status != 'cancelled'";
    $result = fetchOne($sql);
    return $result['total'] ?? 0;
}

function getTotalCustomers() {
    $sql = "SELECT COUNT(*) as count FROM users WHERE role = 'customer'";
    $result = fetchOne($sql);
    return $result['count'] ?? 0;
}

function getRecentOrders($limit = 5) {
    $sql = "SELECT o.*, u.name as customer_name, 
            COALESCE((SELECT COUNT(*) FROM order_items WHERE order_id = o.id), 0) as product_count
            FROM orders o 
            LEFT JOIN users u ON o.user_id = u.id 
            ORDER BY o.created_at DESC 
            LIMIT ?";
    return fetchAll($sql, [$limit]);
}

function getTopProducts($limit = 5) {
    $sql = "SELECT p.*, c.name as category_name,
            COALESCE((SELECT COUNT(*) FROM order_items oi 
             JOIN orders o ON oi.order_id = o.id 
             WHERE oi.product_id = p.id AND o.status != 'cancelled'), 0) as sales_count,
            COALESCE((SELECT SUM(oi.quantity * oi.product_price) FROM order_items oi 
             JOIN orders o ON oi.order_id = o.id 
             WHERE oi.product_id = p.id AND o.status != 'cancelled'), 0) as revenue
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.is_active = 1 
            ORDER BY sales_count DESC, p.name ASC 
            LIMIT ?";
    return fetchAll($sql, [$limit]);
}

function getMonthlySales() {
    $sql = "SELECT DATE_FORMAT(created_at, '%M') as month, 
            SUM(total_amount) as total 
            FROM orders 
            WHERE status != 'cancelled' 
            AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m') 
            ORDER BY created_at";
    $results = fetchAll($sql);
    
    $sales = [];
    foreach ($results as $result) {
        $sales[$result['month']] = $result['total'];
    }
    
    return $sales;
}

function getCategoryStats() {
    $sql = "SELECT c.name, 
            COALESCE((SELECT COUNT(*) FROM order_items oi 
             JOIN orders o ON oi.order_id = o.id 
             JOIN products p ON oi.product_id = p.id 
             WHERE p.category_id = c.id AND o.status != 'cancelled'), 0) as sales
            FROM categories c 
            ORDER BY sales DESC";
    return fetchAll($sql);
}

// Settings Functions
function getSetting($key, $default = null) {
    $sql = "SELECT setting_value, setting_type FROM settings WHERE setting_key = ?";
    $result = fetchOne($sql, [$key]);
    
    if (!$result) {
        return $default;
    }
    
    switch ($result['setting_type']) {
        case 'boolean':
            return (bool)$result['setting_value'];
        case 'integer':
            return (int)$result['setting_value'];
        case 'json':
            return json_decode($result['setting_value'], true);
        default:
            return $result['setting_value'];
    }
}

function setSetting($key, $value, $type = 'string') {
    $sql = "INSERT INTO settings (setting_key, setting_value, setting_type) 
            VALUES (?, ?, ?) 
            ON DUPLICATE KEY UPDATE 
            setting_value = VALUES(setting_value), 
            setting_type = VALUES(setting_type),
            updated_at = CURRENT_TIMESTAMP";
    
    return executeQuery($sql, [$key, $value, $type]);
}

function getAllSettings() {
    $sql = "SELECT * FROM settings ORDER BY setting_key";
    return fetchAll($sql);
}

function updateSettings($settings) {
    $success = true;
    foreach ($settings as $key => $value) {
        if (is_array($value)) {
            $value = json_encode($value);
            $type = 'json';
        } elseif (is_bool($value)) {
            $value = $value ? '1' : '0';
            $type = 'boolean';
        } elseif (is_int($value)) {
            $type = 'integer';
        } else {
            $type = 'string';
        }
        
        if (!setSetting($key, $value, $type)) {
            $success = false;
        }
    }
    return $success;
}
?> 