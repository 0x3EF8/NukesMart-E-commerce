<?php
// Include configuration
require_once '../../config/config.php';

// Check for maintenance mode
if (isMaintenanceMode() && (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin')) {
    header('Location: ../../pages/system/maintenance.php');
    exit();
}

// Set page variables
$current_page = 'about';
$page_title = $site_pages['about']['title'];

// Include header
include '../../includes/header.php';
include '../../includes/navigation.php';
?>

<main class="about-page">
  <div class="container">
    <!-- Page Header -->
    <div class="page-header">
      <div class="breadcrumb">
        <a href="index.php">Home</a>
        <i class="fas fa-chevron-right"></i>
        <span>About Us</span>
      </div>
      <h1><?php echo $about_content['page_header']['title']; ?></h1>
      <p><?php echo $about_content['page_header']['subtitle']; ?></p>
    </div>

    <!-- Hero Section -->
    <section class="about-hero">
      <div class="hero-content">
        <div class="hero-text">
          <h2><?php echo $about_content['hero_section']['title']; ?></h2>
          <p><?php echo $about_content['hero_section']['description']; ?></p>
          <div class="hero-stats">
            <?php foreach ($about_content['hero_section']['stats'] as $stat): ?>
              <div class="stat-item">
                <span class="stat-number"><?php echo $stat['number']; ?></span>
                <span class="stat-label"><?php echo $stat['label']; ?></span>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="hero-image">
          <img src="<?php echo getImageUrl('logo.png'); ?>" alt="<?php echo SITE_NAME; ?> Logo">
        </div>
      </div>
    </section>

    <!-- Mission & Vision -->
    <section class="mission-vision">
      <div class="mission-card">
        <div class="card-icon">
          <i class="<?php echo $about_content['mission_vision']['mission']['icon']; ?>"></i>
        </div>
        <h3><?php echo $about_content['mission_vision']['mission']['title']; ?></h3>
        <p><?php echo $about_content['mission_vision']['mission']['description']; ?></p>
      </div>
      
      <div class="vision-card">
        <div class="card-icon">
          <i class="<?php echo $about_content['mission_vision']['vision']['icon']; ?>"></i>
        </div>
        <h3><?php echo $about_content['mission_vision']['vision']['title']; ?></h3>
        <p><?php echo $about_content['mission_vision']['vision']['description']; ?></p>
      </div>
    </section>

    <!-- Core Values -->
    <section class="core-values">
      <div class="section-header">
        <h2><?php echo $about_content['core_values']['title']; ?></h2>
        <p><?php echo $about_content['core_values']['subtitle']; ?></p>
      </div>
      
      <div class="values-grid">
        <?php foreach ($about_content['core_values']['values'] as $value): ?>
          <div class="value-card">
            <div class="value-icon">
              <i class="<?php echo $value['icon']; ?>"></i>
            </div>
            <h4><?php echo $value['title']; ?></h4>
            <p><?php echo $value['description']; ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- Technology & Innovation -->
    <section class="technology-section">
      <div class="tech-content">
        <div class="tech-text">
          <h2><?php echo $about_content['technology_section']['title']; ?></h2>
          <p><?php echo $about_content['technology_section']['description']; ?></p>
          
          <div class="tech-features">
            <?php foreach ($about_content['technology_section']['features'] as $feature): ?>
              <div class="tech-feature">
                <i class="<?php echo $feature['icon']; ?>"></i>
                <div>
                  <h4><?php echo $feature['title']; ?></h4>
                  <p><?php echo $feature['description']; ?></p>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
        
        <div class="tech-image">
          <div class="tech-visual">
            <i class="fas fa-atom"></i>
          </div>
        </div>
      </div>
    </section>

    <!-- Global Presence -->
    <section class="global-presence">
      <div class="section-header">
        <h2><?php echo $about_content['global_presence']['title']; ?></h2>
        <p><?php echo $about_content['global_presence']['subtitle']; ?></p>
      </div>
      
      <div class="presence-stats">
        <?php foreach ($about_content['global_presence']['stats'] as $stat): ?>
          <div class="presence-stat">
            <div class="stat-circle">
              <span class="stat-number"><?php echo $stat['number']; ?></span>
            </div>
            <h4><?php echo $stat['title']; ?></h4>
            <p><?php echo $stat['description']; ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- Call to Action -->
    <section class="about-cta">
      <div class="cta-content">
        <h2><?php echo $about_content['cta_section']['title']; ?></h2>
        <p><?php echo $about_content['cta_section']['description']; ?></p>
        <div class="cta-buttons">
          <?php foreach ($about_content['cta_section']['buttons'] as $button): ?>
            <a href="<?php echo $button['link']; ?>" class="<?php echo $button['class']; ?>"><?php echo $button['text']; ?></a>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  </div>
</main>

<?php include '../../includes/footer.php'; ?> 