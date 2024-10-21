<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

// Database connection
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
$messageType = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $car_name = $_POST['car_name'];
    $car_type = $_POST['car_type'];
    $car_model = $_POST['car_model'];
    $plate_number = $_POST['plate_number'];
    $stolen_location = $_POST['stolen_location'];
    $permanent_address = $_POST['permanent_address'];
    $phone_number = $_POST['phone_number'];

    // Handle file upload
    $document = $_FILES['document'];
    $documentPath = '';
    
    if ($document['error'] == UPLOAD_ERR_OK) {
        $documentPath = 'uploads/' . basename($document['name']);
        move_uploaded_file($document['tmp_name'], $documentPath);
    }

    // Insert the report into the database
    try {
        $stmt = $pdo->prepare("INSERT INTO stolen_car_reports (user_id, car_name, car_type, car_model, plate_number, stolen_location, permanent_address, phone_number, document_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $car_name, $car_type, $car_model, $plate_number, $stolen_location, $permanent_address, $phone_number, $documentPath]);

        $message = 'Report submitted successfully!';
        $messageType = 'success';
    } catch (PDOException $e) {
        $message = 'Error: ' . $e->getMessage();
        $messageType = 'error';
    }
}

// Fetch user's reports
$stmt = $pdo->prepare("SELECT * FROM stolen_car_reports WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$reports = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Stolen Car Reports</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2ecc71;
            --background-color: #ecf0f1;
            --text-color: #34495e;
            --card-background: #ffffff;
            --error-color: #e74c3c;
            --success-color: #27ae60;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            line-height: 1.6;
        }

        .header {
            background-color: var(--primary-color);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .header h2 {
            font-size: 1.5rem;
        }

        .logout-btn {
            background-color: var(--secondary-color);
            color: white;
            padding: 0.5rem 1rem;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .logout-btn:hover {
            background-color: #27ae60;
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .card {
            background-color: var(--card-background);
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .card h3 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 0.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: var(--primary-color);
            outline: none;
        }

        button[type="submit"] {
            background-color: var(--primary-color);
            color: white;
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: #2980b9;
        }

        .success {
            background-color: var(--success-color);
            color: white;
        }

        .error {
            background-color: var(--error-color);
            color: white;
        }

        .success, .error {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
        }

        .status-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-under_review { background-color: #ffeeba; color: #856404; }
        .status-processing { background-color: #b8daff; color: #004085; }
        .status-pending { background-color: #c3e6cb; color: #155724; }
        .status-cancelled { background-color: #f5c6cb; color: #721c24; }

        .reports-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 1.5rem;
        }

        .reports-table th,
        .reports-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .reports-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: var(--primary-color);
        }

        .reports-table tr:last-child td {
            border-bottom: none;
        }

        .reports-table tr:hover {
            background-color: #f5f5f5;
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 1rem;
            }

            .card {
                padding: 1.5rem;
            }

            .reports-table {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>User Dashboard</h2>
        <div>
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
            <a href="index.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <div class="container">
        <?php if ($message): ?>
            <div class="<?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <h3>Report Stolen Car</h3>
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="car_name">Car Name</label>
                    <input type="text" id="car_name" name="car_name" required>
                </div>

                <div class="form-group">
                    <label for="car_type">Car Type</label>
                    <input type="text" id="car_type" name="car_type" required>
                </div>

                <div class="form-group">
                    <label for="car_model">Car Model</label>
                    <input type="text" id="car_model" name="car_model" required>
                </div>

                <div class="form-group">
                    <label for="plate_number">Plate Number</label>
                    <input type="text" id="plate_number" name="plate_number" required>
                </div>

                <div class="form-group">
                    <label for="stolen_location">Location Where Car Was Stolen</label>
                    <textarea id="stolen_location" name="stolen_location" required></textarea>
                </div>

                <div class="form-group">
                    <label for="permanent_address">Permanent Address</label>
                    <textarea id="permanent_address" name="permanent_address" required></textarea>
                </div>

                <div class="form-group">
                    <label for="phone_number">Active Phone Number</label>
                    <input type="tel" id="phone_number" name="phone_number" required>
                </div>

                <div class="form-group">
                    <label for="document">Upload Valid Document (PDF, JPG, JPEG, PNG)</label>
                    <input type="file" id="document" name="document">
                </div>

                <button type="submit">Submit Report</button>
            </form>
        </div>

        <div class="card">
            <h3>Your Reports</h3>
            <table class="reports-table">
                <thead>
                    <tr>
                        <th>Car Details</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Admin Comments</th>
                        <th>Date Submitted</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reports as $report): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($report['car_name']); ?></strong><br>
                                Model: <?php echo htmlspecialchars($report['car_model']); ?><br>
                                Plate: <?php echo htmlspecialchars($report['plate_number']); ?>
                            </td>
                            <td><?php echo htmlspecialchars($report['stolen_location']); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $report['status']; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $report['status'])); ?>
                                </span>
                            </td>
                            <td><?php echo $report['admin_comments'] ? htmlspecialchars($report['admin_comments']) : 'No comments yet'; ?></td>
                            <td><?php echo date('M d, Y', strtotime($report['created_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
