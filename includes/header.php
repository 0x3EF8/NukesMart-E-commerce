<?php
if (!isset($page_title)) {
    $page_title = SITE_NAME . ' - Premium Nuclear Solutions';
}

// Determine the base path based on current location
$current_file = $_SERVER['SCRIPT_NAME'];
$base_path = '';

if (strpos($current_file, '/pages/') !== false) {
    $base_path = '../../';
} elseif (strpos($current_file, '/admin/') !== false) {
    $base_path = '../';
} else {
    $base_path = './';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo htmlspecialchars($page_title); ?></title>
  <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/styles.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
</head>
<body>
  <header>
    <div class="logo">
      <img src="<?php echo $base_path; ?>assets/img/logo.png" alt="<?php echo SITE_NAME; ?> Logo">
      <div>
        <h1><?php echo SITE_NAME; ?></h1>
        <div class="tagline">
          <p class="text"><?php echo SITE_TAGLINE; ?></p>
          <p class="owner"><?php echo SITE_OWNER; ?></p>
        </div>
      </div>
    </div>
    
    <div class="header-actions">
      <div class="search-box">
        <form action="<?php echo $base_path; ?>pages/shop/products.php" method="GET">
          <input type="text" name="q" placeholder="Search products..." value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
          <button type="submit"><i class="fas fa-search"></i></button>
        </form>
      </div>
      
      <div class="user-menu">
        <?php if (isset($_SESSION['user']['id'])): ?>
          <a href="<?php echo $base_path; ?>pages/user/wishlist.php" class="wishlist-icon" title="Wishlist">
            <i class="fas fa-heart"></i>
            <?php 
            $wishlist_count = getWishlistCount($_SESSION['user']['id']);
            if ($wishlist_count > 0): ?>
              <span class="badge"><?php echo $wishlist_count; ?></span>
            <?php endif; ?>
          </a>
        <?php endif; ?>
        
        <a href="<?php echo $base_path; ?>pages/shop/cart.php" class="cart-icon" title="Shopping Cart">
          <i class="fas fa-shopping-cart"></i>
          <?php 
          $user_id = $_SESSION['user']['id'] ?? null;
          $session_id = $_SESSION['session_id'];
          $cart_count = getCartCount($user_id, $session_id);
          if ($cart_count > 0): ?>
            <span class="badge"><?php echo $cart_count; ?></span>
          <?php endif; ?>
        </a>
        
        <?php if (isset($_SESSION['user'])): ?>
          <div class="user-dropdown">
            <button class="user-btn">
              <i class="fas fa-user"></i>
              <span><?php echo htmlspecialchars($_SESSION['user']['name']); ?></span>
              <i class="fas fa-chevron-down"></i>
            </button>
            <div class="dropdown-menu">
              <?php if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin'): ?>
                <a href="<?php echo $base_path; ?>admin/index.php"><i class="fas fa-tachometer-alt"></i> Admin Dashboard</a>
                <div class="dropdown-divider"></div>
              <?php endif; ?>
              <a href="<?php echo $base_path; ?>pages/user/profile.php"><i class="fas fa-user-circle"></i> My Profile</a>
              <a href="<?php echo $base_path; ?>pages/user/orders.php"><i class="fas fa-box"></i> My Orders</a>
              <a href="<?php echo $base_path; ?>pages/user/wishlist.php"><i class="fas fa-heart"></i> Wishlist</a>
              <a href="<?php echo $base_path; ?>pages/auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
          </div>
        <?php else: ?>
          <div class="auth-buttons">
            <a href="<?php echo $base_path; ?>pages/auth/login.php" class="btn-login">Login</a>
            <a href="<?php echo $base_path; ?>pages/auth/register.php" class="btn-register">Register</a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </header> 