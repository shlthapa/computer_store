<?php
// products.php: Fetches and displays all available products
include 'db_connect.php'; 
$page_title = "Browse Products | Store";

$sql = "SELECT id, name, price, image_url, category FROM products ORDER BY name ASC";
$result = $conn->query($sql);

$products = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

include '_header.php';
?>
    
<div class="container">
    <h2>All Products</h2>

    <?php if (!empty($products)): ?>
        <div class="product-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <img src="<?= htmlspecialchars($product['image_url'] ?? 'assets/images/default.jpg') ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                    
                    <h3><?= htmlspecialchars($product['name']) ?></h3>
                    <p class="category"><?= htmlspecialchars($product['category']) ?></p>
                    <p class="price">$<?= number_format($product['price'], 2) ?></p>
                    
                    <a href="product.php?id=<?= $product['id'] ?>" class="button secondary-button">View Details</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="message info-message">No products found in the database.</p>
    <?php endif; ?>
</div>

<?php 
include '_footer.php';
?>