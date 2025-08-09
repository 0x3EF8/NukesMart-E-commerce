<?php
// Include configuration
require_once '../../config/config.php';

// Check for maintenance mode
if (isMaintenanceMode() && (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin')) {
    header('Location: ../../pages/system/maintenance.php');
    exit();
}

// Set page variables
$current_page = 'category';
$page_title = 'Category - ' . SITE_NAME;

// Get category from URL
$category_slug = $_GET['category'] ?? '';

// Get category details
$category = getCategoryBySlug($category_slug);
if (!$category) {
    header('Location: ../../index.php');
    exit();
}

// Get products for this category
$products = getProductsByCategory($category_slug);

// Include header
include '../../includes/header.php';
include '../../includes/navigation.php';
?>

<main class="category-page">
  <div class="container">
    <nav class="breadcrumb">
      <a href="../../index.php">Home</a>
      <i class="fas fa-chevron-right"></i>
      <span><?php echo htmlspecialchars($category['name']); ?></span>
    </nav>
    
    <div class="category-header">
      <h1><?php echo htmlspecialchars($category['name']); ?></h1>
      <p><?php echo count($products); ?> products found</p>
    </div>
    
    <?php if (empty($products)): ?>
      <div class="empty-category">
        <i class="fas fa-box-open"></i>
        <h2>No products found</h2>
        <p>No products are available in this category at the moment.</p>
        <a href="../../index.php" class="btn-primary">Browse All Products</a>
      </div>
    <?php else: ?>
      <div class="products-grid">
        <?php foreach ($products as $product): ?>
          <div class="product" data-category="<?php echo $product['category']; ?>">
            <div class="product-image">
              <img src="<?php echo getImageUrl('nukes/' . htmlspecialchars($product['image'])); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
              
              <!-- Product badges -->
              <div class="product-badges">
                <?php if ($product['original_price'] > $product['price']): ?>
                  <span class="badge sale">Sale</span>
                <?php endif; ?>
                <?php if ($product['stock'] <= 5): ?>
                  <span class="badge low-stock">Low Stock</span>
                <?php endif; ?>
              </div>
              
              <!-- Quick actions -->
              <div class="quick-actions">
                <button class="quick-btn wishlist-btn" data-product-id="<?php echo $product['id']; ?>" title="Add to Wishlist">
                  <i class="fas fa-heart"></i>
                </button>
                <button class="quick-btn view-btn" onclick="window.location.href='product.php?id=<?php echo $product['id']; ?>'" title="View Details">
                  <i class="fas fa-eye"></i>
                </button>
              </div>
            </div>
            
            <div class="product-info">
              <h3 class="product-name">
                <a href="product.php?id=<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?></a>
              </h3>
              
              <div class="product-rating">
                <div class="stars">
                  <?php for ($i = 1; $i <= 5; $i++): ?>
                    <i class="fas fa-star<?php echo $i <= $product['rating'] ? '' : '-o'; ?>"></i>
                  <?php endfor; ?>
                </div>
                <span class="rating-text">(<?php echo $product['reviews']; ?> reviews)</span>
              </div>
              
              <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
              
              <div class="product-price">
                <?php if ($product['original_price'] > $product['price']): ?>
                  <span class="original-price"><?php echo formatPrice($product['original_price']); ?></span>
                <?php endif; ?>
                <span class="current-price"><?php echo formatPrice($product['price']); ?></span>
              </div>
              
              <div class="product-stock">
                <span class="stock-status <?php echo $product['stock'] > 0 ? 'in-stock' : 'out-of-stock'; ?>">
                  <?php echo $product['stock'] > 0 ? 'In Stock (' . $product['stock'] . ')' : 'Out of Stock'; ?>
                </span>
              </div>
              
              <div class="product-actions">
                <button class="btn-add-cart" data-product-id="<?php echo $product['id']; ?>" <?php echo $product['stock'] <= 0 ? 'disabled' : ''; ?>>
                  <i class="fas fa-shopping-cart"></i> Add to Cart
                </button>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</main>

<script>
// Add to cart functionality
document.querySelectorAll('.btn-add-cart').forEach(button => {
    button.addEventListener('click', function() {
        const productId = this.dataset.productId;
        addToCart(productId);
    });
});

// Add to wishlist functionality
document.querySelectorAll('.wishlist-btn').forEach(button => {
    button.addEventListener('click', function() {
        const productId = this.dataset.productId;
        addToWishlist(productId);
    });
});

function addToCart(productId) {
    fetch('ajax/cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=add&product_id=' + productId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Product added to cart!', 'success');
        } else {
            showNotification(data.message, 'error');
        }
    });
}

function addToWishlist(productId) {
    fetch('ajax/wishlist.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=add&product_id=' + productId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Product added to wishlist!', 'success');
        } else {
            showNotification(data.message, 'error');
        }
    });
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = 'notification ' + type;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>

<?php include '../../includes/footer.php'; ?>
