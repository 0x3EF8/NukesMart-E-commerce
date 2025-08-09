<?php
// Include configuration
require_once '../../config/config.php';

// Check for maintenance mode
if (isMaintenanceMode() && (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin')) {
    header('Location: ../../pages/system/maintenance.php');
    exit();
}

// Get product ID from URL
$product_id = $_GET['id'] ?? '';
$product = getProductById($product_id);

if (!$product) {
    header('Location: index.php');
    exit;
}

// Get all products for related products section
$products = getAllProducts();

// Set page variables
$current_page = 'product';
$page_title = $product['name'] . ' - ' . SITE_NAME;

// Include header
include '../../includes/header.php';
include '../../includes/navigation.php';
?>

<main class="product-page">
  <div class="container">
    <nav class="breadcrumb">
      <a href="../../index.php">Home</a>
      <i class="fas fa-chevron-right"></i>
      <a href="category.php?cat=<?php echo $product['category_slug'] ?? ''; ?>"><?php echo htmlspecialchars($product['category_name'] ?? ''); ?></a>
      <i class="fas fa-chevron-right"></i>
      <span><?php echo htmlspecialchars($product['name']); ?></span>
    </nav>
    
    <div class="product-details">
      <div class="product-gallery">
        <div class="main-image">
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
        </div>
      </div>
      
      <div class="product-info">
        <h1><?php echo htmlspecialchars($product['name']); ?></h1>
        
        <div class="product-rating">
          <div class="stars">
            <?php for ($i = 1; $i <= 5; $i++): ?>
              <i class="fas fa-star<?php echo $i <= $product['rating'] ? '' : '-o'; ?>"></i>
            <?php endfor; ?>
          </div>
          <span class="rating-text"><?php echo $product['rating']; ?> out of 5 (<?php echo $product['reviews_count'] ?? 0; ?> reviews)</span>
        </div>
        
        <div class="product-price">
          <?php if ($product['original_price'] > $product['price']): ?>
            <span class="original-price"><?php echo formatPrice($product['original_price']); ?></span>
            <span class="discount">Save <?php echo formatPrice($product['original_price'] - $product['price']); ?></span>
          <?php endif; ?>
          <span class="current-price"><?php echo formatPrice($product['price']); ?></span>
        </div>
        
        <div class="product-stock">
          <span class="stock-status <?php echo $product['stock'] > 0 ? 'in-stock' : 'out-of-stock'; ?>">
            <?php echo $product['stock'] > 0 ? 'In Stock (' . $product['stock'] . ' available)' : 'Out of Stock'; ?>
          </span>
        </div>
        
        <div class="product-description">
          <h3>Description</h3>
          <p><?php echo htmlspecialchars($product['long_description']); ?></p>
        </div>
        
        <?php if (!empty($product['features'])): ?>
        <div class="product-features">
          <h3>Key Features</h3>
          <ul>
            <?php 
            $features = is_string($product['features']) ? json_decode($product['features'], true) : $product['features'];
            if (is_array($features)):
              foreach ($features as $feature): ?>
              <li><i class="fas fa-check"></i> <?php echo htmlspecialchars($feature); ?></li>
            <?php endforeach; endif; ?>
          </ul>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($product['specifications'])): ?>
        <div class="product-specs">
          <h3>Specifications</h3>
          <div class="specs-grid">
            <?php 
            $specs = is_string($product['specifications']) ? json_decode($product['specifications'], true) : $product['specifications'];
            if (is_array($specs)):
              foreach ($specs as $spec => $value): ?>
              <div class="spec-item">
                <span class="spec-label"><?php echo htmlspecialchars($spec); ?>:</span>
                <span class="spec-value"><?php echo htmlspecialchars($value); ?></span>
              </div>
            <?php endforeach; endif; ?>
          </div>
        </div>
        <?php endif; ?>
        
        <div class="product-actions">
          <div class="quantity-selector">
            <label for="quantity">Quantity:</label>
            <div class="qty-controls">
              <button class="qty-btn minus" onclick="updateQuantity(-1)">-</button>
              <input type="number" id="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>">
              <button class="qty-btn plus" onclick="updateQuantity(1)">+</button>
            </div>
          </div>
          
          <div class="action-buttons">
            <button class="btn-add-cart" data-product-id="<?php echo $product['id']; ?>" <?php echo $product['stock'] <= 0 ? 'disabled' : ''; ?>>
              <i class="fas fa-shopping-cart"></i> Add to Cart
            </button>
            
            <?php if (isset($_SESSION['user']['id'])): ?>
            <button class="btn-wishlist" data-product-id="<?php echo $product['id']; ?>" data-in-wishlist="<?php echo isInWishlist($_SESSION['user']['id'], $product['id']) ? 'true' : 'false'; ?>">
              <i class="fas fa-heart"></i> 
              <span class="wishlist-text"><?php echo isInWishlist($_SESSION['user']['id'], $product['id']) ? 'Remove from Wishlist' : 'Add to Wishlist'; ?></span>
            </button>
            <?php endif; ?>
          </div>
        </div>
        
        <div class="product-meta">
          <div class="meta-item">
            <i class="fas fa-shipping-fast"></i>
            <span>Free shipping on orders over ₱10,000,000</span>
          </div>
          <div class="meta-item">
            <i class="fas fa-shield-alt"></i>
            <span>Secure payment processing</span>
          </div>
          <div class="meta-item">
            <i class="fas fa-undo"></i>
            <span>30-day return policy</span>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Related Products -->
    <section class="related-products">
      <h2>Related Products</h2>
      <div class="products-grid">
                 <?php 
         $related_products = array_filter($products, function($p) use ($product) {
             return $p['category_slug'] === $product['category_slug'] && $p['id'] !== $product['id'];
         });
         
         $related_products = array_slice($related_products, 0, 3);
         
         foreach ($related_products as $related): ?>
          <div class="product">
            <div class="product-image">
              <img src="<?php echo getImageUrl('nukes/' . htmlspecialchars($related['image'])); ?>" alt="<?php echo htmlspecialchars($related['name']); ?>">
            </div>
            
            <div class="product-info">
              <h3 class="product-name">
                <a href="product.php?id=<?php echo $related['id']; ?>"><?php echo htmlspecialchars($related['name']); ?></a>
              </h3>
              
              <div class="product-price">
                <?php if ($related['original_price'] > $related['price']): ?>
                  <span class="original-price"><?php echo formatPrice($related['original_price']); ?></span>
                <?php endif; ?>
                <span class="current-price"><?php echo formatPrice($related['price']); ?></span>
              </div>
              
              <div class="product-actions">
                <button class="btn-add-cart" data-product-id="<?php echo $related['id']; ?>">
                  <i class="fas fa-shopping-cart"></i> Add to Cart
                </button>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  </div>
