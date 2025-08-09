<?php
// Include configuration
require_once 'config/config.php';

// Check for maintenance mode
if (isMaintenanceMode() && (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin')) {
    header('Location: pages/system/maintenance.php');
    exit();
}

// Set page variables
$current_page = 'home';
$page_title = $site_pages['home']['title'];

// Get categories from database
$categories = getAllCategories();

// Include header
include 'includes/header.php';
include 'includes/navigation.php';
?>

<main>
  <!-- Hero Section -->
  <section class="hero">
    <div class="hero-video">
      <video autoplay muted loop playsinline>
        <source src="<?php echo $home_content['hero']['video_src']; ?>" type="video/mp4">
        Your browser does not support the video tag.
      </video>
    </div>
    <div class="hero-overlay"></div>
    <div class="hero-content">
      <h1><?php echo $home_content['hero']['title']; ?></h1>
      <p><?php echo $home_content['hero']['subtitle']; ?></p>
      <a href="<?php echo $home_content['hero']['button_link']; ?>" class="btn-primary"><?php echo $home_content['hero']['button_text']; ?></a>
    </div>
  </section>

  <!-- Moving Flag Banner -->
  <section class="flag-banner">
    <div class="banner-header">
      <h2><?php echo $home_content['flag_banner']['title']; ?></h2>
      <p><?php echo $home_content['flag_banner']['subtitle']; ?></p>
    </div>
    <div class="flag-container">
      <div class="flag-track">
        <?php foreach ($flags as $flag): ?>
          <div class="flag-item">
            <img src="<?php echo getImageUrl('flags/' . $flag['image']); ?>" alt="<?php echo $flag['name']; ?> Flag" class="flag-image">
            <span class="flag-name"><?php echo $flag['name']; ?></span>
            <div class="customer-rating">
              <div class="stars">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                  <i class="fas fa-star"></i>
                <?php endfor; ?>
              </div>
              <span class="rating-score"><?php echo $flag['rating']; ?>/5.0</span>
            </div>
            <div class="customer-testimonial">
              <p><?php echo $flag['testimonial']; ?></p>
              <span class="customer-name">- <?php echo $flag['customer']; ?></span>
            </div>
          </div>
        <?php endforeach; ?>
        <!-- Duplicate flags for seamless loop -->
        <?php foreach ($flags as $flag): ?>
          <div class="flag-item">
            <img src="<?php echo getImageUrl('flags/' . $flag['image']); ?>" alt="<?php echo $flag['name']; ?> Flag" class="flag-image">
            <span class="flag-name"><?php echo $flag['name']; ?></span>
            <div class="customer-rating">
              <div class="stars">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                  <i class="fas fa-star"></i>
                <?php endfor; ?>
              </div>
              <span class="rating-score"><?php echo $flag['rating']; ?>/5.0</span>
            </div>
            <div class="customer-testimonial">
              <p><?php echo $flag['testimonial']; ?></p>
              <span class="customer-name">- <?php echo $flag['customer']; ?></span>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- About Section -->
  <section class="about-section">
    <div class="container">
      <div class="about-content">
        <div class="about-text">
          <h2><?php echo $home_content['about_section']['title']; ?></h2>
          <p><?php echo $home_content['about_section']['description']; ?></p>
          
          <div class="features-list">
            <?php foreach ($home_content['features']['left_column'] as $feature): ?>
              <div class="feature-item">
                <i class="<?php echo $feature['icon']; ?>"></i>
                <div>
                  <h3><?php echo $feature['title']; ?></h3>
                  <p><?php echo $feature['description']; ?></p>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
          
          <a href="<?php echo $home_content['about_section']['button_link']; ?>" class="btn-primary"><?php echo $home_content['about_section']['button_text']; ?></a>
        </div>
        
        <div class="about-image">
          <div class="stats-grid">
            <?php foreach ($home_content['stats'] as $stat): ?>
              <div class="stat-card">
                <div class="stat-number"><?php echo $stat['number']; ?></div>
                <div class="stat-label"><?php echo $stat['label']; ?></div>
              </div>
            <?php endforeach; ?>
          </div>
          
          <div class="right-features">
            <?php foreach ($home_content['features']['right_column'] as $feature): ?>
              <div class="feature-item">
                <i class="<?php echo $feature['icon']; ?>"></i>
                <div>
                  <h3><?php echo $feature['title']; ?></h3>
                  <p><?php echo $feature['description']; ?></p>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- What We Offer Section -->
  <section class="what-we-offer">
    <div class="container">
      <div class="section-header">
        <h2><?php echo $home_content['what_we_offer']['title']; ?></h2>
        <p><?php echo $home_content['what_we_offer']['subtitle']; ?></p>
      </div>
      
      <div class="offerings-grid">
        <?php foreach ($home_content['what_we_offer']['offerings'] as $offering): ?>
          <div class="offering-card">
            <div class="offering-icon">
              <i class="<?php echo $offering['icon']; ?>"></i>
            </div>
            <h3><?php echo $offering['title']; ?></h3>
            <p><?php echo $offering['description']; ?></p>
            <ul class="offering-features">
              <?php foreach ($offering['features'] as $feature): ?>
                <li><?php echo $feature; ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Call to Action -->
  <section class="cta-section">
    <div class="container">
      <div class="cta-content">
        <h2><?php echo $home_content['cta_section']['title']; ?></h2>
        <p><?php echo $home_content['cta_section']['description']; ?></p>
        <div class="cta-buttons">
          <?php foreach ($home_content['cta_section']['buttons'] as $button): ?>
            <a href="<?php echo $button['link']; ?>" class="<?php echo $button['class']; ?>"><?php echo $button['text']; ?></a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include 'includes/footer.php'; ?> 