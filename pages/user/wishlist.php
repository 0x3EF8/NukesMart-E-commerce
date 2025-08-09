<?php
// Include configuration
require_once '../../config/config.php';

// Check for maintenance mode
if (isMaintenanceMode() && (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin')) {
    header('Location: ../../pages/system/maintenance.php');
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: ../../pages/auth/login.php');
    exit();
}

// Set page variables
$current_page = 'wishlist';
$page_title = $site_pages['wishlist']['title'];

// Get wishlist items
$wishlist_items = getWishlistItems($_SESSION['user']['id']);

// Include header
include '../../includes/header.php';
include '../../includes/navigation.php';
?>

<main class="wishlist-page">
  <div class="container">
    <div class="breadcrumb">
      <a href="index.php">Home</a> / Wishlist
    </div>
    
    <h1>My Wishlist</h1>
    
    <?php if (empty($wishlist_items)): ?>
      <div class="empty-wishlist">
        <i class="fas fa-heart"></i>
        <h2>Your wishlist is empty</h2>
        <p>Start adding your favorite nuclear devices to your wishlist!</p>
        <a href="index.php" class="btn-primary">Browse Products</a>
      </div>
    <?php else: ?>
      <div class="wishlist-content">
        <div class="wishlist-items">
          <?php foreach ($wishlist_items as $item): ?>
            <div class="wishlist-item" data-product-id="<?php echo $item['product_id']; ?>">
              <div class="item-image">
                <img src="<?php echo getImageUrl('nukes/' . htmlspecialchars($item['image'])); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
              </div>
              
              <div class="item-details">
                <h3 class="item-name">
                  <a href="product.php?id=<?php echo $item['product_id']; ?>"><?php echo htmlspecialchars($item['name']); ?></a>
                </h3>
                
                <div class="item-rating">
                  <div class="stars">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                      <i class="fas fa-star<?php echo $i <= ($item['rating'] ?? 0) ? '' : '-o'; ?>"></i>
                    <?php endfor; ?>
                  </div>
                  <span class="rating-text">(<?php echo $item['reviews_count'] ?? 0; ?> reviews)</span>
                </div>
                
                <div class="item-price">
                  <?php if (isset($item['original_price']) && $item['original_price'] && $item['original_price'] > $item['price']): ?>
                    <span class="original-price"><?php echo formatPrice($item['original_price']); ?></span>
                  <?php endif; ?>
                  <span class="current-price"><?php echo formatPrice($item['price']); ?></span>
                </div>
                
                <div class="item-stock">
                  <span class="stock-status <?php echo $item['stock'] > 0 ? 'in-stock' : 'out-of-stock'; ?>">
                    <?php echo $item['stock'] > 0 ? 'In Stock (' . $item['stock'] . ')' : 'Out of Stock'; ?>
                  </span>
                </div>
              </div>
              
              <div class="item-actions">
                <button class="btn-add-cart" data-product-id="<?php echo $item['product_id']; ?>" <?php echo $item['stock'] <= 0 ? 'disabled' : ''; ?>>
                  <i class="fas fa-shopping-cart"></i> Add to Cart
                </button>
                <button class="btn-remove-wishlist" data-product-id="<?php echo $item['product_id']; ?>">
                  <i class="fas fa-trash"></i> Remove
                </button>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
</main>

<script>
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

// Remove from wishlist functionality
document.querySelectorAll('.btn-remove-wishlist').forEach(button => {
    button.addEventListener('click', function() {
        const productId = this.dataset.productId;
        const wishlistItem = this.closest('.wishlist-item');
        
        fetch('ajax/wishlist.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=remove&product_id=' + productId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove item from DOM
                wishlistItem.remove();
                
                // Update wishlist count
                const wishlistCount = document.querySelector('.wishlist-count');
                if (wishlistCount) {
                    wishlistCount.textContent = data.wishlist_count;
                }
                
                // Check if wishlist is empty
                const remainingItems = document.querySelectorAll('.wishlist-item');
                if (remainingItems.length === 0) {
                    location.reload(); // Reload to show empty state
                }
                
                showNotification('Product removed from wishlist!', 'success');
            } else {
                showNotification(data.message || 'Failed to remove product from wishlist', 'error');
            }
        })
        .catch(error => {
            showNotification('Error removing product from wishlist', 'error');
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
