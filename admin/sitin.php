<?php
session_start();
include '../header.php';
require_once '../includes/sitin_functions.php';

// Check if user is admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "users";

$conn = new mysqli($servername, $username, $password, $database);

// Handle approval/decline actions
if(isset($_POST['action']) && isset($_POST['record_id'])) {
    $record_id = $_POST['record_id'];
    $status = $_POST['action'] === 'approve' ? 'active' : 'declined';
    
    if(updateSitInStatus($conn, $record_id, $status)) {
        $message = $status === 'active' ? 'Reservation approved!' : 'Reservation declined!';
        $success = true;
    } else {
        $message = 'Error updating reservation.';
        $success = false;
    }
}

// Fetch pending reservations - fixed query
$sql = "SELECT sit_in_records.*, students.First_Name, students.Last_Name, students.Course, students.Year_lvl 
        FROM sit_in_records 
        JOIN students ON sit_in_records.IDNO = students.IDNO 
        WHERE sit_in_records.status = 'pending' 
        ORDER BY sit_in_records.id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sit-in Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }
        
        /* Header styles */
        .header {
            background-color: #000080;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header-title {
            font-size: 18px;
            font-weight: bold;
        }
        
        .nav-links {
            display: flex;
        }
        
        .nav-links a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
        }
        
        .nav-links a:hover {
            text-decoration: underline;
        }
        
        /* Main content styles */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .page-title {
            text-align: center;
            font-size: 24px;
            margin-bottom: 30px;
            font-weight: bold;
        }
        
        /* Charts section */
        .charts-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 40px;
            margin-bottom: 40px;
        }
        
        .chart-wrapper {
            flex: 1;
            min-width: 300px;
            max-width: 500px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .chart-legend {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin-bottom: 10px;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            margin: 0 5px 5px 5px;
        }
        
        .legend-color {
            width: 15px;
            height: 15px;
            margin-right: 5px;
        }
        
        .empty-chart {
            width: 300px;
            height: 300px;
            border-radius: 50%;
            border: 4px solid #ddd;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }
        
        .no-data-message {
            color: #999;
            font-size: 14px;
        }
        
        .chart-scale {
            margin-top: 15px;
            font-size: 12px;
            color: #666;
            display: flex;
            justify-content: center;
            width: 100%;
        }
        
        .scale-number {
            padding: 0 4px;
        }
        
        /* Table controls */
        .table-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .entries-control select {
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .search-control input {
            padding: 5px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 200px;
        }
        
        /* Table styles */
        .records-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: white;
        }
        
        .records-table th, .records-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        
        .records-table th {
            background-color: #f2f2f2;
            cursor: pointer;
        }
        
        .records-table th:hover {
            background-color: #e6e6e6;
        }
        
        .empty-message {
            text-align: center;
            padding: 20px;
            color: #999;
        }
        
        /* Pagination */
        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .page-info {
            font-size: 14px;
            color: #666;
        }
        
        .pagination {
            display: flex;
        }
        
        .pagination a {
            padding: 5px 10px;
            border: 1px solid #ddd;
            text-decoration: none;
            color: #666;
            background-color: #f2f2f2;
        }
        
        .pagination a.active {
            background-color: #ddd;
            color: #333;
        }
        
        .pagination a:first-child {
            border-top-left-radius: 4px;
            border-bottom-left-radius: 4px;
        }
        
        .pagination a:last-child {
            border-top-right-radius: 4px;
            border-bottom-right-radius: 4px;
        }
        
        /* Media queries */
        @media (max-width: 768px) {
            .charts-container {
                flex-direction: column;
                align-items: center;
            }
            
            .table-controls {
                flex-direction: column;
                gap: 10px;
            }
            
            .pagination-container {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-primary shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center">
                <span class="text-white text-xl font-bold py-4">Sit-in</span>
                <div class="flex space-x-4">
                    <div class="hidden md:flex items-center space-x-4">
                        <a href="dashboard.php" class="nav-link text-white hover:text-gray-200">Dashboard</a>
                        <a href="search.php" class="nav-link text-white hover:text-gray-200">Search</a>
                        <a href="students.php" class="nav-link text-white hover:text-gray-200">Students</a>
                        <a href="sitin.php" class="nav-link text-white hover:text-gray-200">Sit-in</a>
                        <a href="sit_in_records.php" class="nav-link text-white hover:text-gray-200">View Sit-in Records</a>
                        <a href="sit_in_reports.php" class="nav-link text-white hover:text-gray-200">Sit-in Reports</a>
                        <a href="../logout.php" class="nav-link text-white hover:text-gray-200">Logout</a>
                    </div>
                </div>
                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button class="mobile-menu-button" onclick="toggleNav()">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <!-- Mobile menu -->
        <div class="hidden md:hidden" id="navbarNav">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="dashboard.php" class="block text-white py-2 px-4 hover:bg-blue-900">Dashboard</a>
                <a href="students.php" class="block text-white py-2 px-4 hover:bg-blue-900">Students</a>
                <a href="sit_in_records.php" class="block text-white py-2 px-4 hover:bg-blue-900">Records</a>
                <a href="announcements.php" class="block text-white py-2 px-4 hover:bg-blue-900">Announcements</a>
                <a href="../logout.php" class="block text-white py-2 px-4 hover:bg-blue-900">Logout</a>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto py-6 px-4">
        <!-- Pending Reservations Section -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold mb-4">
                <i class="fas fa-clock text-blue-600 mr-2"></i>
                Pending Reservations
            </h2>
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <?php if($result->num_rows > 0): ?>
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date & Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student Details</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lab Room</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Purpose</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <?= date('M d, Y', strtotime($row['time_in'])) ?><br>
                                        <span class="text-sm text-gray-500">
                                            <?= date('g:i A', strtotime($row['time_in'])) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="font-medium"><?= htmlspecialchars($row['First_Name'] . ' ' . $row['Last_Name']) ?></p>
                                        <p class="text-sm text-gray-500">
                                            <?= htmlspecialchars($row['Course']) ?> - <?= htmlspecialchars($row['Year_lvl']) ?>
                                        </p>
                                        <p class="text-xs text-gray-500"><?= htmlspecialchars($row['IDNO']) ?></p>
                                    </td>
                                    <td class="px-6 py-4"><?= htmlspecialchars($row['lab_room']) ?></td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm"><?= htmlspecialchars($row['purpose']) ?></p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <form method="POST" class="flex gap-2">
                                            <input type="hidden" name="record_id" value="<?= $row['id'] ?>">
                                            <button type="submit" name="action" value="approve" 
                                                    class="bg-green-500 text-white px-3 py-1 rounded text-sm hover:bg-green-600 flex items-center">
                                                <i class="fas fa-check mr-1"></i> Approve
                                            </button>
                                            <button type="submit" name="action" value="decline" 
                                                    class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600 flex items-center">
                                                <i class="fas fa-times mr-1"></i> Decline
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="p-6 text-center text-gray-500">
                        <i class="fas fa-info-circle text-blue-500 mb-2 text-2xl"></i>
                        <p>No pending reservations at this time.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if(isset($message)): ?>
        <script>
            Swal.fire({
                title: '<?= $success ? 'Success!' : 'Error!' ?>',
                text: '<?= $message ?>',
                icon: '<?= $success ? 'success' : 'error' ?>',
                confirmButtonColor: '#000080'
            });
        </script>
    <?php endif; ?>

    <script>
        // Toggle mobile menu function
        function toggleNav() {
            const navbarNav = document.getElementById('navbarNav');
            if (navbarNav) {
                navbarNav.classList.toggle('hidden');
            }
        }
    </script>
</body>
</html>