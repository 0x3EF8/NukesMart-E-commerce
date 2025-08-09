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

// Handle product actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'delete':
                if (isset($_POST['product_id'])) {
                    $product_id = $_POST['product_id'];
                    $sql = "UPDATE products SET is_active = 0 WHERE id = ?";
                    executeQuery($sql, [$product_id]);
                    $message = "Product deleted successfully!";
                    $message_type = "success";
                }
                break;
                
            case 'toggle_status':
                if (isset($_POST['product_id'])) {
                    $product_id = $_POST['product_id'];
                    $sql = "UPDATE products SET is_active = NOT is_active WHERE id = ?";
                    executeQuery($sql, [$product_id]);
                    $message = "Product status updated successfully!";
                    $message_type = "success";
                }
                break;
        }
    }
}

// Get products with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? $_GET['search'] : '';
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';

// Build query
$where_conditions = ["p.is_active IN (0, 1)"];
$params = [];

if ($search) {
    $where_conditions[] = "(p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($category_filter) {
    $where_conditions[] = "p.category_id = ?";
    $params[] = $category_filter;
}

$where_clause = implode(" AND ", $where_conditions);

$sql = "SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE $where_clause 
        ORDER BY p.created_at DESC 
        LIMIT ? OFFSET ?";

$params[] = $limit;
$params[] = $offset;

$products = fetchAll($sql, $params);

// Get total count for pagination
$count_sql = "SELECT COUNT(*) as count 
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              WHERE $where_clause";

$count_params = array_slice($params, 0, -2);
$total_products = fetchOne($count_sql, $count_params)['count'];
$total_pages = ceil($total_products / $limit);

// Get categories for filter
$categories = getAllCategories();

// Set page variables
$current_page = 'products';
$page_title = 'Products Management - ' . SITE_NAME;

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
                <h1>Products Management</h1>
                <p>Manage your product catalog</p>
            </div>
            <div class="header-right">
                <a href="add-product.php" class="btn-primary">
                    <i class="fas fa-plus"></i>
                    Add Product
                </a>
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
                    <input type="text" name="search" placeholder="Search products..." 
                           value="<?php echo htmlspecialchars($search); ?>" class="form-control">
                </div>
                <div class="filter-group">
                    <select name="category" class="form-control">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>" 
                                <?php echo ($category_filter == $category['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <button type="submit" class="btn-secondary">
                        <i class="fas fa-search"></i>
                        Filter
                    </button>
                    <a href="products.php" class="btn-secondary">
                        <i class="fas fa-times"></i>
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Products Table -->
        <div class="table-section">
            <div class="table-header">
                <h3>Products (<?php echo $total_products; ?> total)</h3>
                <div class="table-actions">
                    <button class="btn-secondary" onclick="exportProducts()">
                        <i class="fas fa-download"></i>
                        Export
                    </button>
                </div>
            </div>
            
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th data-sortable="true" data-column="name">Name</th>
                            <th data-sortable="true" data-column="category">Category</th>
                            <th data-sortable="true" data-column="price">Price</th>
                            <th data-sortable="true" data-column="stock">Stock</th>
                            <th data-sortable="true" data-column="status">Status</th>
                            <th data-sortable="true" data-column="created">Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                        <tr>
                            <td>
                                <div class="product-image-cell">
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
                            </td>
                            <td data-name="<?php echo htmlspecialchars($product['name']); ?>">
                                <div class="product-info">
                                    <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                                    <p class="product-description">
                                        <?php echo htmlspecialchars(substr($product['description'], 0, 100)); ?>...
                                    </p>
                                </div>
                            </td>
                            <td data-category="<?php echo htmlspecialchars($product['category_name']); ?>">
                                <?php echo htmlspecialchars($product['category_name']); ?>
                            </td>
                            <td data-price="<?php echo $product['price']; ?>">
                                <?php echo CURRENCY . number_format($product['price'], 2); ?>
                            </td>
                            <td data-stock="<?php echo $product['stock']; ?>">
                                <span class="stock-badge <?php echo ($product['stock'] > 0) ? 'in-stock' : 'out-of-stock'; ?>">
                                    <?php echo $product['stock']; ?>
                                </span>
                            </td>
                            <td data-status="<?php echo $product['is_active']; ?>">
                                <span class="status-badge status-<?php echo $product['is_active'] ? 'active' : 'inactive'; ?>">
                                    <?php echo $product['is_active'] ? 'Active' : 'Inactive'; ?>
                                </span>
                            </td>
                            <td data-created="<?php echo $product['created_at']; ?>">
                                <?php echo date('M j, Y', strtotime($product['created_at'])); ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="edit-product.php?id=<?php echo $product['id']; ?>" 
                                       class="btn-action" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="../product.php?id=<?php echo $product['id']; ?>" 
                                       class="btn-action" title="View" target="_blank">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button class="btn-action" title="Toggle Status" 
                                            onclick="toggleProductStatus(<?php echo $product['id']; ?>)">
                                        <i class="fas fa-toggle-<?php echo $product['is_active'] ? 'on' : 'off'; ?>"></i>
                                    </button>
                                    <button class="btn-action" title="Delete" 
                                            onclick="deleteProduct(<?php echo $product['id']; ?>)">
                                        <i class="fas fa-trash"></i>
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
                <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $category_filter; ?>" 
                   class="page-link">
                    <i class="fas fa-chevron-left"></i>
                    Previous
                </a>
                <?php endif; ?>
                
                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $category_filter; ?>" 
                   class="page-link <?php echo ($i == $page) ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $category_filter; ?>" 
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
// Product management functions
function deleteProduct(productId) {
    if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="product_id" value="${productId}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function toggleProductStatus(productId) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.innerHTML = `
        <input type="hidden" name="action" value="toggle_status">
        <input type="hidden" name="product_id" value="${productId}">
    `;
    document.body.appendChild(form);
    form.submit();
}

function exportProducts() {
    // Implement export functionality
    alert('Export functionality will be implemented here');
}

// Initialize admin functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize data tables
    initDataTables();
});
</script>

<?php include 'includes/footer.php'; ?>
