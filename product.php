<?php
// product.php: Displays the details of a single product
include 'db_connect.php'; 

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$product_id = $_GET['id'];
$product = null;

$sql = "SELECT * FROM products WHERE id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $product = $result->fetch_assoc();
    }
    $stmt->close();
}

if (!$product) {
    header("Location: products.php");
    exit();
}

$page_title = htmlspecialchars($product['name']) . " | Store";
include '_header.php';
?>

<div class="container product-detail-page">
    <a href="products.php" class="back-link">&larr; Back to Products</a>
    
    <div class="product-detail-content">
        <div class="product-image-container">
            <img src="<?= htmlspecialchars($product['image_url'] ?? 'assets/images/default.jpg') ?>" alt="<?= htmlspecialchars($product['name']) ?>">
        </div>
        
        <div class="product-info">
            <h2><?= htmlspecialchars($product['name']) ?></h2>
            <p class="category">Category: <?= htmlspecialchars($product['category']) ?></p>
            <p class="price">$<?= number_format($product['price'], 2) ?></p>
            <p class="stock-info">
                <strong>Stock:</strong> 
                <?php if ($product['stock'] > 0): ?>
                    <span style="color: green;">In Stock (<?= $product['stock'] ?> units)</span>
                <?php else: ?>
                    <span style="color: red;">Out of Stock</span>
                <?php endif; ?>
            </p>
            
            <h3>Description</h3>
            <p class="description-text"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
            
            <?php if ($product['stock'] > 0 && isset($_SESSION['user_id'])): ?>
                <form method="POST" action="add_to_cart.php" id="add-to-cart-form"> 
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <div class="quantity-input">
                        <label for="quantity">Quantity:</label>
                        <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?= $product['stock'] ?>" required>
                    </div>
                    <button type="submit" class="button primary-button">Add to Cart</button>
                </form>
            <?php elseif (!isset($_SESSION['user_id'])): ?>
                <p class="message info-message"><a href="login.php">Log in</a> to purchase this item.</p>
            <?php else: ?>
                <button class="button disabled-button" disabled>Out of Stock</button>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php 
include '_footer.php';
?>