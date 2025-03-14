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
    $conn->begin_transaction();
    
    try {
        if($_POST['action'] === 'approve') {
            // First check if reservation is still pending
            $check_sql = "SELECT status FROM sit_in_records WHERE id = ? AND status = 'pending' LIMIT 1";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param('i', $record_id);
            $check_stmt->execute();
            $result = $check_stmt->get_result();
            
            if($result->num_rows > 0) {
                // Update the status to active and set current time
                $update_sql = "UPDATE sit_in_records 
                             SET status = 'active',
                                 time_in = NOW(),
                                 date_updated = NOW()
                             WHERE id = ? 
                             AND status = 'pending'";
                $stmt = $conn->prepare($update_sql);
                $stmt->bind_param('i', $record_id);
                
                if($stmt->execute()) {
                    $conn->commit();
                    $message = 'Reservation approved successfully!';
                    $success = true;
                } else {
                    throw new Exception("Failed to update reservation");
                }
            } else {
                throw new Exception("Reservation was already processed");
            }
        } else if($_POST['action'] === 'decline') {
            // Update declined reservation status
            $delete_sql = "UPDATE sit_in_records 
                          SET status = 'declined',
                              date_updated = NOW() 
                          WHERE id = ? 
                          AND status = 'pending'";
            $stmt = $conn->prepare($delete_sql);
            $stmt->bind_param('i', $record_id);
            
            if($stmt->execute() && $stmt->affected_rows > 0) {
                $conn->commit();
                $message = 'Reservation declined!';
                $success = true;
            } else {
                throw new Exception("Failed to decline reservation");
            }
        }
    } catch (Exception $e) {
        $conn->rollback();
        $message = 'Error: ' . $e->getMessage();
        $success = false;
    }
}

// Handle logout action
if(isset($_POST['logout_student']) && isset($_POST['record_id'])) {
    $record_id = (int)$_POST['record_id'];
    date_default_timezone_set('Asia/Manila'); // Set timezone to Philippines
    $time_out = date('Y-m-d H:i:s'); // Current server time
    
    $conn->begin_transaction();
    
    try {
        // First verify this is an active record
        $check_sql = "SELECT * FROM sit_in_records 
                     WHERE id = ? 
                     AND status IN ('active', 'approved')
                     AND time_out IS NULL 
                     LIMIT 1";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param('i', $record_id);
        $check_stmt->execute();
        $active_record = $check_stmt->get_result()->fetch_assoc();

        if($active_record) {
            // Update the record with logout time and completed status
            $update_sql = "UPDATE sit_in_records 
                          SET status = 'completed', 
                              time_out = ?,
                              date_updated = NOW()
                          WHERE id = ? 
                          AND status IN ('active', 'approved')";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param('si', $time_out, $record_id);
            
            if($stmt->execute()) {
                // Update remaining sessions for the student
                $update_sessions = "UPDATE student_session 
                                  SET remaining_sessions = remaining_sessions - 1 
                                  WHERE id_number = ? 
                                  AND remaining_sessions > 0
                                  LIMIT 1";
                $stmt2 = $conn->prepare($update_sessions);
                $stmt2->bind_param('s', $active_record['IDNO']);
                $stmt2->execute();
                
                $conn->commit();
                $message = "Student logged out successfully at " . date('g:i A', strtotime($time_out));
                $success = true;
            } else {
                throw new Exception("Failed to update logout record");
            }
        } else {
            throw new Exception("Invalid record or student already logged out");
        }
    } catch (Exception $e) {
        $conn->rollback();
        $message = "Error: " . $e->getMessage();
        $success = false;
    }
}

// Update query to fetch only pending reservations
$sql = "SELECT sir.*, s.First_Name, s.Last_Name, s.Course, s.Year_lvl 
        FROM sit_in_records sir
        JOIN students s ON sir.IDNO = s.IDNO 
        WHERE sir.status = 'pending'
        ORDER BY sir.date_updated DESC";  // Changed to date_updated for better ordering
$result = $conn->query($sql);

// Update query to fetch only active and approved students
$active_sql = "SELECT sir.*, s.First_Name, s.Last_Name, s.Course, s.Year_lvl 
               FROM sit_in_records sir
               JOIN students s ON sir.IDNO = s.IDNO 
               WHERE sir.status IN ('active', 'approved')
               AND sir.time_out IS NULL
               ORDER BY sir.time_in DESC";
$active_result = $conn->query($active_sql);

