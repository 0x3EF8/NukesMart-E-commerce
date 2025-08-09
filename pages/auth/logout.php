<?php
// Include configuration
require_once '../../config/config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clear all session data
session_destroy();

// Redirect to home page
header('Location: ../../index.php');
exit();
?>
