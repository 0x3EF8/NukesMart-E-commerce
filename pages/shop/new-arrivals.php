<?php
// Include configuration
require_once '../../config/config.php';

// Check for maintenance mode
if (isMaintenanceMode() && (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin')) {
    header('Location: ../../pages/system/maintenance.php');
    exit();
}

// Set page variables
$current_page = 'new-arrivals';
$page_title = 'New Arrivals - ' . SITE_NAME;

// Get new arrivals (products created in the last 30 days)
$sql = "SELECT p.*, c.name as category_name, c.slug as category_slug 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.is_active = 1 
        AND p.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ORDER BY p.created_at DESC";
$new_arrivals = fetchAll($sql);

// Include header
include '../../includes/header.php';
include '../../includes/navigation.php';
?>

<main class="new-arrivals-page">
  <div class="container">
    <nav class="breadcrumb">
      <a href="../../index.php">Home</a> / New Arrivals
    </nav>
    
    <h1>New Arrivals</h1>
    <p class="new-arrivals-subtitle">Latest additions to our nuclear arsenal</p>
    
    <?php if (empty($new_arrivals)): ?>
      <div class="empty-new-arrivals">
        <i class="fas fa-rocket"></i>
        <h2>No new arrivals at the moment</h2>
        <p>Check back soon for the latest nuclear devices!</p>
        <a href="../../index.php" class="btn-primary">Browse All Products</a>
      </div>
    <?php else: ?>
      <div class="new-arrivals-content">
        <div class="section-header">
          <h2>Latest Products</h2>
          <div class="view-controls">
            <button class="view-btn active" data-view="grid"><i class="fas fa-th"></i></button>
            <button class="view-btn" data-view="list"><i class="fas fa-list"></i></button>
          </div>
        </div>
        
        <div class="products-grid">
          <?php foreach ($new_arrivals as $product): ?>
            <div class="product" data-category="<?php echo $product['category_slug']; ?>">
              <div class="product-image">
                <img src="<?php echo getImageUrl('nukes/' . htmlspecialchars($product['image'])); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                
                <!-- Product badges -->
                <div class="product-badges">
                  <span class="badge new">New</span>
                  <?php if ($product['original_price'] && $product['original_price'] > $product['price']): ?>
                    <span class="badge sale">Sale</span>
                  <?php endif; ?>
                  <?php if ($product['stock'] <= 5 && $product['stock'] > 0): ?>
                    <span class="badge low-stock">Low Stock</span>
                  <?php endif; ?>
                </div>
                
                <!-- Quick actions -->
                <div class="quick-actions">
                  <?php if (isset($_SESSION['user']['id'])): ?>
                    <button class="quick-btn wishlist-btn" data-product-id="<?php echo $product['id']; ?>" title="Add to Wishlist">
                      <i class="fas fa-heart"></i>
                    </button>
                  <?php endif; ?>
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
                  <span class="rating-text">(<?php echo $product['reviews_count']; ?> reviews)</span>
                </div>
                
                <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                
                <div class="product-price">
                  <?php if ($product['original_price'] && $product['original_price'] > $product['price']): ?>
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
      </div>
    <?php endif; ?>
  </div>
</main>

<script>
// View controls functionality
document.querySelectorAll('.view-btn').forEach(button => {
    button.addEventListener('click', function() {
        const view = this.dataset.view;
        const productsGrid = document.querySelector('.products-grid');
        
        // Update active button
        document.querySelectorAll('.view-btn').forEach(btn => btn.classList.remove('active'));
        this.classList.add('active');
        
        // Update grid class
        productsGrid.className = 'products-grid ' + view + '-view';
    });
});

// Add to cart functionality
document.querySelectorAll('.btn-add-cart').forEach(button => {
    button.addEventListener('click', function() {
        const productId = this.dataset.productId;
        
        fetch('ajax/cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=add&product_id=' + productId + '&quantity=1'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update cart count
                const cartCount = document.querySelector('.cart-count');
                if (cartCount) {
                    cartCount.textContent = data.cart_count;
                }
                
                // Show success message
                showNotification('Product added to cart!', 'success');
            } else {
                showNotification(data.message || 'Failed to add product to cart', 'error');
            }
        })
        .catch(error => {
            showNotification('Error adding product to cart', 'error');
        });
    });
});

// Wishlist functionality
document.querySelectorAll('.wishlist-btn').forEach(button => {
    button.addEventListener('click', function() {
        const productId = this.dataset.productId;
        
        fetch('ajax/wishlist.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=toggle&product_id=' + productId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update wishlist count
                const wishlistCount = document.querySelector('.wishlist-count');
                if (wishlistCount) {
                    wishlistCount.textContent = data.wishlist_count;
                }
                
                // Toggle button state
                const icon = this.querySelector('i');
                if (data.in_wishlist) {
                    icon.className = 'fas fa-heart';
                    this.style.color = '#ff4757';
                } else {
                    icon.className = 'fas fa-heart-o';
                    this.style.color = '#eaeaea';
                }
                
                showNotification(data.message, 'success');
            } else {
                showNotification(data.message || 'Failed to update wishlist', 'error');
            }
        })
        .catch(error => {
            showNotification('Error updating wishlist', 'error');
        });
    });
});

// Notification function
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
