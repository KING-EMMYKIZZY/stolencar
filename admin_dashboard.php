<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
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

// Handle status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['report_id'])) {
    $stmt = $pdo->prepare("UPDATE stolen_car_reports SET status = ?, admin_comments = ? WHERE id = ?");
    $stmt->execute([$_POST['status'], $_POST['admin_comments'], $_POST['report_id']]);
    header("Location: admin_dashboard.php");
    exit();
}

// Fetch all reports with user information
$stmt = $pdo->prepare("
    SELECT r.*, u.full_name, u.email 
    FROM stolen_car_reports r 
    JOIN users u ON r.user_id = u.id 
    ORDER BY r.created_at DESC
");
$stmt->execute();
$reports = $stmt->fetchAll();

// Get total number of users
$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$total_users = $stmt->fetchColumn();

// Get total number of reports
$stmt = $pdo->query("SELECT COUNT(*) FROM stolen_car_reports");
$total_reports = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Report Management</title>
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

        .stats-container {
            display: flex;
            justify-content: space-around;
            margin-bottom: 2rem;
        }

        .stat-card {
            background-color: var(--card-background);
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 1.5rem;
            text-align: center;
            flex: 1;
            margin: 0 1rem;
        }

        .stat-card h4 {
            color: var(--primary-color);
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }

        .stat-card p {
            font-size: 2rem;
            font-weight: bold;
            color: var(--secondary-color);
        }

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

        .status-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-pending { background-color: #ffeeba; color: #856404; }
        .status-in_progress { background-color: #b8daff; color: #004085; }
        .status-resolved { background-color: #c3e6cb; color: #155724; }
        .status-closed { background-color: #f5c6cb; color: #721c24; }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 2rem;
            border-radius: 8px;
            width: 80%;
            max-width: 500px;
        }

        .report-details {
            margin-bottom: 1.5rem;
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 4px;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }

        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        .form-group textarea {
            resize: vertical;
        }

        button {
            background-color: var(--primary-color);
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #2980b9;
        }

        button[type="button"] {
            background-color: #95a5a6;
        }

        button[type="button"]:hover {
            background-color: #7f8c8d;
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

            .stats-container {
                flex-direction: column;
            }

            .stat-card {
                margin: 0 0 1rem 0;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Admin Dashboard - Report Management</h2>
        <div>
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
            <a href="index.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <div class="container">
        <div class="stats-container">
            <div class="stat-card">
                <h4>Total Users</h4>
                <p><?php echo $total_users; ?></p>
            </div>
            <div class="stat-card">
                <h4>Total Reports</h4>
                <p><?php echo $total_reports; ?></p>
            </div>
        </div>

        <div class="card">
            <h3>Stolen Car Reports</h3>
            <table class="reports-table">
                <thead>
                    <tr>
                        <th>Reporter Details</th>
                        <th>Car Information</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Document</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reports as $report): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($report['full_name']); ?></strong><br>
                                Email: <?php echo htmlspecialchars($report['email']); ?><br>
                                Phone: <?php echo htmlspecialchars($report['phone_number']); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($report['car_name']); ?><br>
                                Model: <?php echo htmlspecialchars($report['car_model']); ?><br>
                                Plate: <?php echo htmlspecialchars($report['plate_number']); ?>
                            </td>
                            <td>
                                Stolen at: <?php echo htmlspecialchars($report['stolen_location']); ?><br>
                                Address: <?php echo htmlspecialchars($report['permanent_address']); ?>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $report['status']; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $report['status'])); ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?php echo htmlspecialchars($report['document_path']); ?>" target="_blank">View Document</a>
                            </td>
                            <td>
                                <button onclick="openUpdateModal(<?php echo $report['id']; ?>)">Update Status</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div id="updateModal" class="modal">
        <div class="modal-content">
            <h3>Update Report Status</h3>
            <form id="updateForm" method="POST" action="">
                <input type="hidden" id="report_id" name="report_id">
                <div class="report-details" id="reportDetails"></div>
                <div class="form-group">
                    <label for="status">Status:</label>
                    <select id="status" name="status" required>
                        <option value="pending">Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="resolved">Resolved</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="admin_comments">Admin Comments:</label>
                    <textarea id="admin_comments" name="admin_comments" rows="4"></textarea>
                </div>
                <div>
                    <button type="submit">Update</button>
                    <button type="button" onclick="closeModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openUpdateModal(reportId) {
            document.getElementById('updateModal').style.display = 'block';
            document.getElementById('report_id').value = reportId;
            
            // Fetch and display report details
            const report = <?php echo json_encode($reports); ?>.find(r => r.id == reportId);
            const detailsHtml = `
                <p><strong>Reporter:</strong> ${report.full_name}</p>
                <p><strong>Car:</strong> ${report.car_name} (${report.car_model})</p>
                <p><strong>Plate Number:</strong> ${report.plate_number}</p>
                <p><strong>Stolen Location:</strong> ${report.stolen_location}</p>
            `;
            document.getElementById('reportDetails').innerHTML = detailsHtml;
            
            // Set current status
            document.getElementById('status').value = report.status;
            document.getElementById('admin_comments').value = report.admin_comments || '';
        }

        function closeModal() {
            document.getElementById('updateModal').style.display = 'none';
        }

        // Close modal if clicked outside
        window.onclick = function(event) {
            if (event.target == document.getElementById('updateModal')) {
                closeModal();
            }
        }
    </script>
</body>
</html>