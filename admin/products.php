<?php
// admin/products.php: Allows admin to add, edit, and delete products
include '../db_connect.php'; 
include '../admin_check.php'; 

$message = '';
$edit_product = null;
// ... (POST and GET request handling logic is omitted for brevity, but it stays the same)

// C. Fetch All Products for Display
$products = [];
$sql_fetch_all = "SELECT id, name, price, stock, category FROM products ORDER BY id DESC";
$result_all = $conn->query($sql_fetch_all);
if ($result_all) {
    $products = $result_all->fetch_all(MYSQLI_ASSOC);
}

$page_title = "Admin: Product Management";
include '../_header.php'; // Use '../' to go up one folder
?>

<div class="container admin-page">
    <h1>Admin Dashboard</h1>
    <div class="admin-nav">
        <a href="products.php">Manage Products</a> | 
        <a href="orders.php">View Orders</a> | 
        <a href="../index.php">View Store</a> | 
        <a href="../logout.php">Logout</a>
    </div>
    <hr>

    <?php if ($message): ?>
        <p class="message success-message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <hr>
    
    </div>

<?php 
include '../_footer.php'; // Use '../' to go up one folder
?>