<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Admin Dashboard'; ?></title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Admin CSS -->
    <link rel="stylesheet" href="assets/css/admin.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="admin-body">
    <div class="admin-wrapper">
        <!-- Top Navigation -->
        <nav class="admin-nav">
            <div class="nav-left">
                <div class="nav-brand">
                    <img src="../assets/img/logo.png" alt="<?php echo SITE_NAME; ?>" class="nav-logo">
                    <span class="nav-title"><?php echo SITE_NAME; ?> Admin</span>
                </div>
            </div>
            
            <div class="nav-right">
                <div class="nav-search">
                    <input type="text" placeholder="Search..." class="search-input">
                    <i class="fas fa-search search-icon"></i>
                </div>
                
                <div class="nav-notifications">
                    <button class="notification-btn">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">3</span>
                    </button>
                </div>
                
                <div class="nav-profile">
                    <div class="profile-avatar">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div class="profile-info">
                        <span class="profile-name"><?php echo htmlspecialchars($_SESSION['user']['name']); ?></span>
                        <span class="profile-role">Administrator</span>
                    </div>
                    <div class="profile-dropdown">
                        <button class="dropdown-btn">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a href="index.php" class="dropdown-item">
                                <i class="fas fa-tachometer-alt"></i>
                                Dashboard
                            </a>
                            <a href="../index.php" class="dropdown-item">
                                <i class="fas fa-home"></i>
                                Go to Site
                            </a>
                            <a href="profile.php" class="dropdown-item">
                                <i class="fas fa-user"></i>
                                Profile
                            </a>
                            <a href="settings.php" class="dropdown-item">
                                <i class="fas fa-cog"></i>
                                Settings
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="logout.php" class="dropdown-item">
                                <i class="fas fa-sign-out-alt"></i>
                                Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
