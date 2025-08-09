<?php
// Include configuration
require_once '../config/config.php';

// Check if user is logged in and is admin
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'store_settings') {
        // Handle store settings
        $store_name = $_POST['store_name'] ?? '';
        $store_email = $_POST['store_email'] ?? '';
        $currency = $_POST['currency'] ?? '₱';
        
        $settings = [
            'site_name' => $store_name,
            'site_email' => $store_email,
            'currency' => $currency
        ];
        
        if (updateSettings($settings)) {
            $message = "Store settings updated successfully!";
            $message_type = "success";
        } else {
            $error = "Failed to update store settings.";
            $message_type = "error";
        }
    } elseif ($action === 'system_settings') {
        // Handle system settings
        $maintenance_mode = isset($_POST['maintenance_mode']) ? 1 : 0;
        $debug_mode = isset($_POST['debug_mode']) ? 1 : 0;
        
        $settings = [
            'maintenance_mode' => $maintenance_mode,
            'debug_mode' => $debug_mode
        ];
        
        if (updateSettings($settings)) {
            $message = "System settings updated successfully!";
            $message_type = "success";
        } else {
            $error = "Failed to update system settings.";
            $message_type = "error";
        }
    }
}

// Get current settings
$current_settings = [];
$all_settings = getAllSettings();
foreach ($all_settings as $setting) {
    $current_settings[$setting['setting_key']] = $setting['setting_value'];
}

// Set page variables
$current_page = 'settings';
$page_title = 'Settings - ' . SITE_NAME;

// Include admin header
include 'includes/header.php';
?>

<div class="admin-container">
    <!-- Sidebar -->
    <?php include 'includes/sidebar.php'; ?>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="admin-header">
            <div class="header-left">
                <h1>Settings</h1>
                <p>Manage system settings and configurations</p>
            </div>
        </div>

        <!-- Alerts -->
        <?php if (isset($message)): ?>
        <div class="alert alert-<?php echo $message_type; ?>">
            <?php echo $message; ?>
        </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
        <div class="alert alert-error">
            <?php echo $error; ?>
        </div>
        <?php endif; ?>

        <!-- Settings Sections -->
        <div class="settings-sections">
            <div class="settings-card">
                <div class="settings-header">
                    <h3><i class="fas fa-store"></i> Store Settings</h3>
                </div>
                <div class="settings-content">
                    <form method="POST" class="settings-form">
                        <input type="hidden" name="action" value="store_settings">
                        
                        <div class="form-group">
                            <label for="store_name">Store Name</label>
                            <input type="text" id="store_name" name="store_name" class="form-control" 
                                   value="<?php echo htmlspecialchars($current_settings['site_name'] ?? getCurrentSiteName()); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="store_email">Store Email</label>
                            <input type="email" id="store_email" name="store_email" class="form-control" 
                                   value="<?php echo htmlspecialchars($current_settings['site_email'] ?? getCurrentSiteEmail()); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="currency">Currency</label>
                            <select id="currency" name="currency" class="form-control">
                                <option value="₱" <?php echo (($current_settings['currency'] ?? getCurrentCurrency()) === '₱') ? 'selected' : ''; ?>>Philippine Peso (₱)</option>
                                <option value="$" <?php echo (($current_settings['currency'] ?? getCurrentCurrency()) === '$') ? 'selected' : ''; ?>>US Dollar ($)</option>
                                <option value="€" <?php echo (($current_settings['currency'] ?? getCurrentCurrency()) === '€') ? 'selected' : ''; ?>>Euro (€)</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i>
                            Save Store Settings
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="settings-card">
                <div class="settings-header">
                    <h3><i class="fas fa-cog"></i> System Settings</h3>
                </div>
                <div class="settings-content">
                    <form method="POST" class="settings-form">
                        <input type="hidden" name="action" value="system_settings">
                        
                        <div class="form-group">
                            <label for="maintenance_mode">Maintenance Mode</label>
                            <div class="toggle-switch">
                                <input type="checkbox" id="maintenance_mode" name="maintenance_mode" 
                                       <?php echo (($current_settings['maintenance_mode'] ?? '0') === '1') ? 'checked' : ''; ?>>
                                <label for="maintenance_mode" class="toggle-label"></label>
                                <span class="toggle-text"><?php echo (($current_settings['maintenance_mode'] ?? '0') === '1') ? 'Enabled' : 'Disabled'; ?></span>
                            </div>
                            <small class="form-text">When enabled, the site will be inaccessible to regular users.</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="debug_mode">Debug Mode</label>
                            <div class="toggle-switch">
                                <input type="checkbox" id="debug_mode" name="debug_mode" 
                                       <?php echo (($current_settings['debug_mode'] ?? '0') === '1') ? 'checked' : ''; ?>>
                                <label for="debug_mode" class="toggle-label"></label>
                                <span class="toggle-text"><?php echo (($current_settings['debug_mode'] ?? '0') === '1') ? 'Enabled' : 'Disabled'; ?></span>
                            </div>
                            <small class="form-text">When enabled, detailed error messages will be displayed.</small>
                        </div>
                        
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i>
                            Save System Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle switch functionality
document.querySelectorAll('.toggle-switch input[type="checkbox"]').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
        const toggleText = this.parentElement.querySelector('.toggle-text');
        toggleText.textContent = this.checked ? 'Enabled' : 'Disabled';
    });
});
</script>

<?php include 'includes/footer.php'; ?>
