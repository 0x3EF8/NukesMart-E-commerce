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
$current_page = 'reports';
$page_title = 'Reports - ' . SITE_NAME;

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
                <h1>Reports</h1>
                <p>Generate and view business reports</p>
            </div>
        </div>

        <!-- Reports Grid -->
        <div class="reports-grid">
            <div class="report-card">
                <div class="report-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="report-content">
                    <h3>Sales Report</h3>
                    <p>Comprehensive sales analysis and trends</p>
                    <button class="btn-primary" onclick="generateReport('sales')">
                        <i class="fas fa-download"></i>
                        Generate Report
                    </button>
                </div>
            </div>
            
            <div class="report-card">
                <div class="report-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="report-content">
                    <h3>Customer Report</h3>
                    <p>Customer demographics and behavior analysis</p>
                    <button class="btn-primary" onclick="generateReport('customers')">
                        <i class="fas fa-download"></i>
                        Generate Report
                    </button>
                </div>
            </div>
            
            <div class="report-card">
                <div class="report-icon">
                    <i class="fas fa-box"></i>
                </div>
                <div class="report-content">
                    <h3>Product Report</h3>
                    <p>Product performance and inventory analysis</p>
                    <button class="btn-primary" onclick="generateReport('products')">
                        <i class="fas fa-download"></i>
                        Generate Report
                    </button>
                </div>
            </div>
            
            <div class="report-card">
                <div class="report-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="report-content">
                    <h3>Order Report</h3>
                    <p>Order processing and fulfillment analysis</p>
                    <button class="btn-primary" onclick="generateReport('orders')">
                        <i class="fas fa-download"></i>
                        Generate Report
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function generateReport(type) {
    // Implement report generation logic
    window.location.href = `generate-report.php?type=${type}`;
}
</script>

<?php include 'includes/footer.php'; ?>
