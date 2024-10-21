<?php
session_start();

// Database connection configuration
$host = 'localhost';
$dbname = 'report';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Validate input
        if (empty($_POST['username']) || empty($_POST['password'])) {
            throw new Exception("Please enter both username and password");
        }

        // Prepare and execute the SELECT statement
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$_POST['username']]);
        $user = $stmt->fetch();

        // Verify user exists and password is correct
        if ($user && password_verify($_POST['password'], $user['password'])) {
            // Store user data in session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['email'] = $user['email'];
            
            // Redirect based on role
            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: user_dashboard.php");
            }
            exit();
        } else {
            throw new Exception("Invalid username or password");
        }

    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .login-container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }
        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }
        input:focus {
            border-color: #4CAF50;
            outline: none;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 14px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #45a049;
        }
        .message {
            margin-bottom: 20px;
            padding: 12px;
            border-radius: 6px;
            text-align: center;
        }
        .error {
            background-color: #ffe6e6;
            color: #dc3545;
            border: 1px solid #dc3545;
        }
        .register-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }
        .register-link a {
            color: #4CAF50;
            text-decoration: none;
            font-weight: 500;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h2>Welcome Back</h2>
        </div>
        
        <?php if ($message): ?>
            <div class="message error">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit">Login</button>
        </form>

        <div class="register-link">
            <p>Don't have an account? <a href="registration.php">Register here</a></p>
        </div>
    </div>
</body>
</html>