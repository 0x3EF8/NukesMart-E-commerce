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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? 0;
    $original_price = $_POST['original_price'] ?? 0;
    $stock = $_POST['stock'] ?? 0;
    $category_id = $_POST['category_id'] ?? '';
    $features = $_POST['features'] ?? [];
    $specifications = $_POST['specifications'] ?? [];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Handle image upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../assets/img/nukes/';
        $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($file_extension, $allowed_extensions)) {
            $image = uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $image;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                // Image uploaded successfully
            } else {
                $error = "Failed to upload image.";
            }
        } else {
            $error = "Invalid file type. Only JPG, PNG, and GIF are allowed.";
        }
    }
    
    if (empty($error)) {
        // Convert arrays to JSON
        $features_json = json_encode($features);
        $specifications_json = json_encode($specifications);
        
        // Insert product
        $sql = "INSERT INTO products (name, description, price, original_price, stock, category_id, image, features, specifications, is_active, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $params = [$name, $description, $price, $original_price, $stock, $category_id, $image, $features_json, $specifications_json, $is_active];
        
        if (executeQuery($sql, $params)) {
            $success = "Product added successfully!";
        } else {
            $error = "Failed to add product.";
        }
    }
}

// Get categories
$categories = getAllCategories();

// Set page variables
$current_page = 'products';
$page_title = 'Add Product - ' . SITE_NAME;

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
                <h1>Add New Product</h1>
                <p>Create a new product for your catalog</p>
            </div>
            <div class="header-right">
                <a href="products.php" class="btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Back to Products
                </a>
            </div>
        </div>

        <!-- Alerts -->
        <?php if (isset($success)): ?>
        <div class="alert alert-success">
            <?php echo $success; ?>
        </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
        <div class="alert alert-error">
            <?php echo $error; ?>
        </div>
        <?php endif; ?>

        <!-- Product Form -->
        <div class="form-section">
            <form method="POST" enctype="multipart/form-data" class="product-form">
                <div class="form-grid">
                    <!-- Basic Information -->
                    <div class="form-group">
                        <label for="name">Product Name *</label>
                        <input type="text" id="name" name="name" required class="form-control" 
                               value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="category_id">Category *</label>
                        <select id="category_id" name="category_id" required class="form-control">
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" 
                                    <?php echo (($_POST['category_id'] ?? '') == $category['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="price">Price *</label>
                        <input type="number" id="price" name="price" step="0.01" required class="form-control" 
                               value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="original_price">Original Price</label>
                        <input type="number" id="original_price" name="original_price" step="0.01" class="form-control" 
                               value="<?php echo htmlspecialchars($_POST['original_price'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="stock">Stock Quantity *</label>
                        <input type="number" id="stock" name="stock" required class="form-control" 
                               value="<?php echo htmlspecialchars($_POST['stock'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="image">Product Image *</label>
                        <input type="file" id="image" name="image" accept="image/*" required class="form-control">
                        <small>Recommended size: 800x600 pixels. Max file size: 2MB.</small>
                    </div>
                    
                    <div class="form-group full-width">
                        <label for="description">Description *</label>
                        <textarea id="description" name="description" rows="4" required class="form-control"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <!-- Features -->
                    <div class="form-group full-width">
                        <label>Features</label>
                        <div id="features-container">
                            <div class="feature-item">
                                <input type="text" name="features[]" placeholder="Enter feature" class="form-control">
                                <button type="button" class="btn-remove" onclick="removeFeature(this)">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <button type="button" class="btn-secondary" onclick="addFeature()">
                            <i class="fas fa-plus"></i>
                            Add Feature
                        </button>
                    </div>
                    
                    <!-- Specifications -->
                    <div class="form-group full-width">
                        <label>Specifications</label>
                        <div id="specifications-container">
                            <div class="spec-item">
                                <input type="text" name="specifications[]" placeholder="Enter specification" class="form-control">
                                <button type="button" class="btn-remove" onclick="removeSpec(this)">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <button type="button" class="btn-secondary" onclick="addSpec()">
                            <i class="fas fa-plus"></i>
                            Add Specification
                        </button>
                    </div>
                    
                    <!-- Status -->
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_active" <?php echo (($_POST['is_active'] ?? '') == 'on') ? 'checked' : ''; ?>>
                            <span class="checkmark"></span>
                            Active Product
                        </label>
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i>
                        Add Product
                    </button>
                    <a href="products.php" class="btn-secondary">
                        <i class="fas fa-times"></i>
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Feature management
function addFeature() {
    const container = document.getElementById('features-container');
    const featureItem = document.createElement('div');
    featureItem.className = 'feature-item';
    featureItem.innerHTML = `
        <input type="text" name="features[]" placeholder="Enter feature" class="form-control">
        <button type="button" class="btn-remove" onclick="removeFeature(this)">
            <i class="fas fa-times"></i>
        </button>
    `;
    container.appendChild(featureItem);
}

function removeFeature(button) {
    button.parentElement.remove();
}

// Specification management
function addSpec() {
    const container = document.getElementById('specifications-container');
    const specItem = document.createElement('div');
    specItem.className = 'spec-item';
    specItem.innerHTML = `
        <input type="text" name="specifications[]" placeholder="Enter specification" class="form-control">
        <button type="button" class="btn-remove" onclick="removeSpec(this)">
            <i class="fas fa-times"></i>
        </button>
    `;
    container.appendChild(specItem);
}

function removeSpec(button) {
    button.parentElement.remove();
}

// Form validation
document.querySelector('.product-form').addEventListener('submit', function(e) {
    const price = parseFloat(document.getElementById('price').value);
    const originalPrice = parseFloat(document.getElementById('original_price').value);
    
    if (originalPrice > 0 && originalPrice <= price) {
        e.preventDefault();
        alert('Original price must be greater than current price for discounts.');
        return false;
    }
});
</script>

<style>
.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

.feature-item, .spec-item {
    display: flex;
    gap: 10px;
    margin-bottom: 10px;
    align-items: center;
}

.btn-remove {
    background: var(--admin-danger);
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 4px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.btn-remove:hover {
    background: #d32f2f;
}

.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid var(--admin-border);
}
</style>

<?php include 'includes/footer.php'; ?>
