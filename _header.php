<?php
// _header.php: Reusable HTML header and navigation structure
// NOTE: This file assumes db_connect.php (which starts the session) has already been included.

// Check if the current script is in the admin folder
$is_admin_page = (strpos($_SERVER['PHP_SELF'], '/admin/') !== false);

// Determine the correct path prefix for links
$path_prefix = $is_admin_page ? '../' : ''; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Online Computer Store'; ?></title>
    <link rel="stylesheet" href="<?= $path_prefix ?>assets/css/style.css">
    <script src="<?= $path_prefix ?>assets/js/script.js" defer></script>
</head>
<body>
    <header>
        <div class="container">
            <h1>The Computer Store</h1>
            <nav>
                <a href="<?= $path_prefix ?>index.php">Home</a>
                <a href="<?= $path_prefix ?>products.php">Products</a>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="<?= $path_prefix ?>cart.php">Cart</a>
                    <a href="<?= $path_prefix ?>order_history.php">Orders</a>
                    
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                        <a href="<?= $path_prefix ?>admin/products.php" class="admin-link">Admin Panel</a>
                    <?php endif; ?>
                    
                    <a href="<?= $path_prefix ?>logout.php">Logout (<?= htmlspecialchars($_SESSION['user_name']) ?>)</a>
                <?php else: ?>
                    <a href="<?= $path_prefix ?>login.php">Login</a>
                    <a href="<?= $path_prefix ?>register.php">Register</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    <div class="main-content">
    ```