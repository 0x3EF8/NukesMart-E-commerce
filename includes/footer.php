<?php
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
  <footer>
    <div class="footer-content">
      <div class="container">
        <div class="footer-grid">
          <!-- Company Info -->
          <div class="footer-section">
            <div class="footer-logo">
              <img src="<?php echo $base_path; ?>assets/img/logo.png" alt="<?php echo SITE_NAME; ?> Logo">
              <h3><?php echo SITE_NAME; ?></h3>
            </div>
            <p class="footer-description">
              <?php echo SITE_TAGLINE; ?><br>
              Leading provider of premium nuclear solutions for strategic defense requirements.
            </p>
            <div class="footer-contact">
              <div class="contact-item">
                <i class="fas fa-envelope"></i>
                <span><?php echo SITE_EMAIL; ?></span>
              </div>
              <div class="contact-item">
                <i class="fas fa-phone"></i>
                <span>+63 00000000</span>
              </div>
              <div class="contact-item">
                <i class="fas fa-map-marker-alt"></i>
                <span>Strategic Defense Complex, Secure Location</span>
              </div>
            </div>
          </div>

          <!-- Quick Links -->
          <div class="footer-section">
            <h4>Quick Links</h4>
            <ul class="footer-links">
              <li><a href="<?php echo $base_path; ?>index.php">Home</a></li>
              <li><a href="<?php echo $base_path; ?>pages/shop/products.php">Products</a></li>
              <li><a href="<?php echo $base_path; ?>pages/user/about.php">About Us</a></li>
              <li><a href="<?php echo $base_path; ?>pages/user/contact.php">Contact</a></li>
              <li><a href="<?php echo $base_path; ?>pages/shop/deals.php">Special Deals</a></li>
              <li><a href="<?php echo $base_path; ?>pages/shop/new-arrivals.php">New Arrivals</a></li>
            </ul>
          </div>

          <!-- Services -->
          <div class="footer-section">
            <h4>Our Services</h4>
            <ul class="footer-links">
              <li><a href="#">Tactical Devices</a></li>
              <li><a href="#">Stealth Technology</a></li>
              <li><a href="#">Premium Models</a></li>
              <li><a href="#">Defense Systems</a></li>
              <li><a href="#">Custom Solutions</a></li>
              <li><a href="#">Expert Support</a></li>
            </ul>
          </div>

          <!-- Support -->
          <div class="footer-section">
            <h4>Support & Legal</h4>
            <ul class="footer-links">
              <li><a href="#">Technical Support</a></li>
              <li><a href="#">Installation Guide</a></li>
              <li><a href="#">Safety Protocols</a></li>
              <li><a href="#">Privacy Policy</a></li>
              <li><a href="#">Terms of Service</a></li>
              <li><a href="#">Export Compliance</a></li>
            </ul>
          </div>

          <!-- Newsletter -->
          <div class="footer-section">
            <h4>Stay Updated</h4>
            <p>Subscribe to our newsletter for the latest strategic defense updates and exclusive offers.</p>
            <form class="newsletter-form">
              <div class="newsletter-input">
                <input type="email" placeholder="Enter your email" required>
                <button type="submit">
                  <i class="fas fa-paper-plane"></i>
                </button>
              </div>
            </form>
            <div class="social-links">
              <a href="#" title="Facebook"><i class="fab fa-facebook"></i></a>
              <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
              <a href="#" title="LinkedIn"><i class="fab fa-linkedin"></i></a>
              <a href="#" title="YouTube"><i class="fab fa-youtube"></i></a>
            </div>
          </div>
        </div>

        <!-- Footer Bottom -->
        <div class="footer-bottom">
          <div class="footer-bottom-content">
            <p>&copy; <?php echo SITE_YEAR; ?> <?php echo SITE_NAME; ?>. All rights reserved. | Strategic Defense Solutions</p>
            <div class="footer-bottom-links">
              <a href="#">Privacy Policy</a>
              <a href="#">Terms of Service</a>
              <a href="#">Cookie Policy</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </footer>
</body>
</html> 