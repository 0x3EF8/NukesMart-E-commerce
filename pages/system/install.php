<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');

// Start the installation process
try {
    // First, try to connect to the specific database
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=nukemart_db;charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        
        $connected_to_existing = true;
        
        // Check if database has tables
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (empty($tables)) {
            throw new Exception("Database exists but has no tables");
        }
        
    } catch (Exception $e) {
        $connected_to_existing = false;
        // Database doesn't exist or has no tables, create it
        $create_database = true;
        
        // Connect to MySQL without specifying database
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        
        // Create database
        $pdo->exec("CREATE DATABASE IF NOT EXISTS nukemart_db");
        
        // Select the database
        $pdo->exec("USE nukemart_db");
        
        // Read and execute the SQL file
        $sql = file_get_contents('../../database.sql');
        
        // Split the SQL into individual statements
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        foreach ($statements as $statement) {
            if (!empty($statement) && !preg_match('/^(CREATE DATABASE|USE)/i', $statement)) {
                try {
                    $pdo->exec($statement);
                } catch (PDOException $e) {
                    // Skip errors for duplicate entries or existing tables
                    if (!strpos($e->getMessage(), 'Duplicate entry') && !strpos($e->getMessage(), 'already exists')) {
                        $warnings[] = $e->getMessage();
                    }
                }
            }
        }
        
        // Verify the setup
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    // Check if products exist
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM products");
    $product_count = $stmt->fetch()['count'] ?? 0;
    
    // Check if categories exist
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM categories");
    $category_count = $stmt->fetch()['count'] ?? 0;
    
    $installation_success = true;
    
} catch (Exception $e) {
    $installation_success = false;
    $error_message = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NukeMart Installation - Nuclear Deployment System</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .install-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #0d0d0d 0%, #1a1a1a 100%);
            color: #eaeaea;
            font-family: system-ui, sans-serif;
        }
        
        .install-header {
            text-align: center;
            padding: 2rem 0;
            background: rgba(255, 204, 0, 0.1);
            border-bottom: 2px solid #ffcc00;
        }
        
        .install-header h1 {
            font-size: 3rem;
            margin: 0;
            color: #ffcc00;
            text-shadow: 0 0 10px rgba(255, 204, 0, 0.5);
        }
        
        .install-header .tagline {
            font-size: 1.2rem;
            color: #ccc;
            margin-top: 0.5rem;
        }
        
        .install-content {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .status-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid #333;
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1rem 0;
            backdrop-filter: blur(10px);
        }
        
        .status-item {
            display: flex;
            align-items: center;
            margin: 0.5rem 0;
            padding: 0.5rem;
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.02);
        }
        
        .status-icon {
            margin-right: 1rem;
            font-size: 1.2rem;
            width: 20px;
            text-align: center;
        }
        
        .status-success { color: #4CAF50; }
        .status-warning { color: #FF9800; }
        .status-error { color: #f44336; }
        
        .deployment-status {
            text-align: center;
            margin: 2rem 0;
            padding: 2rem;
            background: rgba(255, 204, 0, 0.1);
            border: 2px solid #ffcc00;
            border-radius: 15px;
        }
        
        .deployment-status h2 {
            color: #ffcc00;
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        
        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin: 2rem 0;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .btn-primary {
            background: #ffcc00;
            color: #0d0d0d;
        }
        
        .btn-primary:hover {
            background: #e6b800;
            transform: translateY(-2px);
        }
        
        .btn-success {
            background: #4CAF50;
            color: white;
        }
        
        .btn-success:hover {
            background: #45a049;
            transform: translateY(-2px);
        }
        
        .nuclear-animation {
            text-align: center;
            margin: 2rem 0;
        }
        
        .nuclear-animation i {
            font-size: 4rem;
            color: #ffcc00;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        .deployment-ready {
            background: rgba(76, 175, 80, 0.1);
            border: 2px solid #4CAF50;
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            margin: 2rem 0;
        }
        
        .deployment-ready h2 {
            color: #4CAF50;
            font-size: 2rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="install-container">
        <div class="install-header">
            <h1><i class="fas fa-radiation"></i> NukeMart</h1>
            <div class="tagline">Nuclear Deployment System</div>
        </div>
        
        <div class="install-content">
            <div class="nuclear-animation">
                <i class="fas fa-rocket"></i>
            </div>
            
            <div class="status-card">
                <h2><i class="fas fa-cogs"></i> System Deployment</h2>
                
                <?php if ($installation_success): ?>
                    <?php if (isset($connected_to_existing) && $connected_to_existing): ?>
                        <div class="status-item">
                            <i class="fas fa-check status-icon status-success"></i> 
                            Connected to existing database
                        </div>
                        <div class="status-item">
                            <i class="fas fa-check status-icon status-success"></i> 
                            Database tables found: <?php echo implode(', ', $tables); ?>
                        </div>
                    <?php else: ?>
                        <div class="status-item">
                            <i class="fas fa-exclamation-triangle status-icon status-warning"></i> 
                            Database not found or empty. Creating database...
                        </div>
                        <div class="status-item">
                            <i class="fas fa-check status-icon status-success"></i> 
                            Connected to MySQL server
                        </div>
                        <div class="status-item">
                            <i class="fas fa-check status-icon status-success"></i> 
                            Database 'nukemart_db' created
                        </div>
                        <div class="status-item">
                            <i class="fas fa-check status-icon status-success"></i> 
                            Database selected
                        </div>
                        <?php if (isset($warnings)): ?>
                            <?php foreach ($warnings as $warning): ?>
                                <div class="status-item">
                                    <i class="fas fa-exclamation-triangle status-icon status-warning"></i> 
                                    Warning: <?php echo htmlspecialchars($warning); ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <div class="status-item">
                            <i class="fas fa-check status-icon status-success"></i> 
                            Database tables and data imported
                        </div>
                        <div class="status-item">
                            <i class="fas fa-check status-icon status-success"></i> 
                            Tables created: <?php echo implode(', ', $tables); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="status-item">
                        <i class="fas fa-check status-icon status-success"></i> 
                        Database tables found: <?php echo implode(', ', $tables); ?>
                    </div>
                    
                    <?php if ($product_count > 0): ?>
                        <div class="status-item">
                            <i class="fas fa-check status-icon status-success"></i> 
                            Products found: <?php echo $product_count; ?>
                        </div>
                    <?php else: ?>
                        <div class="status-item">
                            <i class="fas fa-exclamation-triangle status-icon status-warning"></i> 
                            No products found! Please import sample data.
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($category_count > 0): ?>
                        <div class="status-item">
                            <i class="fas fa-check status-icon status-success"></i> 
                            Categories found: <?php echo $category_count; ?>
                        </div>
                    <?php else: ?>
                        <div class="status-item">
                            <i class="fas fa-exclamation-triangle status-icon status-warning"></i> 
                            No categories found! Please import sample data.
                        </div>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <div class="status-item">
                        <i class="fas fa-times status-icon status-error"></i> 
                        Installation failed: <?php echo htmlspecialchars($error_message); ?>
                    </div>
                    <p>Please check your database configuration in config/database.php</p>
                <?php endif; ?>
            </div>
            
            <?php if ($installation_success): ?>
                <div class="deployment-ready">
                    <h2><i class="fas fa-rocket"></i> DEPLOYMENT READY!</h2>
                    <p>Your NukeMart nuclear deployment system is now operational.</p>
                </div>
                
                <div class="action-buttons">
                    <a href="../../pages/auth/login.php" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i> Launch Login
                    </a>
                    <a href="../../index.php" class="btn btn-success">
                        <i class="fas fa-home"></i> Go to Homepage
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
