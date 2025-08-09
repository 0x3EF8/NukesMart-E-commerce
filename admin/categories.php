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

// Handle category actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $name = $_POST['name'] ?? '';
                $slug = strtolower(str_replace(' ', '-', $name));
                $description = $_POST['description'] ?? '';
                $icon = $_POST['icon'] ?? '';
                
                if (!empty($name)) {
                    $sql = "INSERT INTO categories (name, slug, description, icon) VALUES (?, ?, ?, ?)";
                    executeQuery($sql, [$name, $slug, $description, $icon]);
                    $message = "Category added successfully!";
                    $message_type = "success";
                }
                break;
                
            case 'update':
                $id = $_POST['id'] ?? '';
                $name = $_POST['name'] ?? '';
                $slug = strtolower(str_replace(' ', '-', $name));
                $description = $_POST['description'] ?? '';
                $icon = $_POST['icon'] ?? '';
                
                if (!empty($id) && !empty($name)) {
                    $sql = "UPDATE categories SET name = ?, slug = ?, description = ?, icon = ? WHERE id = ?";
                    executeQuery($sql, [$name, $slug, $description, $icon, $id]);
                    $message = "Category updated successfully!";
                    $message_type = "success";
                }
                break;
                
            case 'delete':
                $id = $_POST['id'] ?? '';
                if (!empty($id)) {
                    // Check if category has products
                    $check_sql = "SELECT COUNT(*) as count FROM products WHERE category_id = ?";
                    $result = fetchOne($check_sql, [$id]);
                    
                    if ($result['count'] > 0) {
                        $message = "Cannot delete category with existing products!";
                        $message_type = "error";
                    } else {
                        $sql = "DELETE FROM categories WHERE id = ?";
                        executeQuery($sql, [$id]);
                        $message = "Category deleted successfully!";
                        $message_type = "success";
                    }
                }
                break;
        }
    }
}

// Get categories
$categories = getAllCategories();

// Set page variables
$current_page = 'categories';
$page_title = 'Categories Management - ' . SITE_NAME;

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
                <h1>Categories Management</h1>
                <p>Manage product categories and organization</p>
            </div>
            <div class="header-right">
                <button class="btn-primary" onclick="openAddModal()">
                    <i class="fas fa-plus"></i>
                    Add Category
                </button>
            </div>
        </div>

        <!-- Alerts -->
        <?php if (isset($message)): ?>
        <div class="alert alert-<?php echo $message_type; ?>">
            <?php echo $message; ?>
        </div>
        <?php endif; ?>

        <!-- Categories Table -->
        <div class="table-section">
            <div class="table-header">
                <h3>Categories (<?php echo count($categories); ?> total)</h3>
            </div>
            
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Icon</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Description</th>
                            <th>Products</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                        <tr>
                            <td>
                                <div class="category-icon">
                                    <i class="fas fa-<?php echo htmlspecialchars($category['icon'] ?: 'tag'); ?>"></i>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($category['name']); ?></td>
                            <td><?php echo htmlspecialchars($category['slug']); ?></td>
                            <td><?php echo htmlspecialchars($category['description'] ?: 'No description'); ?></td>
                            <td>
                                <?php 
                                $product_count_sql = "SELECT COUNT(*) as count FROM products WHERE category_id = ?";
                                $product_count = fetchOne($product_count_sql, [$category['id']]);
                                echo $product_count['count'];
                                ?>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($category['created_at'])); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-action" title="Edit" onclick="editCategory(<?php echo $category['id']; ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-action" title="Delete" onclick="deleteCategory(<?php echo $category['id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Category Modal -->
<div id="categoryModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Add Category</h3>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <form id="categoryForm" method="POST">
            <input type="hidden" name="action" id="formAction" value="add">
            <input type="hidden" name="id" id="categoryId">
            
            <div class="form-group">
                <label for="name">Category Name *</label>
                <input type="text" id="name" name="name" required class="form-control">
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="3" class="form-control"></textarea>
            </div>
            
            <div class="form-group">
                <label for="icon">Icon (FontAwesome class)</label>
                <input type="text" id="icon" name="icon" class="form-control" placeholder="e.g., tag, box, rocket">
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-primary">Save Category</button>
                <button type="button" class="btn-secondary" onclick="closeModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Add Category';
    document.getElementById('formAction').value = 'add';
    document.getElementById('categoryId').value = '';
    document.getElementById('categoryForm').reset();
    document.getElementById('categoryModal').classList.add('show');
}

function editCategory(id) {
    // Fetch category data and populate form
    fetch(`../ajax/categories.php?action=get&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('modalTitle').textContent = 'Edit Category';
                document.getElementById('formAction').value = 'update';
                document.getElementById('categoryId').value = data.category.id;
                document.getElementById('name').value = data.category.name;
                document.getElementById('description').value = data.category.description || '';
                document.getElementById('icon').value = data.category.icon || '';
                document.getElementById('categoryModal').classList.add('show');
            }
        });
}

function deleteCategory(id) {
    if (confirm('Are you sure you want to delete this category?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="${id}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function closeModal() {
    document.getElementById('categoryModal').classList.remove('show');
}
</script>

<?php include 'includes/footer.php'; ?>
