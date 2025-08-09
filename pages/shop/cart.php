<?php
// Include configuration
require_once '../../config/config.php';

// Check for maintenance mode
if (isMaintenanceMode() && (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin')) {
    header('Location: ../../pages/system/maintenance.php');
    exit();
}

// Set page variables
$current_page = 'cart';
$page_title = $site_pages['cart']['title'];

// Get cart items
$user_id = $_SESSION['user']['id'] ?? null;
$session_id = $_SESSION['session_id'];
$cart_items = getCartItems($user_id, $session_id);

// Include header
include '../../includes/header.php';
include '../../includes/navigation.php';
?>

<main class="cart-page">
  <div class="container">
    <h1>Shopping Cart</h1>
    
    <?php if (empty($cart_items)): ?>
      <div class="empty-cart">
        <i class="fas fa-shopping-cart"></i>
        <h2>Your cart is empty</h2>
        <p>Looks like you haven't added any products to your cart yet.</p>
        <a href="../../index.php" class="btn-primary">Continue Shopping</a>
      </div>
    <?php else: ?>
      <div class="cart-content">
        <div class="cart-items">
          <?php 
          $total = 0;
          $subtotal = 0;
          foreach ($cart_items as $item):
            $item_total = $item['price'] * $item['quantity'];
            $subtotal += $item_total;
          ?>
            <div class="cart-item" data-product-id="<?php echo $item['product_id']; ?>">
              <div class="item-image">
                <img src="<?php echo getImageUrl('nukes/' . htmlspecialchars($item['image'])); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
              </div>
              
              <div class="item-details">
                <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                <div class="item-price"><?php echo formatPrice($item['price']); ?></div>
              </div>
              
              <div class="item-quantity">
                <button class="qty-btn minus" data-product-id="<?php echo $item['product_id']; ?>">-</button>
                <input type="number" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock']; ?>" class="qty-input" data-product-id="<?php echo $item['product_id']; ?>">
                <button class="qty-btn plus" data-product-id="<?php echo $item['product_id']; ?>">+</button>
              </div>
              
              <div class="item-total">
                <?php echo formatPrice($item_total); ?>
              </div>
              
              <div class="item-actions">
                <button class="remove-btn" data-product-id="<?php echo $item['product_id']; ?>" title="Remove item">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
        
        <div class="cart-summary">
          <h3>Order Summary</h3>
          
          <div class="summary-item">
            <span>Subtotal:</span>
            <span><?php echo formatPrice($subtotal); ?></span>
          </div>
          
          <?php 
          $shipping = $subtotal >= FREE_SHIPPING_THRESHOLD ? 0 : SHIPPING_FEE;
          $tax = $subtotal * TAX_RATE;
          $total = $subtotal + $shipping + $tax;
          ?>
          
          <div class="summary-item">
            <span>Shipping:</span>
            <span><?php echo $shipping > 0 ? formatPrice($shipping) : 'Free'; ?></span>
          </div>
          
          <div class="summary-item">
            <span>Tax (12%):</span>
            <span><?php echo formatPrice($tax); ?></span>
          </div>
          
          <div class="summary-total">
            <span>Total:</span>
            <span><?php echo formatPrice($total); ?></span>
          </div>
          
          <?php if ($shipping > 0): ?>
            <div class="free-shipping-notice">
              <i class="fas fa-info-circle"></i>
              Add <?php echo formatPrice(FREE_SHIPPING_THRESHOLD - $subtotal); ?> more for free shipping!
            </div>
          <?php endif; ?>
          
          <div class="cart-actions">
            <a href="../../index.php" class="btn-secondary">Continue Shopping</a>
            <a href="checkout.php" class="btn-primary">Proceed to Checkout</a>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>
</main>

<script>
// Cart functionality
document.querySelectorAll('.qty-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const productId = this.dataset.productId;
        const input = document.querySelector(`.qty-input[data-product-id="${productId}"]`);
        const currentQty = parseInt(input.value);
        
        if (this.classList.contains('minus')) {
            if (currentQty > 1) {
                input.value = currentQty - 1;
                updateQuantity(productId, currentQty - 1);
            }
        } else {
            input.value = currentQty + 1;
            updateQuantity(productId, currentQty + 1);
        }
    });
});

document.querySelectorAll('.qty-input').forEach(input => {
    input.addEventListener('change', function() {
        const productId = this.dataset.productId;
        const quantity = parseInt(this.value);
        updateQuantity(productId, quantity);
    });
});

document.querySelectorAll('.remove-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const productId = this.dataset.productId;
        removeFromCart(productId);
    });
});

function updateQuantity(productId, quantity) {
    fetch('ajax/cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=update&product_id=${productId}&quantity=${quantity}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            showNotification(data.message, 'error');
        }
    });
}

function removeFromCart(productId) {
    if (confirm('Are you sure you want to remove this item from your cart?')) {
        fetch('ajax/cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=remove&product_id=${productId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                showNotification(data.message, 'error');
            }
        });
    }
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
