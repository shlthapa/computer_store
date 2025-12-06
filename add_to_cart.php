<?php
// add_to_cart.php: Handles adding, updating, and removing items from the cart.
include 'db_connect.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "You must be logged in to modify your cart.";
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
$action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING); // 'add', 'update', or 'remove'
$quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

if (!$action) {
    $action = 'add';
}


//  INPUT VALIDATION

if (!$product_id) {
    $_SESSION['error'] = "Invalid product ID.";
    header("Location: cart.php");
    exit();
}

// Fetch the product to check stock and existence
$sql_product = "SELECT stock, name FROM products WHERE id = ?";
$stock = 0;
$product_name = '';

if ($stmt_prod = $conn->prepare($sql_product)) {
    $stmt_prod->bind_param("i", $product_id);
    $stmt_prod->execute();
    $result_prod = $stmt_prod->get_result();
    
    if ($row = $result_prod->fetch_assoc()) {
        $stock = $row['stock'];
        $product_name = $row['name'];
    }
    $stmt_prod->close();
}

if ($stock === 0 && $action !== 'remove') {
    $_SESSION['error'] = "Item is out of stock.";
    header("Location: cart.php");
    exit();
}



// REMOVE ACTION (DELETE item from cart)

if ($action === 'remove') {
    $sql_remove = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
    if ($stmt_remove = $conn->prepare($sql_remove)) {
        $stmt_remove->bind_param("ii", $user_id, $product_id);
        if ($stmt_remove->execute()) {
            $_SESSION['success'] = htmlspecialchars($product_name) . " removed from cart.";
        } else {
            $_SESSION['error'] = "Failed to remove item from cart.";
        }
        $stmt_remove->close();
    }
    header("Location: cart.php");
    exit();
}


// ADD / UPDATE ACTION (Requires quantity)

if (!$quantity || $quantity < 1) {
    $_SESSION['error'] = "Invalid quantity specified.";
    header("Location: cart.php");
    exit();
}

$sql_check = "SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?";
$current_quantity = 0;

if ($stmt_check = $conn->prepare($sql_check)) {
    $stmt_check->bind_param("ii", $user_id, $product_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    
    if ($row = $result_check->fetch_assoc()) {
        $current_quantity = $row['quantity'];
    }
    $stmt_check->close();
}


$new_quantity = $quantity; 
if ($action === 'add') {
    $new_quantity = $current_quantity + $quantity; 
}

if ($new_quantity > $stock) {
    $_SESSION['error'] = "Cannot add/update. Only " . $stock . " units of " . htmlspecialchars($product_name) . " are available.";
    header("Location: cart.php");
    exit();
}



//  DATABASE EXECUTION


if ($current_quantity > 0) {
    $sql_update = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
    if ($stmt_update = $conn->prepare($sql_update)) {
        $stmt_update->bind_param("iii", $new_quantity, $user_id, $product_id);
        $stmt_update->execute();
        $_SESSION['success'] = "Cart quantity for " . htmlspecialchars($product_name) . " updated to " . $new_quantity . ".";
        $stmt_update->close();
    }
} else {
    $sql_insert = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
    if ($stmt_insert = $conn->prepare($sql_insert)) {
        $stmt_insert->bind_param("iii", $user_id, $product_id, $quantity);
        $stmt_insert->execute();
        $_SESSION['success'] = htmlspecialchars($product_name) . " added to cart.";
        $stmt_insert->close();
    }
}

// Redirect back to the cart
header("Location: cart.php");
exit();
?>