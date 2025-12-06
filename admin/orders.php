<?php
// admin/orders.php: Allows admin to view all placed user orders
include '../db_connect.php'; 
include '../admin_check.php'; 

$orders = [];
$error = '';
// ... Fetch all orders with basic info

$page_title = "Admin: View All Orders";
include '../_header.php';
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

    <h2>All Customer Orders (<?= count($orders) ?>)</h2>

    </div>

<?php 
include '../_footer.php';
?>