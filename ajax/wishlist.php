<?php
require_once '../config/config.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';
$product_id = $_POST['product_id'] ?? '';

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user']['id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to use wishlist']);
    exit;
}

$user_id = $_SESSION['user']['id'];

switch ($action) {
    case 'add':
        $product = getProductById($product_id);
        
        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
            exit;
        }
        
        // Check if already in wishlist
        $existing = fetchOne("SELECT * FROM wishlist WHERE user_id = ? AND product_id = ?", [$user_id, $product_id]);
        
        if (!$existing) {
            executeQuery("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)", [$user_id, $product_id]);
        }
        
        $wishlist_count = getWishlistCount($user_id);
        echo json_encode(['success' => true, 'wishlist_count' => $wishlist_count]);
        break;
        
    case 'remove':
        executeQuery("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?", [$user_id, $product_id]);
        
        $wishlist_count = getWishlistCount($user_id);
        echo json_encode(['success' => true, 'wishlist_count' => $wishlist_count]);
        break;
        
    case 'toggle':
        $product = getProductById($product_id);
        
        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
            exit;
        }
        
        // Check if already in wishlist
        $existing = fetchOne("SELECT * FROM wishlist WHERE user_id = ? AND product_id = ?", [$user_id, $product_id]);
        
        if ($existing) {
            // Remove from wishlist
            executeQuery("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?", [$user_id, $product_id]);
            $in_wishlist = false;
            $message = 'Product removed from wishlist!';
        } else {
            // Add to wishlist
            executeQuery("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)", [$user_id, $product_id]);
            $in_wishlist = true;
            $message = 'Product added to wishlist!';
        }
        
        $wishlist_count = getWishlistCount($user_id);
        echo json_encode([
            'success' => true, 
            'wishlist_count' => $wishlist_count,
            'in_wishlist' => $in_wishlist,
            'message' => $message
        ]);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?>
