<?php
// index.php: The main landing page with the video background
include 'db_connect.php'; 
$page_title = "Home | Online Computer Store"; 

$featured_products = [];
// Fetch featured products (limit 3 or 4 for consistency)
$sql_featured = "SELECT id, name, price, image_url, description FROM products ORDER BY id DESC LIMIT 4"; 
$result_featured = $conn->query($sql_featured);

if ($result_featured && $result_featured->num_rows > 0) {
    $featured_products = $result_featured->fetch_all(MYSQLI_ASSOC);
}

$greeting = "Welcome to the Online Computer Store!";
if (isset($_SESSION['user_name'])) {
    $greeting = "Hello, " . htmlspecialchars($_SESSION['user_name']) . "!";
}

include '_header.php'; 
?>

<div class="hero-video-container">
    
    <video autoplay muted loop id="video-background" class="video-background">
        <source src="assets/videos/laptop_loop.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>
    
    <div class="hero-content-overlay">
        <h2><?= htmlspecialchars($greeting) ?></h2>
        <p>Your one-stop shop for the latest in computer technology.</p>
        <a href="products.php" class="button primary-button">Browse Our Inventory</a>
    </div>

</div>

<?php if (!empty($featured_products)): ?>
    <div class="container featured-section">
        <h2>🔥 Featured Products</h2>
        <div class="product-grid">
            <?php foreach ($featured_products as $product): ?>
                <div class="product-card">
                    <img src="<?= htmlspecialchars($product['image_url'] ?? 'assets/images/default.jpg') ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                    <h3><?= htmlspecialchars($product['name']) ?></h3>
                    <p class="price">$<?= number_format($product['price'], 2) ?></p>
                    <a href="product.php?id=<?= $product['id'] ?>" class="button secondary-button">View Details</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<?php 
include '_footer.php';
?>