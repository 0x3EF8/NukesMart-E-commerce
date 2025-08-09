<?php
// Include configuration
require_once '../../config/config.php';

// Initialize variables
$error = '';
$success = '';

// Check if user is already logged in
if (isset($_SESSION['user'])) {
    header('Location: ../../index.php');
    exit();
}

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (!empty($name) && !empty($email) && !empty($password)) {
        if ($password === $confirm_password) {
            // Check if email already exists
            $existing_user = getUserByEmail($email);
            if ($existing_user) {
                $error = "Email already registered. Please use a different email or login.";
            } else {
                // Create new user
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'customer')";
                if (executeQuery($sql, [$name, $email, $hashed_password])) {
                    $success = "Registration successful! Please login with your new account.";
                } else {
                    $error = "Registration failed. Please try again.";
                }
            }
        } else {
            $error = "Passwords do not match.";
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}

// Set page variables
$current_page = 'register';
$page_title = $site_pages['register']['title'];

// Include header
include '../../includes/header.php';
include '../../includes/navigation.php';
?>

<main class="auth-page">
  <div class="container">
    <div class="auth-form">
      <h1>Create Your Account</h1>
      
      <?php if ($error): ?>
        <div class="error-message">
          <i class="fas fa-exclamation-circle"></i>
          <?php echo htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>
      
      <?php if ($success): ?>
        <div class="success-message">
          <i class="fas fa-check-circle"></i>
          <?php echo htmlspecialchars($success); ?>
        </div>
      <?php endif; ?>
      
      <form method="POST" class="register-form">
        <div class="form-group">
          <label for="name">Full Name</label>
          <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
          <label for="email">Email Address</label>
          <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required>
          <small>Must be at least 6 characters long</small>
        </div>
        
        <div class="form-group">
          <label for="confirm_password">Confirm Password</label>
          <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        
        <div class="form-options">
          <label class="checkbox-label">
            <input type="checkbox" name="terms" required>
            <span class="checkmark"></span>
            I agree to the <a href="#" class="terms-link">Terms of Service</a> and <a href="#" class="terms-link">Privacy Policy</a>
          </label>
        </div>
        
        <button type="submit" class="btn-primary">Create Account</button>
      </form>
      
      <div class="auth-footer">
        <p>Already have an account? <a href="login.php">Login here</a></p>
      </div>
      
      <div class="benefits">
        <h3>Why Create an Account?</h3>
        <ul>
          <li><i class="fas fa-shopping-cart"></i> Save items to your wishlist</li>
          <li><i class="fas fa-box"></i> Track your orders</li>
          <li><i class="fas fa-shipping-fast"></i> Faster checkout process</li>
          <li><i class="fas fa-tag"></i> Access to exclusive deals</li>
        </ul>
      </div>
    </div>
  </div>
</main>

<?php include '../../includes/footer.php'; ?>
