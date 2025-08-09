<?php
// Include configuration
require_once '../../config/config.php';

// Check for maintenance mode
if (isMaintenanceMode() && (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin')) {
    header('Location: ../../pages/system/maintenance.php');
    exit();
}

// Set page variables
$current_page = 'products';
$page_title = 'All Products - ' . SITE_NAME;

// Get products and categories from database
$products = getAllProducts();
$categories = getAllCategories();

// Include header
include '../../includes/header.php';
include '../../includes/navigation.php';
?>

<main class="products-page">
  <div class="container">
    <!-- Page Header -->
    <div class="page-header">
      <div class="breadcrumb">
        <a href="../../index.php">Home</a>
        <i class="fas fa-chevron-right"></i>
        <span>All Products</span>
      </div>
      <h1>Strategic Defense Products</h1>
      <p>Discover our comprehensive collection of premium nuclear solutions designed for strategic defense requirements</p>
    </div>

    <!-- Products Layout with Sidebar -->
    <div class="products-layout">
      <!-- Sidebar -->
      <aside class="products-sidebar">
        <div class="sidebar-header">
          <h3>Filters</h3>
          <button class="btn-clear-filters" id="clear-filters">Clear All</button>
        </div>
        
        <div class="sidebar-section">
          <h4>Category</h4>
          <select id="category-filter" class="sidebar-select">
            <option value="all">All Categories</option>
            <?php foreach ($categories as $category): ?>
              <option value="<?php echo $category['slug']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        
        <div class="sidebar-section">
          <h4>Price Range</h4>
          <select id="price-filter" class="sidebar-select">
            <option value="all">All Prices</option>
            <option value="0-5000000">Under ₱5M</option>
            <option value="5000000-20000000">₱5M - ₱20M</option>
            <option value="20000000-100000000">₱20M - ₱100M</option>
            <option value="100000000+">Over ₱100M</option>
          </select>
        </div>
        
        <div class="sidebar-section">
          <h4>Availability</h4>
          <select id="stock-filter" class="sidebar-select">
            <option value="all">All Items</option>
            <option value="in-stock">In Stock</option>
            <option value="low-stock">Low Stock</option>
            <option value="out-of-stock">Out of Stock</option>
          </select>
        </div>
        
        <div class="sidebar-section">
          <h4>View</h4>
          <div class="view-controls">
            <button class="view-btn active" data-view="grid" title="Grid View">
              <i class="fas fa-th"></i>
            </button>
            <button class="view-btn" data-view="list" title="List View">
              <i class="fas fa-list"></i>
            </button>
          </div>
        </div>
        
        <div class="sidebar-section">
          <h4>Sort by</h4>
          <select class="sidebar-select" id="sort-select">
            <option value="default">Default</option>
            <option value="name-asc">Name (A-Z)</option>
            <option value="name-desc">Name (Z-A)</option>
            <option value="price-asc">Price (Low to High)</option>
            <option value="price-desc">Price (High to Low)</option>
            <option value="rating-desc">Rating (High to Low)</option>
            <option value="newest">Newest First</option>
          </select>
        </div>
      </aside>

      <!-- Main Content -->
      <main class="products-main">
        <!-- Results Summary -->
        <div class="results-summary">
          <div class="results-info">
            <span class="results-count"><?php echo count($products); ?> products found</span>
            <span class="results-filters" id="active-filters"></span>
          </div>
        </div>

        <!-- Products Grid -->
        <div class="products-grid" id="products-grid">
              <?php foreach ($products as $product): ?>
          <div class="product" 
               data-category="<?php echo $product['category_slug']; ?>"
               data-price="<?php echo $product['price']; ?>"
               data-stock="<?php echo $product['stock']; ?>"
               data-rating="<?php echo $product['rating']; ?>"
               data-name="<?php echo htmlspecialchars(strtolower($product['name'])); ?>">
          <div class="product-image">
            <img src="<?php echo getImageUrl('nukes/' . htmlspecialchars($product['image'])); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            
            <!-- Product badges -->
            <div class="product-badges">
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
              <div class="stars" data-rating="<?php echo $product['rating']; ?>">
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
      </main>
    </div>
  </div>
</main>

<script>
// Enhanced Products Page Functionality
class ProductsManager {
    constructor() {
        this.products = Array.from(document.querySelectorAll('.product'));
        this.filters = {
            category: 'all',
            price: 'all',
            stock: 'all'
        };
        this.sortBy = 'default';
        this.viewMode = 'grid';
        
        this.initializeEventListeners();
        this.updateResultsCount();
    }
    
    initializeEventListeners() {
        // Filter event listeners
        document.getElementById('category-filter').addEventListener('change', (e) => {
            this.filters.category = e.target.value;
            this.applyFilters();
        });
        
        document.getElementById('price-filter').addEventListener('change', (e) => {
            this.filters.price = e.target.value;
            this.applyFilters();
        });
        
        document.getElementById('stock-filter').addEventListener('change', (e) => {
            this.filters.stock = e.target.value;
            this.applyFilters();
        });
        
        // Sort event listener
        document.getElementById('sort-select').addEventListener('change', (e) => {
            this.sortBy = e.target.value;
            this.applySorting();
        });
        
        // View controls
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.setViewMode(e.target.closest('.view-btn').dataset.view);
            });
        });
        
        // Clear filters
        document.getElementById('clear-filters').addEventListener('click', () => {
            this.clearAllFilters();
        });
        
        // Cart and wishlist functionality
        this.initializeProductActions();
    }
    
    applyFilters() {
        this.products.forEach(product => {
            const shouldShow = this.shouldShowProduct(product);
            product.style.display = shouldShow ? 'block' : 'none';
        });
        
        this.updateResultsCount();
        this.updateActiveFilters();
    }
    
    shouldShowProduct(product) {
        // Category filter
        if (this.filters.category !== 'all' && product.dataset.category !== this.filters.category) {
            return false;
        }
        
        // Price filter
        if (this.filters.price !== 'all') {
            const price = parseFloat(product.dataset.price);
            const [min, max] = this.filters.price.split('-').map(p => p === '+' ? Infinity : parseFloat(p));
            if (price < min || (max !== Infinity && price > max)) {
                return false;
            }
        }
        
        // Stock filter
        if (this.filters.stock !== 'all') {
            const stock = parseInt(product.dataset.stock);
            switch (this.filters.stock) {
                case 'in-stock':
                    if (stock <= 0) return false;
                    break;
                case 'low-stock':
                    if (stock > 5 || stock <= 0) return false;
                    break;
                case 'out-of-stock':
                    if (stock > 0) return false;
                    break;
            }
        }
        
        return true;
    }
    
    applySorting() {
        const visibleProducts = this.products.filter(p => p.style.display !== 'none');
        const productsGrid = document.getElementById('products-grid');
        
        visibleProducts.sort((a, b) => {
            switch(this.sortBy) {
                case 'name-asc':
                    return a.dataset.name.localeCompare(b.dataset.name);
                case 'name-desc':
                    return b.dataset.name.localeCompare(a.dataset.name);
                case 'price-asc':
                    return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
                case 'price-desc':
                    return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
                case 'rating-desc':
                    return parseFloat(b.dataset.rating) - parseFloat(a.dataset.rating);
                default:
                    return 0;
            }
        });
        
        // Re-append sorted products
        visibleProducts.forEach(product => productsGrid.appendChild(product));
    }
    
    setViewMode(mode) {
        this.viewMode = mode;
        const productsGrid = document.getElementById('products-grid');
        
        // Update active button
        document.querySelectorAll('.view-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelector(`[data-view="${mode}"]`).classList.add('active');
        
        // Update grid class
        productsGrid.className = `products-grid ${mode}-view`;
    }
    
    clearAllFilters() {
        document.getElementById('category-filter').value = 'all';
        document.getElementById('price-filter').value = 'all';
        document.getElementById('stock-filter').value = 'all';
        document.getElementById('sort-select').value = 'default';
        
        this.filters = { category: 'all', price: 'all', stock: 'all' };
        this.sortBy = 'default';
        
        this.products.forEach(product => product.style.display = 'block');
        this.updateResultsCount();
        this.updateActiveFilters();
    }
    
    updateResultsCount() {
        const visibleCount = this.products.filter(p => p.style.display !== 'none').length;
        document.querySelector('.results-count').textContent = `${visibleCount} products found`;
    }
    
    updateActiveFilters() {
        const activeFilters = [];
        if (this.filters.category !== 'all') {
            const categoryName = document.getElementById('category-filter').options[document.getElementById('category-filter').selectedIndex].text;
            activeFilters.push(categoryName);
        }
        if (this.filters.price !== 'all') {
            const priceName = document.getElementById('price-filter').options[document.getElementById('price-filter').selectedIndex].text;
            activeFilters.push(priceName);
        }
        if (this.filters.stock !== 'all') {
            const stockName = document.getElementById('stock-filter').options[document.getElementById('stock-filter').selectedIndex].text;
            activeFilters.push(stockName);
        }
        
        const filtersElement = document.getElementById('active-filters');
        if (activeFilters.length > 0) {
            filtersElement.textContent = `• Filtered by: ${activeFilters.join(', ')}`;
        } else {
            filtersElement.textContent = '';
        }
    }
    
    initializeProductActions() {
        // Add to cart functionality
        document.querySelectorAll('.btn-add-cart').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const productId = button.dataset.productId;
                this.addToCart(productId);
            });
        });
        
        // Add to wishlist functionality
        document.querySelectorAll('.wishlist-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const productId = button.dataset.productId;
                this.addToWishlist(productId);
            });
        });
    }
    
    addToCart(productId) {
        // Prevent double clicks
        const button = document.querySelector(`[data-product-id="${productId}"].btn-add-cart`);
        if (button.disabled) return;
        
        // Disable button and show loading state
        button.disabled = true;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
        
        fetch('../../ajax/cart.php', {
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
                
                // Show success state briefly
                button.innerHTML = '<i class="fas fa-check"></i> Added!';
                button.style.background = '#28a745';
                
                setTimeout(() => {
                    button.disabled = false;
                    button.innerHTML = originalText;
                    button.style.background = '';
                }, 2000);
                
                showNotification('Product added to cart!', 'success');
            } else {
                // Reset button on error
                button.disabled = false;
                button.innerHTML = originalText;
                showNotification(data.message || 'Failed to add product to cart', 'error');
            }
        })
        .catch(error => {
            // Reset button on error
            button.disabled = false;
            button.innerHTML = originalText;
            showNotification('Error adding product to cart', 'error');
        });
    }
    
    addToWishlist(productId) {
        fetch('../../ajax/wishlist.php', {
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
                const button = document.querySelector(`[data-product-id="${productId}"].wishlist-btn`);
                const icon = button.querySelector('i');
                if (data.in_wishlist) {
                    icon.className = 'fas fa-heart';
                    button.style.color = '#ff4757';
                } else {
                    icon.className = 'fas fa-heart-o';
                    button.style.color = '#eaeaea';
                }
                
                showNotification(data.message, 'success');
            } else {
                showNotification(data.message || 'Failed to update wishlist', 'error');
            }
        })
        .catch(error => {
            showNotification('Error updating wishlist', 'error');
        });
    }
}

// Initialize products manager
document.addEventListener('DOMContentLoaded', function() {
    new ProductsManager();
    
    // Real-time cart updates
    updateCartCount();
    
    // Update cart count every 30 seconds
    setInterval(updateCartCount, 30000);
});

// Function to update cart count in real-time
function updateCartCount() {
    fetch('../../ajax/cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=get_count'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const cartCount = document.querySelector('.cart-count');
            if (cartCount) {
                cartCount.textContent = data.cart_count;
            }
        }
    })
    .catch(error => {
        console.log('Error updating cart count:', error);
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
