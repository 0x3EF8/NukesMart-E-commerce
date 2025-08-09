<?php
require_once '../config/config.php';

header('Content-Type: application/json');

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure session_id exists
if (!isset($_SESSION['session_id'])) {
    $_SESSION['session_id'] = uniqid();
}

$action = $_POST['action'] ?? '';
$product_id = $_POST['product_id'] ?? '';

switch ($action) {
    case 'add':
        $quantity = intval($_POST['quantity'] ?? 1);
        $product = getProductById($product_id);
        
        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
            exit;
        }
        
        if ($product['stock'] <= 0) {
            echo json_encode(['success' => false, 'message' => 'Product is out of stock']);
            exit;
        }
        
        // Check if item already in cart
        $user_id = $_SESSION['user']['id'] ?? null;
        $session_id = $_SESSION['session_id'];
        
        try {
            if ($user_id) {
                $existing = fetchOne("SELECT * FROM cart WHERE user_id = ? AND product_id = ?", [$user_id, $product_id]);
            } else {
                $existing = fetchOne("SELECT * FROM cart WHERE session_id = ? AND product_id = ?", [$session_id, $product_id]);
            }
            
            if ($existing) {
                // Update quantity
                $new_quantity = $existing['quantity'] + $quantity;
                if ($user_id) {
                    executeQuery("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?", [$new_quantity, $user_id, $product_id]);
                } else {
                    executeQuery("UPDATE cart SET quantity = ? WHERE session_id = ? AND product_id = ?", [$new_quantity, $session_id, $product_id]);
                }
            } else {
                // Add new item
                if ($user_id) {
                    executeQuery("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)", [$user_id, $product_id, $quantity]);
                } else {
                    executeQuery("INSERT INTO cart (session_id, product_id, quantity) VALUES (?, ?, ?)", [$session_id, $product_id, $quantity]);
                }
            }
            
            // Calculate cart count
            $cart_count = getCartCount($user_id, $session_id);
            
            echo json_encode(['success' => true, 'cart_count' => $cart_count]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
        break;
        
    case 'update':
        $quantity = intval($_POST['quantity'] ?? 1);
        $product = getProductById($product_id);
        
        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
            exit;
        }
        
        $user_id = $_SESSION['user']['id'] ?? null;
        $session_id = $_SESSION['session_id'];
        
        if ($quantity <= 0) {
            // Remove item
            if ($user_id) {
                executeQuery("DELETE FROM cart WHERE user_id = ? AND product_id = ?", [$user_id, $product_id]);
            } else {
                executeQuery("DELETE FROM cart WHERE session_id = ? AND product_id = ?", [$session_id, $product_id]);
            }
        } else {
            if ($quantity > $product['stock']) {
                echo json_encode(['success' => false, 'message' => 'Not enough stock available']);
                exit;
            }
            
            // Update quantity
            if ($user_id) {
                executeQuery("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?", [$quantity, $user_id, $product_id]);
            } else {
                executeQuery("UPDATE cart SET quantity = ? WHERE session_id = ? AND product_id = ?", [$quantity, $session_id, $product_id]);
            }
        }
        
        echo json_encode(['success' => true]);
        break;
        
    case 'remove':
        $user_id = $_SESSION['user']['id'] ?? null;
        $session_id = $_SESSION['session_id'];
        
        if ($user_id) {
            executeQuery("DELETE FROM cart WHERE user_id = ? AND product_id = ?", [$user_id, $product_id]);
        } else {
            executeQuery("DELETE FROM cart WHERE session_id = ? AND product_id = ?", [$session_id, $product_id]);
        }
        
        echo json_encode(['success' => true]);
        break;
        
    case 'get_count':
        $user_id = $_SESSION['user']['id'] ?? null;
        $session_id = $_SESSION['session_id'];
        $cart_count = getCartCount($user_id, $session_id);
        echo json_encode(['success' => true, 'cart_count' => $cart_count]);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?>
