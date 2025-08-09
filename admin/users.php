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

// Get users with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? $_GET['search'] : '';
$role_filter = isset($_GET['role']) ? $_GET['role'] : '';

// Build query
$where_conditions = ["1=1"];
$params = [];

if ($search) {
    $where_conditions[] = "(name LIKE ? OR email LIKE ? OR phone LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($role_filter) {
    $where_conditions[] = "role = ?";
    $params[] = $role_filter;
}

$where_clause = implode(" AND ", $where_conditions);

$sql = "SELECT * FROM users WHERE $where_clause ORDER BY created_at DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;

$users = fetchAll($sql, $params);

// Get total count for pagination
$count_sql = "SELECT COUNT(*) as count FROM users WHERE $where_clause";
$count_params = array_slice($params, 0, -2);
$total_users = fetchOne($count_sql, $count_params)['count'];
$total_pages = ceil($total_users / $limit);

// Set page variables
$current_page = 'users';
$page_title = 'Users Management - ' . SITE_NAME;

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
                <h1>Users Management</h1>
                <p>Manage user accounts and permissions</p>
            </div>
            <div class="header-right">
                <button class="btn-primary" onclick="openAddUserModal()">
                    <i class="fas fa-plus"></i>
                    Add User
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-section">
            <form method="GET" class="filters-form">
                <div class="filter-group">
                    <input type="text" name="search" placeholder="Search users..." 
                           value="<?php echo htmlspecialchars($search); ?>" class="form-control">
                </div>
                <div class="filter-group">
                    <select name="role" class="form-control">
                        <option value="">All Roles</option>
                        <option value="admin" <?php echo ($role_filter === 'admin') ? 'selected' : ''; ?>>Admin</option>
                        <option value="customer" <?php echo ($role_filter === 'customer') ? 'selected' : ''; ?>>Customer</option>
                    </select>
                </div>
                <div class="filter-group">
                    <button type="submit" class="btn-secondary">
                        <i class="fas fa-search"></i>
                        Search
                    </button>
                    <a href="users.php" class="btn-secondary">
                        <i class="fas fa-times"></i>
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Users Table -->
        <div class="table-section">
            <div class="table-header">
                <h3>Users (<?php echo $total_users; ?> total)</h3>
            </div>
            
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Phone</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td>
                                <div class="user-info">
                                    <h4><?php echo htmlspecialchars($user['name']); ?></h4>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <span class="role-badge role-<?php echo $user['role']; ?>">
                                    <?php echo ucfirst($user['role']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($user['phone'] ?: 'N/A'); ?></td>
                            <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-action" title="Edit" onclick="editUser(<?php echo $user['id']; ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <?php if ($user['id'] != $_SESSION['user']['id']): ?>
                                    <button class="btn-action" title="Delete" onclick="deleteUser(<?php echo $user['id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <?php endif; ?>
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
                <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&role=<?php echo $role_filter; ?>" class="page-link">
                    <i class="fas fa-chevron-left"></i>
                    Previous
                </a>
                <?php endif; ?>
                
                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&role=<?php echo $role_filter; ?>" 
                   class="page-link <?php echo ($i == $page) ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&role=<?php echo $role_filter; ?>" class="page-link">
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
function openAddUserModal() {
    // Implement add user modal
    alert('Add user functionality will be implemented here');
}

function editUser(id) {
    window.open(`edit-user.php?id=${id}`, '_blank');
}

function deleteUser(id) {
    if (confirm('Are you sure you want to delete this user?')) {
        // Implement delete user functionality
        alert('Delete user functionality will be implemented here');
    }
}
</script>

<?php include 'includes/footer.php'; ?>
