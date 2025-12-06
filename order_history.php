<?php
// order_history.php: Displays a logged-in user's past orders
include 'db_connect.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$orders = [];
$error = '';

$sql_orders = "SELECT id, total_price, order_date FROM orders WHERE user_id = ? ORDER BY order_date DESC";

if ($stmt_orders = $conn->prepare($sql_orders)) {
    $stmt_orders->bind_param("i", $user_id);
    $stmt_orders->execute();
    $result_orders = $stmt_orders->get_result();
    
    while ($order = $result_orders->fetch_assoc()) {
        $order_id = $order['id'];
        $order['items'] = [];

        $sql_items = "
            SELECT oi.quantity, oi.price_at_purchase, p.name 
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
        ";
        if ($stmt_items = $conn->prepare($sql_items)) {
            $stmt_items->bind_param("i", $order_id);
            $stmt_items->execute();
            $result_items = $stmt_items->get_result();
            
            while ($item = $result_items->fetch_assoc()) {
                $order['items'][] = $item;
            }
            $stmt_items->close();
        }
        $orders[] = $order;
    }
    $stmt_orders->close();
} else {
    $error = "Database error fetching orders.";
}

$success = $_SESSION['success'] ?? '';
unset($_SESSION['success']);

$page_title = "Order History | Store";
include '_header.php';
?>

<div class="container">
    <h2>Your Order History</h2>

    <?php if ($success): ?>
        <p class="message success-message"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>
    <?php if ($error): ?>
        <p class="message error-message"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    
    <?php if (empty($orders)): ?>
        <p class="message info-message">You have not placed any orders yet.</p>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <div class="order-card">
                <div class="order-header">
                    <span>Order #<?= htmlspecialchars($order['id']) ?></span>
                    <span>Date: <?= date("M d, Y", strtotime($order['order_date'])) ?></span>
                </div>
                
                <div class="order-items">
                    <p><strong>Items Ordered:</strong></p>
                    <ul>
                        <?php foreach ($order['items'] as $item): ?>
                            <li>
                                <?= htmlspecialchars($item['name']) ?> 
                                (Qty: <?= $item['quantity'] ?> @ 
                                $<?= number_format($item['price_at_purchase'], 2) ?> each)
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="order-total">
                    <strong>Total Paid: $<?= number_format($order['total_price'], 2) ?></strong>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php 
include '_footer.php';
?>