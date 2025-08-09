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

// Handle order actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_status':
                if (isset($_POST['order_id']) && isset($_POST['status'])) {
                    $order_id = $_POST['order_id'];
                    $status = $_POST['status'];
                    $sql = "UPDATE orders SET status = ? WHERE id = ?";
                    executeQuery($sql, [$status, $order_id]);
                    $message = "Order status updated successfully!";
                    $message_type = "success";
                }
                break;
        }
    }
}

// Get orders with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build query
$where_conditions = ["1=1"];
$params = [];

if ($status_filter) {
    $where_conditions[] = "o.status = ?";
    $params[] = $status_filter;
}

if ($search) {
    $where_conditions[] = "(u.name LIKE ? OR o.id LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$where_clause = implode(" AND ", $where_conditions);

$sql = "SELECT o.*, u.name as customer_name, u.email as customer_email
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id 
        WHERE $where_clause 
        ORDER BY o.created_at DESC 
        LIMIT ? OFFSET ?";

$params[] = $limit;
$params[] = $offset;

$orders = fetchAll($sql, $params);

// Get total count for pagination
$count_sql = "SELECT COUNT(*) as count 
              FROM orders o 
              LEFT JOIN users u ON o.user_id = u.id 
              WHERE $where_clause";

$count_params = array_slice($params, 0, -2);
$total_orders = fetchOne($count_sql, $count_params)['count'];
$total_pages = ceil($total_orders / $limit);

// Set page variables
$current_page = 'orders';
$page_title = 'Orders Management - ' . SITE_NAME;

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
                <h1>Orders Management</h1>
                <p>Manage customer orders and track shipments</p>
            </div>
            <div class="header-right">
                <button class="btn-secondary" onclick="exportOrders()">
                    <i class="fas fa-download"></i>
                    Export Orders
                </button>
            </div>
        </div>

        <!-- Alerts -->
        <?php if (isset($message)): ?>
        <div class="alert alert-<?php echo $message_type; ?>">
            <?php echo $message; ?>
        </div>
        <?php endif; ?>

        <!-- Filters -->
        <div class="filters-section">
            <form method="GET" class="filters-form">
                <div class="filter-group">
                    <input type="text" name="search" placeholder="Search orders..." 
                           value="<?php echo htmlspecialchars($search); ?>" class="form-control">
                </div>
                <div class="filter-group">
                    <select name="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="pending" <?php echo ($status_filter == 'pending') ? 'selected' : ''; ?>>Pending</option>
                        <option value="processing" <?php echo ($status_filter == 'processing') ? 'selected' : ''; ?>>Processing</option>
                        <option value="shipped" <?php echo ($status_filter == 'shipped') ? 'selected' : ''; ?>>Shipped</option>
                        <option value="delivered" <?php echo ($status_filter == 'delivered') ? 'selected' : ''; ?>>Delivered</option>
                        <option value="cancelled" <?php echo ($status_filter == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>
                <div class="filter-group">
                    <button type="submit" class="btn-secondary">
                        <i class="fas fa-search"></i>
                        Filter
                    </button>
                    <a href="orders.php" class="btn-secondary">
                        <i class="fas fa-times"></i>
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Orders Table -->
        <div class="table-section">
            <div class="table-header">
                <h3>Orders (<?php echo $total_orders; ?> total)</h3>
            </div>
            
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>
                                <strong>#<?php echo $order['id']; ?></strong>
                            </td>
                            <td>
                                <div class="customer-info">
                                    <div class="customer-name"><?php echo htmlspecialchars($order['customer_name']); ?></div>
                                    <div class="customer-email"><?php echo htmlspecialchars($order['customer_email']); ?></div>
                                </div>
                            </td>
                            <td>
                                <?php 
                                // Get order items count
                                $items_sql = "SELECT COUNT(*) as count FROM order_items WHERE order_id = ?";
                                $items_count = fetchOne($items_sql, [$order['id']])['count'];
                                echo $items_count . ' items';
                                ?>
                            </td>
                            <td>
                                <strong><?php echo CURRENCY . number_format($order['total_amount'], 2); ?></strong>
                            </td>
                            <td>
                                <select class="status-select" onchange="updateOrderStatus(<?php echo $order['id']; ?>, this.value)">
                                    <option value="pending" <?php echo ($order['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="processing" <?php echo ($order['status'] == 'processing') ? 'selected' : ''; ?>>Processing</option>
                                    <option value="shipped" <?php echo ($order['status'] == 'shipped') ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="delivered" <?php echo ($order['status'] == 'delivered') ? 'selected' : ''; ?>>Delivered</option>
                                    <option value="cancelled" <?php echo ($order['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </td>
                            <td>
                                <?php echo date('M j, Y', strtotime($order['created_at'])); ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="order-details.php?id=<?php echo $order['id']; ?>" 
                                       class="btn-action" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button class="btn-action" title="Print Invoice" 
                                            onclick="printInvoice(<?php echo $order['id']; ?>)">
                                        <i class="fas fa-print"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $status_filter; ?>" 
                   class="page-link">
                    <i class="fas fa-chevron-left"></i>
                    Previous
                </a>
                <?php endif; ?>
                
                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $status_filter; ?>" 
                   class="page-link <?php echo ($i == $page) ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $status_filter; ?>" 
                   class="page-link">
                    Next
                    <i class="fas fa-chevron-right"></i>
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Order management functions
function updateOrderStatus(orderId, status) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.innerHTML = `
        <input type="hidden" name="action" value="update_status">
        <input type="hidden" name="order_id" value="${orderId}">
        <input type="hidden" name="status" value="${status}">
    `;
    document.body.appendChild(form);
    form.submit();
}

function printInvoice(orderId) {
    // Open invoice in new window for printing
    window.open(`order-invoice.php?id=${orderId}`, '_blank');
}

function exportOrders() {
    // Implement export functionality
    alert('Export functionality will be implemented here');
}

// Initialize admin functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize data tables
    initDataTables();
});
</script>

<style>
.customer-info {
    display: flex;
    flex-direction: column;
}

.customer-name {
    font-weight: 600;
    color: var(--admin-text);
    font-size: 0.9rem;
}

.customer-email {
    font-size: 0.8rem;
    color: var(--admin-text-secondary);
}

.status-select {
    background: var(--admin-bg);
    border: 1px solid var(--admin-border);
    color: var(--admin-text);
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.8rem;
    cursor: pointer;
}

.status-select:focus {
    outline: none;
    border-color: var(--admin-accent);
}
</style>

<?php include 'includes/footer.php'; ?>
