<?php
// register.php: Handles user registration (Frontend and Backend)
include 'db_connect.php';

$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error']);
unset($_SESSION['success']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    //  Validation and Error Checks
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        // Hash the password 
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $is_admin = 0;

        //  Check if email already exists
        $check_sql = "SELECT id FROM users WHERE email = ?";
        if ($check_stmt = $conn->prepare($check_sql)) {
            $check_stmt->bind_param("s", $email);
            $check_stmt->execute();
            $check_stmt->store_result();

            if ($check_stmt->num_rows > 0) {
                $error = "This email address is already registered.";
            } else {
                // Create a new user entry using Prepared Statements [source: 25]

                $insert_sql = "INSERT INTO users (name, email, password, is_admin) VALUES (?, ?, ?, ?)";
                
                if ($insert_stmt = $conn->prepare($insert_sql)) {
                    $insert_stmt->bind_param("sssi", $name, $email, $hashed_password, $is_admin);

                    if ($insert_stmt->execute()) {
                        $success = "Registration successful! You can now log in.";
                    // Optional: Send the user to the login page
                    // header("Location: login.php"); exit();

                    } else {
                        $error = "Database insertion failed.";
                    }
                    $insert_stmt->close();
                } else {
                    $error = "Database error: Could not prepare insert statement.";
                }
            }
            $check_stmt->close();
        } else {
            $error = "Database error: Could not prepare check statement.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Online Computer Store</title>
    <link rel="stylesheet" href="assets/css/style.css">
    </head>
<body>
    <div class="container">
        <h1>Create a New Account</h1>
        
        <?php if ($error): ?>
            <p style="color: red; border: 1px solid red; padding: 10px;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <p style="color: green; border: 1px solid green; padding: 10px;"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <form method="POST" action="register.php" onsubmit="return validateForm()">
            <div>
                <label for="name">Full Name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div>
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit">Register</button>
        </form>

        <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </div>

    <script>
    [cite_start]// Basic front-end validation with JavaScript [ref: 23]

    function validateForm() {
        var password = document.getElementById('password').value;
        var confirmPassword = document.getElementById('confirm_password').value;
        
        if (password.length < 6) {
            alert("Password must be at least 6 characters long.");
            return false;
        }
        if (password !== confirmPassword) {
            alert("Passwords do not match.");
            return false;
        }
        return true;
    }
    </script>
</body>
</html>

<?php $conn->close(); ?>