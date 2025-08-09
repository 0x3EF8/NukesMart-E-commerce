<?php
// Include configuration
require_once '../../config/config.php';

// Check for maintenance mode
if (isMaintenanceMode() && (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin')) {
    header('Location: ../../pages/system/maintenance.php');
    exit();
}

// Set page variables
$current_page = 'contact';
$page_title = $site_pages['contact']['title'];

// Include header
include '../../includes/header.php';
include '../../includes/navigation.php';
?>

<main class="contact-page">
  <div class="container">
    <!-- Page Header -->
    <div class="page-header">
      <div class="breadcrumb">
        <a href="index.php">Home</a>
        <i class="fas fa-chevron-right"></i>
        <span>Contact</span>
      </div>
      <h1><?php echo $contact_content['page_header']['title']; ?></h1>
      <p><?php echo $contact_content['page_header']['subtitle']; ?></p>
    </div>

    <!-- Contact Layout -->
    <div class="contact-layout">
      <!-- Contact Form -->
      <div class="contact-form-section">
        <div class="form-header">
          <h2><?php echo $contact_content['contact_form']['title']; ?></h2>
          <p><?php echo $contact_content['contact_form']['subtitle']; ?></p>
        </div>
        
        <form class="contact-form" id="contact-form">
          <div class="form-row">
            <div class="form-group">
              <label for="name"><?php echo $contact_content['contact_form']['form_fields']['name']; ?></label>
              <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
              <label for="email"><?php echo $contact_content['contact_form']['form_fields']['email']; ?></label>
              <input type="email" id="email" name="email" required>
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label for="organization"><?php echo $contact_content['contact_form']['form_fields']['organization']; ?></label>
              <input type="text" id="organization" name="organization">
            </div>
            <div class="form-group">
              <label for="phone"><?php echo $contact_content['contact_form']['form_fields']['phone']; ?></label>
              <input type="tel" id="phone" name="phone">
            </div>
          </div>
          
          <div class="form-group">
            <label for="subject"><?php echo $contact_content['contact_form']['form_fields']['subject']; ?></label>
            <select id="subject" name="subject" required>
              <option value="">Select Subject</option>
              <?php foreach ($contact_content['contact_form']['subject_options'] as $value => $label): ?>
                <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          
          <div class="form-group">
            <label for="message"><?php echo $contact_content['contact_form']['form_fields']['message']; ?></label>
            <textarea id="message" name="message" rows="4" placeholder="Describe your inquiry..." required></textarea>
          </div>
          
          <div class="form-group">
            <label class="checkbox-label">
              <input type="checkbox" name="confidential" required>
              <span class="checkmark"></span>
              <?php echo $contact_content['contact_form']['form_fields']['confidential']; ?>
            </label>
          </div>
          
          <button type="submit" class="btn-submit">
            <i class="fas fa-paper-plane"></i>
            <?php echo $contact_content['contact_form']['submit_text']; ?>
          </button>
        </form>
      </div>

      <!-- Contact Info -->
      <div class="contact-info-section">
        <div class="info-header">
          <h2><?php echo $contact_content['contact_info']['title']; ?></h2>
          <p><?php echo $contact_content['contact_info']['subtitle']; ?></p>
        </div>
        
        <div class="info-cards">
          <?php foreach ($contact_content['contact_info']['cards'] as $card): ?>
            <div class="info-card">
              <div class="info-icon">
                <i class="<?php echo $card['icon']; ?>"></i>
              </div>
              <div class="info-content">
                <h3><?php echo $card['title']; ?></h3>
                <p><?php echo $card['content']; ?></p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- Emergency Contact -->
        <div class="emergency-contact">
          <h3><?php echo $contact_content['emergency_contact']['title']; ?></h3>
          <p><?php echo $contact_content['emergency_contact']['description']; ?></p>
          <div class="emergency-info">
            <?php foreach ($contact_content['emergency_contact']['items'] as $item): ?>
              <div class="emergency-item">
                <i class="<?php echo $item['icon']; ?>"></i>
                <span><?php echo $item['text']; ?></span>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>


  </div>
</main>

<script>
// Contact Form Submission
document.getElementById('contact-form').addEventListener('submit', function(e) {
  e.preventDefault();
  
  // Show success message
  alert('Thank you for your message. Our team will contact you within 24 hours.');
  
  // Reset form
  this.reset();
});
</script>

<?php include '../../includes/footer.php'; ?> 