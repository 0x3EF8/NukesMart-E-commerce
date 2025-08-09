<aside class="admin-sidebar">
    <nav class="sidebar-nav">
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="index.php" class="nav-link <?php echo ($current_page == 'dashboard') ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a href="products.php" class="nav-link <?php echo ($current_page == 'products') ? 'active' : ''; ?>">
                    <i class="fas fa-box"></i>
                    <span>Products</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a href="categories.php" class="nav-link <?php echo ($current_page == 'categories') ? 'active' : ''; ?>">
                    <i class="fas fa-tags"></i>
                    <span>Categories</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a href="orders.php" class="nav-link <?php echo ($current_page == 'orders') ? 'active' : ''; ?>">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Orders</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a href="customers.php" class="nav-link <?php echo ($current_page == 'customers') ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i>
                    <span>Customers</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a href="analytics.php" class="nav-link <?php echo ($current_page == 'analytics') ? 'active' : ''; ?>">
                    <i class="fas fa-chart-bar"></i>
                    <span>Analytics</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a href="reports.php" class="nav-link <?php echo ($current_page == 'reports') ? 'active' : ''; ?>">
                    <i class="fas fa-file-alt"></i>
                    <span>Reports</span>
                </a>
            </li>
            
            <li class="nav-divider"></li>
            
            <li class="nav-item">
                <a href="settings.php" class="nav-link <?php echo ($current_page == 'settings') ? 'active' : ''; ?>">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a href="users.php" class="nav-link <?php echo ($current_page == 'users') ? 'active' : ''; ?>">
                    <i class="fas fa-user-shield"></i>
                    <span>Users</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a href="backup.php" class="nav-link <?php echo ($current_page == 'backup') ? 'active' : ''; ?>">
                    <i class="fas fa-database"></i>
                    <span>Backup</span>
                </a>
            </li>
        </ul>
    </nav>
    
    <div class="sidebar-footer">
        <div class="sidebar-info">
            <p class="info-text">Need help?</p>
            <a href="support.php" class="support-link">
                <i class="fas fa-question-circle"></i>
                Contact Support
            </a>
        </div>
    </div>
</aside>
