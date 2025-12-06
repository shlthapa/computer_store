<?php
// cart.php: Allows user to view, edit, and proceed from their shopping cart
include 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "You must be logged in to view your cart.";
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$cart_items = [];
$cart_total = 0.00;

// Fetch cart items using prepared statement
$sql = "
    SELECT c.product_id, c.quantity, p.name, p.price, p.image_url, p.stock 
    FROM cart c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = ?
";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $cart_items = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Calculate total
foreach ($cart_items as $item) {
    $cart_total += ($item['quantity'] * $item['price']);
}

$page_title = "Your Shopping Cart | Store";
include '_header.php';
?>

<div class="container">
    <h1>Your Shopping Cart</h1>

    <?php if (isset($_SESSION['success'])): ?>
        <p class="message success-message"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></p>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <p class="message error-message"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
    <?php endif; ?>

    <?php if (empty($cart_items)): ?>
        <p class="message info-message">Your cart is empty. <a href="products.php">Start shopping now!</a></p>
    <?php else: ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items as $item): ?>
                <tr>
                    <td class="product-info">
                        <img src="<?= htmlspecialchars($item['image_url'] ?? 'assets/images/default.jpg') ?>" 
                             alt="<?= htmlspecialchars($item['name']) ?>" class="cart-img" style="width: 50px;">
                        <a href="product.php?id=<?= $item['product_id'] ?>"><?= htmlspecialchars($item['name']) ?></a>
                    </td>
                    <td>$<?= number_format($item['price'], 2) ?></td>
                    <td>
                        <form action="add_to_cart.php" method="POST" class="update-cart-form" style="display: flex; align-items: center; gap: 5px;">
                            <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                            <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" max="<?= $item['stock'] ?>" required class="cart-qty-input" style="width: 60px;">
                            <button type="submit" name="action" value="update" class="button secondary-button">Update</button>
                        </form>
                    </td>
                    <td>$<?= number_format($item['quantity'] * $item['price'], 2) ?></td>
                    <td>
                        <form action="add_to_cart.php" method="POST">
                            <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                            <button type="submit" name="action" value="remove" class="button delete-button">Remove</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="cart-total">
            <h3>Cart Total: <span>$<?= number_format($cart_total, 2) ?></span></h3>
        </div>

        <div class="checkout-actions">
            <a href="payment.php" class="button primary-button">Proceed to Payment</a>
        </div>
    <?php endif; ?>
</div>

<?php 
// Include reusable footer and close connection
include '_footer.php';
?>