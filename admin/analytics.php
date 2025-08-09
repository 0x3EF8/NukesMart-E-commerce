<?php
// Include configuration
require_once '../config/config.php';

// Check if user is logged in and is admin
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Get analytics data
$total_revenue = getTotalRevenue();
$total_orders = getTotalOrders();
$total_customers = getTotalCustomers();
$total_products = getTotalProducts();

// Get monthly sales data
$monthly_sales = getMonthlySales();

// Get category performance
$category_stats = getCategoryStats();

// Get top products
$top_products = getTopProducts(10);

// Get recent orders
$recent_orders = getRecentOrders(5);

// Set page variables
$current_page = 'analytics';
$page_title = 'Analytics - ' . SITE_NAME;

// Include admin header
include 'includes/header.php';
?>

<div class="admin-container">
    <!-- Sidebar -->
    <?php include 'includes/sidebar.php'; ?>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="admin-header">
            <div class="header-left">
                <h1>Analytics Dashboard</h1>
                <p>Comprehensive insights into your business performance</p>
            </div>
            <div class="header-right">
                <div class="date-range">
                    <select class="form-control" onchange="updateDateRange(this.value)">
                        <option value="7">Last 7 days</option>
                        <option value="30" selected>Last 30 days</option>
                        <option value="90">Last 90 days</option>
                        <option value="365">Last year</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo CURRENCY . number_format($total_revenue, 2); ?></h3>
                    <p>Total Revenue</p>
                    <span class="stat-change positive">+15% from last month</span>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo number_format($total_orders); ?></h3>
                    <p>Total Orders</p>
                    <span class="stat-change positive">+8% from last month</span>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo number_format($total_customers); ?></h3>
                    <p>Total Customers</p>
                    <span class="stat-change positive">+5% from last month</span>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-box"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo number_format($total_products); ?></h3>
                    <p>Total Products</p>
                    <span class="stat-change positive">+12% from last month</span>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="charts-section">
            <div class="chart-container">
                <div class="chart-header">
                    <h3>Sales Trend</h3>
                    <div class="chart-controls">
                        <select class="form-control" onchange="updateSalesChart(this.value)">
                            <option value="monthly">Monthly</option>
                            <option value="weekly">Weekly</option>
                            <option value="daily">Daily</option>
                        </select>
                    </div>
                </div>
                <div class="chart-content">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
            
            <div class="chart-container">
                <div class="chart-header">
                    <h3>Category Performance</h3>
                </div>
                <div class="chart-content">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Products -->
        <div class="activity-section">
            <div class="section-header">
                <h3>Top Performing Products</h3>
                <a href="products.php" class="btn-view-all">View All Products</a>
            </div>
            <div class="products-list">
                <?php foreach ($top_products as $product): ?>
                <div class="product-item">
                    <div class="product-image">
                        <?php if ($product['image']): ?>
                            <img src="../assets/img/nukes/<?php echo htmlspecialchars($product['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                                 onerror="this.parentElement.innerHTML='<div class=\'no-image-placeholder\'><i class=\'fas fa-image\'></i></div>'">
                        <?php else: ?>
                            <div class="no-image-placeholder">
                                <i class="fas fa-image"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                        <p class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></p>
                        <div class="product-stats">
                            <span class="stat">
                                <i class="fas fa-shopping-cart"></i>
                                <?php echo $product['sales_count']; ?> sales
                            </span>
                            <span class="stat">
                                <i class="fas fa-dollar-sign"></i>
                                <?php echo CURRENCY . number_format($product['revenue'], 2); ?>
                            </span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="activity-section">
            <div class="section-header">
                <h3>Recent Orders</h3>
                <a href="orders.php" class="btn-view-all">View All Orders</a>
            </div>
            <div class="recent-orders">
                <?php foreach ($recent_orders as $order): ?>
                <div class="order-item">
                    <div class="order-info">
                        <h4>Order #<?php echo $order['order_number']; ?></h4>
                        <p class="customer-name"><?php echo htmlspecialchars($order['customer_name']); ?></p>
                        <div class="order-stats">
                            <span class="stat">
                                <i class="fas fa-box"></i>
                                <?php echo $order['product_count']; ?> items
                            </span>
                            <span class="stat">
                                <i class="fas fa-dollar-sign"></i>
                                <?php echo CURRENCY . number_format($order['total_amount'], 2); ?>
                            </span>
                            <span class="stat">
                                <i class="fas fa-clock"></i>
                                <?php echo date('M j, Y', strtotime($order['created_at'])); ?>
                            </span>
                        </div>
                    </div>
                    <div class="order-status">
                        <span class="status-badge status-<?php echo $order['status']; ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Sales Chart
const salesCtx = document.getElementById('salesChart').getContext('2d');
const salesChart = new Chart(salesCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode(array_keys($monthly_sales)); ?>,
        datasets: [{
            label: 'Sales',
            data: <?php echo json_encode(array_values($monthly_sales)); ?>,
            borderColor: '#ffcc00',
            backgroundColor: 'rgba(255, 204, 0, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(255, 255, 255, 0.1)'
                },
                ticks: {
                    color: '#eaeaea'
                }
            },
            x: {
                grid: {
                    color: 'rgba(255, 255, 255, 0.1)'
                },
                ticks: {
                    color: '#eaeaea'
                }
            }
        }
    }
});

// Category Chart
const categoryCtx = document.getElementById('categoryChart').getContext('2d');
const categoryChart = new Chart(categoryCtx, {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode(array_column($category_stats, 'name')); ?>,
        datasets: [{
            data: <?php echo json_encode(array_column($category_stats, 'sales')); ?>,
            backgroundColor: [
                '#ffcc00',
                '#4caf50',
                '#2196f3',
                '#ff9800',
                '#9c27b0'
            ],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    color: '#eaeaea',
                    padding: 20
                }
            }
        }
    }
});

function updateDateRange(range) {
    // Implement date range update logic
    console.log('Date range updated:', range);
}

function updateSalesChart(period) {
    // Implement sales chart update logic
    console.log('Sales chart updated:', period);
}
</script>

<?php include 'includes/footer.php'; ?>
