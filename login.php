<?php
// login.php: Handles user login authentication
include 'db_connect.php'; 

// Redirect if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php"); 
    exit();
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Both email and password are required.";
    } else {
        $sql = "SELECT id, name, password, is_admin FROM users WHERE email = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();
                
                //  Verify the password against the stored hash
                if (password_verify($password, $user['password'])) {
            
                    
                    // Store necessary user data in the session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['is_admin'] = $user['is_admin'];

                    // Redirect based on admin status
                    if ($user['is_admin'] == 1) {
                        header("Location: admin/products.php"); // Redirect Admin to dashboard
                    } else {
                        header("Location: index.php"); // Redirect regular user to homepage
                    }
                    exit();
                } else {
                    $error = "Invalid email or password.";
                }
            } else {
                $error = "Invalid email or password.";
            }

            $stmt->close();
        } else {
            $error = "Database error: Could not prepare statement.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Online Computer Store</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>User Login</h1>
        
        <?php if ($error): ?>
            <p style="color: red; border: 1px solid red; padding: 10px;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        
        <?php 
        // Display success message from registration, if any
        if (isset($_SESSION['success'])): ?>
            <p style="color: green; border: 1px solid green; padding: 10px;"><?= htmlspecialchars($_SESSION['success']) ?></p>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>

        <p>Don't have an account? <a href="register.php">Register here</a>.</p>
    </div>
</body>
</html>

<?php $conn->close(); ?>