// Prepare data for charts
$languages = [];
$lab_rooms = [];
while($row = $active_result->fetch_assoc()) {
    $languages[] = $row['purpose'];
    $lab_rooms[] = $row['lab_room'];
}

$language_count = array_count_values($languages);
$lab_room_count = array_count_values($lab_rooms);
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        <!-- Analytics Section -->
        <div class="mb-8 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white p-4 rounded-lg shadow" style="width: 400px;">
                <h3 class="text-lg font-semibold mb-4">Programming Languages Distribution</h3>
                <div class="flex flex-col items-center">
                    <div style="width: 250px; height: 250px;">
                        <canvas id="languageChart"></canvas>
                    </div>
                    <div class="mt-4 text-sm grid grid-cols-2 gap-2">
                        <div class="flex items-center">
                            <span class="w-3 h-3 inline-block mr-2" style="background-color: #FF6384;"></span>
                            <span class="text-xs">ASP.Net</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-3 h-3 inline-block mr-2" style="background-color: #36A2EB;"></span>
                            <span class="text-xs">C</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-3 h-3 inline-block mr-2" style="background-color: #FFCE56;"></span>
                            <span class="text-xs">C++</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-3 h-3 inline-block mr-2" style="background-color: #4BC0C0;"></span>
                            <span class="text-xs">C#</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-3 h-3 inline-block mr-2" style="background-color: #9d1a67;"></span>
                            <span class="text-xs">Java</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-3 h-3 inline-block mr-2" style="background-color: #682cbd;"></span>
                            <span class="text-xs">PHP</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-3 h-3 inline-block mr-2" style="background-color: #c371cd;"></span>
                            <span class="text-xs">Python</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow" style="width: 400px;">
                <h3 class="text-lg font-semibold mb-4">Lab Room Distribution</h3>
                <div class="flex flex-col items-center">
                    <div style="width: 250px; height: 250px;">
                        <canvas id="labRoomChart"></canvas>
                    </div>
                    <div class="mt-4 text-sm grid grid-cols-2 gap-2">
                        <div class="flex items-center">
                            <span class="w-3 h-3 inline-block mr-2" style="background-color: #FF9F40;"></span>
                            <span class="text-xs">Lab 524</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-3 h-3 inline-block mr-2" style="background-color: #4BC0C0;"></span>
                            <span class="text-xs">Lab 526</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-3 h-3 inline-block mr-2" style="background-color: #36A2EB;"></span>
                            <span class="text-xs">Lab 528</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-3 h-3 inline-block mr-2" style="background-color: #FF6384;"></span>
                            <span class="text-xs">Lab 530</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-3 h-3 inline-block mr-2" style="background-color: #682cbd;"></span>
                            <span class="text-xs">Lab 542</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Sit-in Students -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold mb-4">
                <i class="fas fa-user-check text-green-600 mr-2"></i>
                Currently Active Sit-in Students
            </h2>
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <?php 
                $active_result->data_seek(0);
                if($active_result->num_rows > 0): ?>
                    <table class="min-w-full active-students-table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lab Room</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time In</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Purpose</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php while($row = $active_result->fetch_assoc()): ?>
                                <tr class="hover:bg-gray-50" id="student-row-<?= $row['id'] ?>">
                                    <td class="px-6 py-4">
                                        <p class="font-medium"><?= htmlspecialchars($row['First_Name'] . ' ' . $row['Last_Name']) ?></p>
                                        <p class="text-sm text-gray-500"><?= htmlspecialchars($row['Course']) ?> - <?= htmlspecialchars($row['Year_lvl']) ?></p>
                                    </td>
                                    <td class="px-6 py-4"><?= htmlspecialchars($row['lab_room']) ?></td>
                                    <td class="px-6 py-4"><?= date('g:i A', strtotime($row['time_in'])) ?></td>
                                    <td class="px-6 py-4"><?= htmlspecialchars($row['purpose']) ?></td>
                                    <td class="px-6 py-4">
                                        <form method="POST" class="flex gap-2" onsubmit="handleLogout(event, <?= $row['id'] ?>)">
                                            <input type="hidden" name="record_id" value="<?= $row['id'] ?>">
                                            <button type="submit" name="logout_student" 
                                                    class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600 flex items-center">
                                                <i class="fas fa-sign-out-alt mr-1"></i> Logout
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="p-6 text-center text-gray-500 no-active-message">
                        <i class="fas fa-info-circle text-blue-500 mb-2 text-2xl"></i>
                        <p>No active sit-in students at this time.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

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
                                <tr class="hover:bg-gray-50" id="reservation-row-<?= $row['id'] ?>">
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
                                        <form method="POST" class="flex gap-2" onsubmit="handleReservation(event, <?= $row['id'] ?>)">
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
        let languageChart, labRoomChart;  // Make charts globally accessible
        
        // Chart configurations and initialization
        function initializeCharts() {
            const chartColors = {
                languages: {
                    'ASP.Net': '#FF6384',
                    'C': '#36A2EB',
                    'C++': '#FFCE56',
                    'C#': '#4BC0C0',
                    'Java': '#9d1a67',
                    'PHP': '#682cbd',
                    'Python': '#c371cd'
                },
                labRooms: {
                    'Lab 524': '#FF9F40',
                    'Lab 526': '#4BC0C0',
                    'Lab 528': '#36A2EB',
                    'Lab 530': '#FF6384',
                    'Lab 542': '#682cbd'
                }
            };

            const languageData = <?= json_encode($language_count) ?>;
            const labRoomData = <?= json_encode($lab_room_count) ?>;

            // Language Chart with explicit color mapping
            languageChart = new Chart(document.getElementById('languageChart'), {
                type: 'pie',
                data: {
                    labels: Object.keys(languageData),
                    datasets: [{
                        data: Object.values(languageData),
                        backgroundColor: Object.keys(languageData).map(lang => chartColors.languages[lang] || '#000000')
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            // Lab Room Chart with explicit color mapping
            labRoomChart = new Chart(document.getElementById('labRoomChart'), {
                type: 'pie',
                data: {
                    labels: Object.keys(labRoomData),
                    datasets: [{
                        data: Object.values(labRoomData),
                        backgroundColor: Object.keys(labRoomData).map(room => chartColors.labRooms[room] || '#000000')
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }

        // Call initialization on page load
        document.addEventListener('DOMContentLoaded', initializeCharts);

        // Add this function before the existing handleLogout function
        function handleReservation(event, reservationId) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const action = event.submitter.value; // Get which button was clicked

            // Get row data before sending request
            const row = document.getElementById(`reservation-row-${reservationId}`);
            const studentName = row.querySelector('td:nth-child(2) p.font-medium').textContent;
            const studentDetails = row.querySelector('td:nth-child(2) p.text-sm').textContent;
            const studentId = row.querySelector('td:nth-child(2) p.text-xs').textContent;
            const labRoom = row.querySelector('td:nth-child(3)').textContent;
            const purpose = row.querySelector('td:nth-child(4)').textContent;
            const timeIn = new Date().toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });

            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(html => {
                if(html.includes('Reservation approved') || html.includes('Reservation declined')) {
                    // Only remove the specific reservation row
                    row.remove();

                    // Check if there are any remaining pending reservations
                    const pendingTableBody = row.closest('tbody');
                    if (pendingTableBody && pendingTableBody.children.length === 0) {
                        const pendingTable = pendingTableBody.closest('table');
                        const container = pendingTable.parentElement;
                        container.innerHTML = `
                            <div class="p-6 text-center text-gray-500">
                                <i class="fas fa-info-circle text-blue-500 mb-2 text-2xl"></i>
                                <p>No pending reservations at this time.</p>
                            </div>
                        `;
                    }

                    if(action === 'approve') {
                        // Add to active students table
                        const activeTableBody = document.querySelector('.active-students-table tbody');
                        const noActiveMessage = document.querySelector('.no-active-message');

                        if (noActiveMessage) {
                            noActiveMessage.parentElement.innerHTML = `
                                <table class="min-w-full active-students-table">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lab Room</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time In</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Purpose</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200"></tbody>
                                </table>
                            `;
                        }

                        const newActiveRow = `
                            <tr class="hover:bg-gray-50" id="student-row-${reservationId}">
                                <td class="px-6 py-4">
                                    <p class="font-medium">${studentName}</p>
                                    <p class="text-sm text-gray-500">${studentDetails}</p>
                                </td>
                                <td class="px-6 py-4">${labRoom}</td>
                                <td class="px-6 py-4">${timeIn}</td>
                                <td class="px-6 py-4">${purpose}</td>
                                <td class="px-6 py-4">
                                    <form method="POST" class="flex gap-2" onsubmit="handleLogout(event, ${reservationId})">
                                        <input type="hidden" name="record_id" value="${reservationId}">
                                        <button type="submit" name="logout_student" 
                                                class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600 flex items-center">
                                            <i class="fas fa-sign-out-alt mr-1"></i> Logout
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        `;

                        const activeTable = document.querySelector('.active-students-table tbody');
                        if (activeTable) {
                            activeTable.insertAdjacentHTML('afterbegin', newActiveRow);
                        }

                        // Update charts
                        updateChartsForNewStudent(purpose.trim(), labRoom.trim());
                    }

                    // Show success message
                    Swal.fire({
                        title: 'Success!',
                        text: action === 'approve' ? 'Reservation approved successfully!' : 'Reservation declined successfully!',
                        icon: 'success',
                        confirmButtonColor: '#000080'
                    });
                }
            });
        }

        // Add new function to update charts when adding a student
        function updateChartsForNewStudent(purpose, labRoom) {
            const chartColors = {
                languages: {
                    'ASP.Net': '#FF6384',
                    'C': '#36A2EB',
                    'C++': '#FFCE56',
                    'C#': '#4BC0C0',
                    'Java': '#9d1a67',
                    'PHP': '#682cbd',
                    'Python': '#c371cd'
                },
                labRooms: {
                    'Lab 524': '#FF9F40',
                    'Lab 526': '#4BC0C0',
                    'Lab 528': '#36A2EB',
                    'Lab 530': '#FF6384',
                    'Lab 542': '#682cbd'
                }
            };

            // Update language chart
            let purposeIndex = languageChart.data.labels.indexOf(purpose);
            if (purposeIndex === -1) {
                languageChart.data.labels.push(purpose);
                languageChart.data.datasets[0].data.push(1);
                languageChart.data.datasets[0].backgroundColor.push(chartColors.languages[purpose] || '#000000');
            } else {
                languageChart.data.datasets[0].data[purposeIndex]++;
            }
            languageChart.update();

            // Update lab room chart
            let labIndex = labRoomChart.data.labels.indexOf(labRoom);
            if (labIndex === -1) {
                labRoomChart.data.labels.push(labRoom);
                labRoomChart.data.datasets[0].data.push(1);
                labRoomChart.data.datasets[0].backgroundColor.push(chartColors.labRooms[labRoom] || '#000000');
            } else {
                labRoomChart.data.datasets[0].data[labIndex]++;
            }
            labRoomChart.update();
        }

        // Modified handleLogout function
        function handleLogout(event, studentId) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);

            // Get the row data before removing it
            const row = document.getElementById(`student-row-${studentId}`);
            const purpose = row.querySelector('td:nth-child(4)').textContent.trim();
            const labRoom = row.querySelector('td:nth-child(2)').textContent.trim();

            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(html => {
                if(html.includes('Student logged out successfully')) {
                    // Remove row and update empty state if needed
                    if(row) {
                        row.remove();
                        
                        // Update charts
                        updateCharts(purpose, labRoom);
                        
                        if(document.querySelector('tbody').children.length === 0) {
                            const table = document.querySelector('table');
                            const container = table.parentElement;
                            table.remove();
                            container.innerHTML = `
                                <div class="p-6 text-center text-gray-500">
                                    <i class="fas fa-info-circle text-blue-500 mb-2 text-2xl"></i>
                                    <p>No active sit-in students at this time.</p>
                                </div>
                            `;

                            // Reset charts when no students are left
                            languageChart.data.labels = [];
                            languageChart.data.datasets[0].data = [];
                            labRoomChart.data.labels = [];
                            labRoomChart.data.datasets[0].data = [];
                            languageChart.update();
                            labRoomChart.update();
                        }
                    }
                    
                    Swal.fire({
                        title: 'Success!',
                        text: 'Student logged out successfully!',
                        icon: 'success',
                        confirmButtonColor: '#000080'
                    });
                }
            });
        }

        // New function to update charts
        function updateCharts(purpose, labRoom) {
            // Update language chart
            let purposeIndex = languageChart.data.labels.indexOf(purpose);
            if (purposeIndex !== -1) {
                languageChart.data.datasets[0].data[purposeIndex]--;
                if (languageChart.data.datasets[0].data[purposeIndex] <= 0) {
                    languageChart.data.labels.splice(purposeIndex, 1);
                    languageChart.data.datasets[0].data.splice(purposeIndex, 1);
                }
            }
            languageChart.update();

            // Update lab room chart
            let labIndex = labRoomChart.data.labels.indexOf(labRoom);
            if (labIndex !== -1) {
                labRoomChart.data.datasets[0].data[labIndex]--;
                if (labRoomChart.data.datasets[0].data[labIndex] <= 0) {
                    labRoomChart.data.labels.splice(labIndex, 1);
                    labRoomChart.data.datasets[0].data.splice(labIndex, 1);
                }
            }
            labRoomChart.update();
        }

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