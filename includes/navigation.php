<?php
if (!isset($current_page)) {
    $current_page = 'home';
}

// Get categories from database
$categories = getAllCategories();

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
<nav class="nav">
  <ul class="nav-main">
    <li><a href="<?php echo $base_path; ?>index.php" class="<?php echo ($current_page == 'home') ? 'active' : ''; ?>">Home</a></li>
    
    <li><a href="<?php echo $base_path; ?>pages/shop/products.php" class="<?php echo ($current_page == 'products') ? 'active' : ''; ?>">Products</a></li>
    <li><a href="<?php echo $base_path; ?>pages/user/about.php" class="<?php echo ($current_page == 'about') ? 'active' : ''; ?>">About Us</a></li>
    <li><a href="<?php echo $base_path; ?>pages/user/contact.php" class="<?php echo ($current_page == 'contact') ? 'active' : ''; ?>">Contact</a></li>
  </ul>
  
  <ul class="nav-secondary">
    <li><a href="<?php echo $base_path; ?>pages/shop/deals.php"><i class="fas fa-tag"></i> Special Deals</a></li>
    <li><a href="<?php echo $base_path; ?>pages/shop/new-arrivals.php"><i class="fas fa-star"></i> New Arrivals</a></li>
  </ul>
</nav> 