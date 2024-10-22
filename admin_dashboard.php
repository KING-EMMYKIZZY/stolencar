<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Stolen Car Reports</title>
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
            padding: 1rem;
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
            width: 100%;
            margin: 1rem auto;
            padding: 0;
        }

        .card {
            background-color: var(--card-background);
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 1rem;
            margin: 0 0.5rem;
        }

        .card h3 {
            color: var(--primary-color);
            margin-bottom: 1rem;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 0.5rem;
        }

        .reports-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 1rem;
            display: block;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .reports-table th,
        .reports-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
            white-space: nowrap;
        }

        .reports-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: var(--primary-color);
        }

        .status-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: bold;
            text-transform: uppercase;
            display: inline-block;
            white-space: nowrap;
        }

        .status-under_review { background-color: #ffeeba; color: #856404; }
        .status-processing { background-color: #b8daff; color: #004085; }
        .status-pending { background-color: #c3e6cb; color: #155724; }
        .status-cancelled { background-color: #f5c6cb; color: #721c24; }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 1rem;
            border: 1px solid #888;
            width: 90%;
            max-width: 500px;
            border-radius: 8px;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        button {
            background-color: var(--primary-color);
            color: white;
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            white-space: nowrap;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            padding: 0 0.5rem;
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }

            .card {
                margin: 0;
                border-radius: 0;
            }

            .container {
                margin: 0;
            }

            .reports-table th,
            .reports-table td {
                padding: 0.5rem;
            }

            button {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }

            .status-badge {
                padding: 0.2rem 0.6rem;
                font-size: 0.7em;
            }
        }
    </style>
</head>
<body>
    <!-- Rest of the HTML and JavaScript remains the same -->
    <div class="header">
        <h2>Admin Dashboard</h2>
        <div>
            <span>Welcome, <span id="adminName"></span></span>
            <a href="index.html" class="logout-btn">Logout</a>
        </div>
    </div>

    <div class="container">
        <div class="card">
            <h3>All Stolen Car Reports</h3>
            <table class="reports-table">
                <thead>
                    <tr>
                        <th>Token</th>
                        <th>Car Details</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Date Submitted</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="reportsTableBody"></tbody>
            </table>
        </div>
    </div>

    <div id="updateModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Update Report</h3>
            <form id="updateForm">
                <input type="hidden" id="updateToken" name="updateToken">
                <div class="form-group">
                    <label for="updateStatus">Status</label>
                    <select id="updateStatus" name="updateStatus" required>
                        <option value="under_review">Under Review</option>
                        <option value="processing">Processing</option>
                        <option value="pending">Pending</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="adminComments">Admin Comments</label>
                    <textarea id="adminComments" name="adminComments" required></textarea>
                </div>
                <button type="submit">Update Report</button>
            </form>
        </div>
    </div>

    <script>
        // Simulated report data
        const reportsData = [
            {
                token: "abc123",
                carDetails: "Toyota Camry 2020",
                location: "123 Main St, City",
                status: "under_review",
                adminComments: "Investigating",
                dateSubmitted: "2024-10-20"
            },
            {
                token: "def456",
                carDetails: "Honda Civic 2019",
                location: "456 Elm St, Town",
                status: "processing",
                adminComments: "Contacting local authorities",
                dateSubmitted: "2024-10-19"
            }
        ];

        // Display admin name
        document.getElementById('adminName').textContent = "Admin User";

        // Populate reports table
        function populateReportsTable() {
            const tableBody = document.getElementById('reportsTableBody');
            tableBody.innerHTML = '';
            reportsData.forEach(report => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${report.token}</td>
                    <td>${report.carDetails}</td>
                    <td>${report.location}</td>
                    <td><span class="status-badge status-${report.status}">${report.status}</span></td>
                    <td>${report.dateSubmitted}</td>
                    <td><button onclick="openUpdateModal('${report.token}')">Update</button></td>
                `;
                tableBody.appendChild(row);
            });
        }

        // Open update modal
        function openUpdateModal(token) {
            const modal = document.getElementById('updateModal');
            const report = reportsData.find(r => r.token === token);
            document.getElementById('updateToken').value = token;
            document.getElementById('updateStatus').value = report.status;
            document.getElementById('adminComments').value = report.adminComments;
            modal.style.display = "block";
        }

        // Close modal
        document.querySelector('.close').onclick = function() {
            document.getElementById('updateModal').style.display = "none";
        }

        // Handle update form submission
        document.getElementById('updateForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const token = document.getElementById('updateToken').value;
            const status = document.getElementById('updateStatus').value;
            const comments = document.getElementById('adminComments').value;
            
            // Update report in our simulated data
            const reportIndex = reportsData.findIndex(r => r.token === token);
            if (reportIndex !== -1) {
                reportsData[reportIndex].status = status;
                reportsData[reportIndex].adminComments = comments;
            }

            // Close modal and refresh table
            document.getElementById('updateModal').style.display = "none";
            populateReportsTable();
        });

        // Initialize page
        populateReportsTable();
    </script>
</body>
</html>