</main>

<script>
let currentQuantity = 1;

function updateQuantity(change) {
    const input = document.getElementById('quantity');
    const newQuantity = Math.max(1, Math.min(<?php echo $product['stock']; ?>, currentQuantity + change));
    input.value = newQuantity;
    currentQuantity = newQuantity;
}

document.getElementById('quantity').addEventListener('change', function() {
    currentQuantity = parseInt(this.value);
});

// Add to cart functionality
document.querySelectorAll('.btn-add-cart').forEach(button => {
    button.addEventListener('click', function() {
        const productId = this.dataset.productId;
        const quantity = productId === '<?php echo $product['id']; ?>' ? currentQuantity : 1;
        addToCart(productId, quantity);
    });
});

// Wishlist functionality
document.querySelectorAll('.btn-wishlist').forEach(button => {
    button.addEventListener('click', function() {
        const productId = this.dataset.productId;
        const inWishlist = this.dataset.inWishlist === 'true';
        
        if (inWishlist) {
            removeFromWishlist(productId);
        } else {
            addToWishlist(productId);
        }
    });
});

function addToCart(productId, quantity = 1) {
    fetch('ajax/cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=add&product_id=${productId}&quantity=${quantity}`
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
        body: `action=add&product_id=${productId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const btn = document.querySelector(`[data-product-id="${productId}"]`);
            btn.dataset.inWishlist = 'true';
            btn.querySelector('.wishlist-text').textContent = 'Remove from Wishlist';
            showNotification('Product added to wishlist!', 'success');
        } else {
            showNotification(data.message, 'error');
        }
    });
}

function removeFromWishlist(productId) {
    fetch('ajax/wishlist.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=remove&product_id=${productId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const btn = document.querySelector(`[data-product-id="${productId}"]`);
            btn.dataset.inWishlist = 'false';
            btn.querySelector('.wishlist-text').textContent = 'Add to Wishlist';
            showNotification('Product removed from wishlist!', 'success');
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
