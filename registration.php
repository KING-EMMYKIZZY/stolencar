<?php
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
        if (empty($_POST['full_name']) || empty($_POST['username']) || 
            empty($_POST['password']) || empty($_POST['email'])) {
            throw new Exception("All required fields must be filled out");
        }

        // Validate email format
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }

        // Check if username already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$_POST['username']]);
        if ($stmt->rowCount() > 0) {
            throw new Exception("Username already exists");
        }

        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$_POST['email']]);
        if ($stmt->rowCount() > 0) {
            throw new Exception("Email already exists");
        }

        // Hash password
        $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        // Prepare and execute the INSERT statement
        $stmt = $pdo->prepare("INSERT INTO users (full_name, username, password, phone_number, email, role) 
                              VALUES (?, ?, ?, ?, ?, 'user')");
        
        $stmt->execute([
            $_POST['full_name'],
            $_POST['username'],
            $hashed_password,
            $_POST['phone_number'],
            $_POST['email']
        ]);

        $message = "Registration successful!";
        
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
    <title>User Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"],
        input[type="password"],
        input[type="email"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .message {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 4px;
        }
        .success {
            background-color: #dff0d8;
            color: #3c763d;
            border: 1px solid #d6e9c6;
        }
        .error {
            background-color: #f2dede;
            color: #a94442;
            border: 1px solid #ebccd1;
        }
    </style>
</head>
<body>
    <h2>User Registration</h2>
    
    <?php if ($message): ?>
        <div class="message <?php echo strpos($message, 'Error') !== false ? 'error' : 'success'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="form-group">
            <label for="full_name">Full Name *</label>
            <input type="text" id="full_name" name="full_name" required>
        </div>

        <div class="form-group">
            <label for="username">Username *</label>
            <input type="text" id="username" name="username" required>
        </div>

        <div class="form-group">
            <label for="password">Password *</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="form-group">
            <label for="phone_number">Phone Number</label>
            <input type="text" id="phone_number" name="phone_number">
        </div>

        <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" required>
        </div>

        <button type="submit">Register</button> <a href="login.php">Login here</a></p>
    </form>
</body>
</html>