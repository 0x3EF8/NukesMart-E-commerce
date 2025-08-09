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

// Set page variables
$current_page = 'backup';
$page_title = 'Backup - ' . SITE_NAME;

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
                <h1>Backup & Restore</h1>
                <p>Manage database backups and system restore</p>
            </div>
        </div>

        <!-- Backup Options -->
        <div class="backup-sections">
            <div class="backup-card">
                <div class="backup-header">
                    <h3><i class="fas fa-download"></i> Create Backup</h3>
                </div>
                <div class="backup-content">
                    <p>Create a complete backup of your database and files</p>
                    <button class="btn-primary" onclick="createBackup()">
                        <i class="fas fa-download"></i>
                        Create Backup
                    </button>
                </div>
            </div>
            
            <div class="backup-card">
                <div class="backup-header">
                    <h3><i class="fas fa-upload"></i> Restore Backup</h3>
                </div>
                <div class="backup-content">
                    <p>Restore from a previous backup file</p>
                    <input type="file" id="backupFile" accept=".sql,.zip" style="display: none;">
                    <button class="btn-secondary" onclick="document.getElementById('backupFile').click()">
                        <i class="fas fa-upload"></i>
                        Choose File
                    </button>
                </div>
            </div>
            
            <div class="backup-card">
                <div class="backup-header">
                    <h3><i class="fas fa-history"></i> Backup History</h3>
                </div>
                <div class="backup-content">
                    <p>View and manage previous backups</p>
                    <button class="btn-secondary" onclick="viewBackupHistory()">
                        <i class="fas fa-history"></i>
                        View History
                    </button>
                </div>
            </div>
        </div>

        <!-- Backup Status -->
        <div class="backup-status">
            <h3>Backup Status</h3>
            <div class="status-grid">
                <div class="status-item">
                    <span class="status-label">Last Backup:</span>
                    <span class="status-value">Never</span>
                </div>
                <div class="status-item">
                    <span class="status-label">Backup Size:</span>
                    <span class="status-value">0 MB</span>
                </div>
                <div class="status-item">
                    <span class="status-label">Auto Backup:</span>
                    <span class="status-value">Disabled</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function createBackup() {
    if (confirm('Are you sure you want to create a backup? This may take a few minutes.')) {
        // Implement backup creation
        alert('Backup creation will be implemented here');
    }
}

function viewBackupHistory() {
    // Implement backup history view
    alert('Backup history will be implemented here');
}

document.getElementById('backupFile').addEventListener('change', function(e) {
    if (e.target.files.length > 0) {
        if (confirm('Are you sure you want to restore from this backup file? This will overwrite current data.')) {
            // Implement backup restoration
            alert('Backup restoration will be implemented here');
        }
    }
});
</script>

<?php include 'includes/footer.php'; ?>
