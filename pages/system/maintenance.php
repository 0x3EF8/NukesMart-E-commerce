<?php
require_once '../../config/config.php';

// Check if maintenance mode is enabled
if (!isMaintenanceMode()) {
    header('Location: ../../index.php');
    exit();
}

// Allow admin access even during maintenance
if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin') {
    header('Location: ../../admin/index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance - <?php echo getCurrentSiteName(); ?></title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .maintenance-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0d0d0d 0%, #1a1a1a 100%);
            padding: 2rem;
        }
        
        .maintenance-content {
            text-align: center;
            max-width: 600px;
            background: rgba(26, 26, 26, 0.9);
            padding: 3rem;
            border-radius: 15px;
            border: 2px solid #ffcc00;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }
        
        .maintenance-icon {
            font-size: 4rem;
            color: #ffcc00;
            margin-bottom: 1.5rem;
        }
        
        .maintenance-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: #ffcc00;
            margin-bottom: 1rem;
        }
        
        .maintenance-message {
            font-size: 1.2rem;
            color: #eaeaea;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        
        .maintenance-status {
            background: rgba(255, 204, 0, 0.1);
            border: 1px solid #ffcc00;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 2rem;
        }
        
        .status-text {
            color: #ffcc00;
            font-weight: 600;
        }
        
        .admin-login {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #333;
        }
        
        .admin-login a {
            color: #ffcc00;
            text-decoration: none;
            font-weight: 600;
        }
        
        .admin-login a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="maintenance-container">
        <div class="maintenance-content">
            <div class="maintenance-icon">
                <i class="fas fa-tools"></i>
            </div>
            
            <h1 class="maintenance-title">Under Maintenance</h1>
            
            <p class="maintenance-message">
                We're currently performing scheduled maintenance to improve your experience. 
                We'll be back shortly with enhanced features and better performance.
            </p>
            
            <div class="maintenance-status">
                <p class="status-text">
                    <i class="fas fa-clock"></i>
                    Expected completion: Soon
                </p>
            </div>
            
            <div class="admin-login">
                <p style="color: #808080; margin-bottom: 0.5rem;">Administrator?</p>
                <a href="login.php">
                    <i class="fas fa-user-shield"></i>
                    Login to Admin Panel
                </a>
            </div>
        </div>
    </div>
</body>
</html>
