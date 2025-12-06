<?php
// checkout.php: Processes the final order and handles database transaction
include 'db_connect.php'; 

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "You must be logged in to complete checkout.";
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
// Fetch total price from POST (sent by payment.php)
$final_total = isset($_POST['total_price']) ? floatval($_POST['total_price']) : 0.00; 

if ($final_total <= 0) {
    $_SESSION['error'] = "Invalid cart total. Cannot proceed with checkout.";
    header("Location: cart.php");
    exit();
}

//  Fetch cart items
$cart_items = [];
$sql_cart = "
    SELECT c.product_id, c.quantity, p.name, p.price, p.stock
    FROM cart c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = ?
";
if ($stmt_cart = $conn->prepare($sql_cart)) {
    $stmt_cart->bind_param("i", $user_id);
    $stmt_cart->execute();
    $result_cart = $stmt_cart->get_result();
    $cart_items = $result_cart->fetch_all(MYSQLI_ASSOC);
    $stmt_cart->close();
}

if (empty($cart_items)) {
    $_SESSION['error'] = "Your cart is empty. Nothing to checkout.";
    header("Location: cart.php");
    exit();
}

//  Start Database Transaction (for security and integrity)
$conn->begin_transaction();
$success = true;

try {
    //  Insert the new order into the 'orders' table
    $sql_order = "INSERT INTO orders (user_id, total_price) VALUES (?, ?)";
    if ($stmt_order = $conn->prepare($sql_order)) {
        $stmt_order->bind_param("id", $user_id, $final_total);
        if (!$stmt_order->execute()) {
            $success = false;
        }
        $order_id = $conn->insert_id; // Get the ID of the new order
        $stmt_order->close();
    } else {
        $success = false;
    }

    if ($success) {
        //  Insert items into 'order_items' and update product 'stock'
        $sql_item = "INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)";
        $sql_stock = "UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?";

        foreach ($cart_items as $item) {
            $product_id = $item['product_id'];
            $quantity = $item['quantity'];
            $price = $item['price'];
            $stock = $item['stock'];

            // Insert into order_items
            if ($stmt_item = $conn->prepare($sql_item)) {
                $stmt_item->bind_param("iiid", $order_id, $product_id, $quantity, $price);
                if (!$stmt_item->execute()) {
                    $success = false;
                    break;
                }
                $stmt_item->close();
            } else {
                $success = false;
                break;
            }

            // Update product stock (ensure sufficient stock)
            if ($stmt_stock = $conn->prepare($sql_stock)) {
                $stmt_stock->bind_param("iii", $quantity, $product_id, $quantity);
                if (!$stmt_stock->execute() || $conn->affected_rows === 0) { 
                    $success = false;
                    $_SESSION['error'] = "Inventory error for product: " . htmlspecialchars($item['name']) . ". Order cancelled.";
                    break;
                }
                $stmt_stock->close();
            } else {
                $success = false;
                break;
            }
        }
    }

    if ($success) {
        // Clear the user's cart
        $sql_clear = "DELETE FROM cart WHERE user_id = ?";
        if ($stmt_clear = $conn->prepare($sql_clear)) {
            $stmt_clear->bind_param("i", $user_id);
            if (!$stmt_clear->execute()) {
                $success = false;
            }
            $stmt_clear->close();
        } else {
            $success = false;
        }
    }

    //  Commit or Rollback the transaction
    if ($success) {
        $conn->commit();
        $_SESSION['success'] = "Order #{$order_id} placed successfully! Thank you for your purchase.";
        header("Location: order_history.php");
    } else {
        $conn->rollback();
        // Redirect back to payment page with the specific error
        if (!isset($_SESSION['error'])) {
             $_SESSION['error'] = "Order processing failed due to a database error. Please try again.";
        }
        header("Location: payment.php");
    }

} catch (Exception $e) {
    // Catch any unexpected PHP or database exceptions
    $conn->rollback();
    $_SESSION['error'] = "An unexpected error occurred during checkout. Please contact support. Error: " . $e->getMessage();
    header("Location: payment.php");
}

exit();
?>