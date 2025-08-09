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

// Get dashboard statistics
$total_products = getTotalProducts();
$total_orders = getTotalOrders();
$total_revenue = getTotalRevenue();
$total_customers = getTotalCustomers();
$recent_orders = getRecentOrders(5);
$top_products = getTopProducts(5);
$monthly_sales = getMonthlySales();
$category_stats = getCategoryStats();

// Debug category stats if empty
if (empty($category_stats)) {
    $category_stats = [
        ['name' => 'No Data', 'sales' => 1]
    ];
}

// Set page variables
$current_page = 'dashboard';
$page_title = 'Admin Dashboard - ' . SITE_NAME;

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
                <h1>Dashboard</h1>
                <p>Welcome back, <?php echo htmlspecialchars($_SESSION['user']['name']); ?>!</p>
            </div>
            <div class="header-right">
                <div class="admin-profile">
                    <div class="admin-avatar">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div class="admin-info">
                        <span class="admin-name"><?php echo htmlspecialchars($_SESSION['user']['name']); ?></span>
                        <span class="admin-role">Administrator</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
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
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo number_format($total_customers); ?></h3>
                    <p>Total Customers</p>
                    <span class="stat-change positive">+5% from last month</span>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="charts-section">
            <div class="chart-container">
                <div class="chart-header">
                    <h3>Sales Analytics</h3>
                    <div class="chart-controls">
                        <select id="sales-period">
                            <option value="7">Last 7 Days</option>
                            <option value="30" selected>Last 30 Days</option>
                            <option value="90">Last 90 Days</option>
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

        <!-- Recent Activity -->
        <div class="activity-section">
            <div class="recent-orders">
                <div class="section-header">
                    <h3>Recent Orders</h3>
                    <a href="orders.php" class="btn-view-all">View All</a>
                </div>
                <div class="table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Products</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_orders as $order): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                <td><?php echo $order['product_count']; ?> items</td>
                                <td><?php echo CURRENCY . number_format($order['total'], 2); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $order['status']; ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="order-details.php?id=<?php echo $order['id']; ?>" class="btn-action" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="edit-order.php?id=<?php echo $order['id']; ?>" class="btn-action" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="top-products">
                <div class="section-header">
                    <h3>Top Products</h3>
                    <a href="products.php" class="btn-view-all">View All</a>
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
                        <div class="product-actions">
                            <a href="edit-product.php?id=<?php echo $product['id']; ?>" class="btn-action" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
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
                '#ff6b6b',
                '#4ecdc4',
                '#45b7d1',
                '#96ceb4',
                '#feca57'
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

// Sales period change
document.getElementById('sales-period').addEventListener('change', function() {
    // Here you would typically make an AJAX call to get new data
    // For now, we'll just log the selected period
    console.log('Selected period:', this.value);
});
</script>

<?php include 'includes/footer.php'; ?>
