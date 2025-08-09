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

// Get customers with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build query
$where_conditions = ["role = 'customer'"];
$params = [];

if ($search) {
    $where_conditions[] = "(name LIKE ? OR email LIKE ? OR phone LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$where_clause = implode(" AND ", $where_conditions);

$sql = "SELECT * FROM users WHERE $where_clause ORDER BY created_at DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;

$customers = fetchAll($sql, $params);

// Get total count for pagination
$count_sql = "SELECT COUNT(*) as count FROM users WHERE $where_clause";
$count_params = array_slice($params, 0, -2);
$total_customers = fetchOne($count_sql, $count_params)['count'];
$total_pages = ceil($total_customers / $limit);

// Set page variables
$current_page = 'customers';
$page_title = 'Customers Management - ' . SITE_NAME;

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
                <h1>Customers Management</h1>
                <p>Manage customer accounts and information</p>
            </div>
            <div class="header-right">
                <button class="btn-secondary" onclick="exportCustomers()">
                    <i class="fas fa-download"></i>
                    Export Customers
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-section">
            <form method="GET" class="filters-form">
                <div class="filter-group">
                    <input type="text" name="search" placeholder="Search customers..." 
                           value="<?php echo htmlspecialchars($search); ?>" class="form-control">
                </div>
                <div class="filter-group">
                    <button type="submit" class="btn-secondary">
                        <i class="fas fa-search"></i>
                        Search
                    </button>
                    <a href="customers.php" class="btn-secondary">
                        <i class="fas fa-times"></i>
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Customers Table -->
        <div class="table-section">
            <div class="table-header">
                <h3>Customers (<?php echo $total_customers; ?> total)</h3>
            </div>
            
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Orders</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customers as $customer): ?>
                        <tr>
                            <td><?php echo $customer['id']; ?></td>
                            <td>
                                <div class="customer-info">
                                    <h4><?php echo htmlspecialchars($customer['name']); ?></h4>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($customer['email']); ?></td>
                            <td><?php echo htmlspecialchars($customer['phone'] ?: 'N/A'); ?></td>
                            <td>
                                <?php echo htmlspecialchars(substr($customer['address'] ?: 'No address', 0, 50)); ?>
                                <?php if (strlen($customer['address'] ?: '') > 50): ?>...<?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                $order_count_sql = "SELECT COUNT(*) as count FROM orders WHERE user_id = ?";
                                $order_count = fetchOne($order_count_sql, [$customer['id']]);
                                echo $order_count['count'];
                                ?>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($customer['created_at'])); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-action" title="View Details" onclick="viewCustomer(<?php echo $customer['id']; ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn-action" title="View Orders" onclick="viewOrders(<?php echo $customer['id']; ?>)">
                                        <i class="fas fa-shopping-cart"></i>
                                    </button>
                                    <button class="btn-action" title="Edit" onclick="editCustomer(<?php echo $customer['id']; ?>)">
                                        <i class="fas fa-edit"></i>
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
                <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>" class="page-link">
                    <i class="fas fa-chevron-left"></i>
                    Previous
                </a>
                <?php endif; ?>
                
                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" 
                   class="page-link <?php echo ($i == $page) ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>" class="page-link">
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
function viewCustomer(id) {
    window.open(`customer-details.php?id=${id}`, '_blank');
}

function viewOrders(id) {
    window.open(`orders.php?customer_id=${id}`, '_blank');
}

function editCustomer(id) {
    window.open(`edit-customer.php?id=${id}`, '_blank');
}

function exportCustomers() {
    window.location.href = 'export-customers.php';
}
</script>

<?php include 'includes/footer.php'; ?>
