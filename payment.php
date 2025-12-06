<?php
// payment.php: Collects simulated payment details before finalizing the order
include 'db_connect.php'; 

// Check for required session data
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "You must be logged in to proceed to payment.";
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$cart_total = 0.00;

// Fetch Cart Items and Calculate Total
$sql_fetch_total = "
    SELECT SUM(c.quantity * p.price) AS total
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
";
if ($stmt_fetch = $conn->prepare($sql_fetch_total)) {
    $stmt_fetch->bind_param("i", $user_id);
    $stmt_fetch->execute();
    $result = $stmt_fetch->get_result();
    if ($row = $result->fetch_assoc()) {
        $cart_total = $row['total'] ?? 0.00;
    }
    $stmt_fetch->close();
}

// Redirect if cart is empty
if ($cart_total <= 0) {
    $_SESSION['error'] = "Your cart is empty. Please add items before checking out.";
    header("Location: cart.php");
    exit();
}

// Set page title and include header
$page_title = "Payment Details | Store";
include '_header.php';
?>

<div class="container form-container">
    <h1>Payment Information</h1>
    
    <?php if (isset($_SESSION['error'])): ?>
        <p class="message error-message"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
    <?php endif; ?>

    <p class="message info-message">
        Order Total: <strong>$<?= number_format($cart_total, 2) ?></strong>
    </p>

    <form method="POST" action="checkout.php">
        
        <label for="card_number">Card Number:</label>
        <input type="text" id="card_number" name="card_number" placeholder="XXXX XXXX XXXX XXXX" pattern="\d{16}" title="16 digits required" required>
        
        <div class="form-row" style="display: flex; gap: 15px;">
            <div style="flex: 1;">
                <label for="expiry">Expiry Date (MM/YY):</label>
                <input type="text" id="expiry" name="expiry" placeholder="MM/YY" pattern="\d{2}/\d{2}" title="Format MM/YY" required>
            </div>
            <div style="flex: 1;">
                <label for="cvv">CVV:</label>
                <input type="text" id="cvv" name="cvv" placeholder="CVV" pattern="\d{3,4}" title="3 or 4 digits required" required>
            </div>
        </div>

        <label for="card_name">Name on Card:</label>
        <input type="text" id="card_name" name="card_name" required>
        
        <input type="hidden" name="total_price" value="<?= $cart_total ?>">

        <button type="submit" class="button primary-button" style="margin-top: 15px;">Place Order (Simulated Payment)</button>
    </form>
    
    <p style="margin-top: 15px;"><a href="cart.php">Back to Cart</a></p>
</div>

<?php 
include '_footer.php';
?>