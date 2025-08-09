<?php
// Include configuration
require_once '../../config/config.php';

// Initialize variables
$error = '';

// Check if user is already logged in
if (isset($_SESSION['user'])) {
    if ($_SESSION['user']['role'] === 'admin') {
        header('Location: ../../admin/index.php');
    } else {
        header('Location: ../../index.php');
    }
    exit();
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (!empty($email) && !empty($password)) {
        $user = getUserByEmail($email);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            
            // Redirect based on user role
            if ($user['role'] === 'admin') {
                header('Location: ../../admin/index.php');
            } else {
                header('Location: ../../index.php');
            }
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Please enter both email and password.";
    }
}

// Set page variables
$current_page = 'login';
$page_title = $site_pages['login']['title'];

// Include header
include '../../includes/header.php';
include '../../includes/navigation.php';
?>

<main class="auth-page">
  <div class="container">
    <div class="auth-form">
      <h1>Login to Your Account</h1>
      
      <?php if ($error): ?>
        <div class="error-message">
          <i class="fas fa-exclamation-circle"></i>
          <?php echo htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>
      
      <form method="POST" class="login-form">
        <div class="form-group">
          <label for="email">Email Address</label>
          <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required>
        </div>
        
        <div class="form-options">
          <label class="checkbox-label">
            <input type="checkbox" name="remember">
            <span class="checkmark"></span>
            Remember me
          </label>
          <a href="#" class="forgot-password">Forgot Password?</a>
        </div>
        
        <button type="submit" class="btn-primary">Login</button>
      </form>
      
      <div class="auth-footer">
        <p>Don't have an account? <a href="register.php">Sign up here</a></p>
      </div>
      
      <div class="demo-info">
        <h3>Demo Accounts</h3>
        <p>For testing purposes, use these credentials:</p>
        <div class="demo-credentials">
          <div><strong>Customer Account:</strong></div>
          <div><strong>Email:</strong> demo@nukemart.com</div>
          <div><strong>Password:</strong> password123</div>
          <br>
          <div><strong>Admin Account:</strong></div>
          <div><strong>Email:</strong> admin@nukemart.com</div>
          <div><strong>Password:</strong> admin123</div>
        </div>
      </div>
    </div>
  </div>
</main>

<?php include '../../includes/footer.php'; ?>
