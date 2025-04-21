<?php
session_start();
require_once '../includes/sitin_functions.php';
include '../header.php';

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

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle record completion
if(isset($_POST['complete_sitin'])) {
    $record_id = $_POST['record_id'];
    $time_out = date('Y-m-d H:i:s');
    
    if(updateSitInRecord($conn, $record_id, $time_out)) {
        $success = "Sit-in session completed successfully!";
    } else {
        $error = "Error updating record.";
    }
}

// Handle approval/decline actions
if(isset($_POST['action']) && isset($_POST['record_id'])) {
    $record_id = $_POST['record_id'];
    $status = $_POST['action'] === 'approve' ? 'active' : 'declined';
    
    if(updateSitInStatus($conn, $record_id, $status)) {
        echo "<script>
            Swal.fire({
                title: 'Success!',
                text: '" . ($status === 'active' ? 'Reservation approved!' : 'Reservation declined!') . "',
                icon: 'success',
                confirmButtonColor: '#000080'
            });
        </script>";
    }
}

// Fetch all sit-in records
$sql = "SELECT 
            'direct' as record_type,
            ds.id,
            ds.IDNO,
            ds.lab_room,
            ds.purpose,
            ds.time_in,
            ds.time_out,
            ds.status,
            ds.date_updated,
            s.First_Name, 
            s.Last_Name, 
            s.Course,
            s.Year_lvl
        FROM direct_sitin ds
        JOIN students s ON ds.IDNO = s.IDNO 
        WHERE (ds.status = 'completed' AND ds.time_out IS NOT NULL)
            OR (ds.status = 'active' AND ds.time_out IS NULL)
        UNION ALL
        SELECT 
            'reservation' as record_type,
            sir.id,
            sir.IDNO,
            sir.lab_room,
            sir.purpose,
            sir.time_in,
            sir.time_out,
            sir.status,
            sir.date_updated,
            s.First_Name, 
            s.Last_Name, 
            s.Course,
            s.Year_lvl
        FROM sit_in_records sir
        JOIN students s ON sir.IDNO = s.IDNO 
        WHERE (sir.status = 'completed' AND sir.time_out IS NOT NULL)
            OR (sir.status = 'active' AND sir.time_out IS NULL)
        ORDER BY time_in DESC";

$result = $conn->query($sql);

if (!$result) {
    die("Error fetching records: " . $conn->error);
}

// Get statistics for charts
// Programming languages distribution
$languagesSql = "SELECT purpose as language, COUNT(*) as count 
                 FROM (
                     SELECT purpose FROM direct_sitin 
                     WHERE status = 'completed'
                     UNION ALL
                     SELECT purpose FROM sit_in_records 
                     WHERE status = 'completed'
                 ) combined 
                 GROUP BY purpose";

$languagesResult = $conn->query($languagesSql);
$languagesData = [];
if ($languagesResult) {
    while($row = $languagesResult->fetch_assoc()) {
        $languagesData[] = $row;
    }
}

// Lab rooms distribution
$labsSql = "SELECT lab_room, COUNT(*) as count 
            FROM (
                SELECT lab_room FROM direct_sitin 
                WHERE status = 'completed'
                UNION ALL
                SELECT lab_room FROM sit_in_records 
                WHERE status = 'completed'
            ) combined 
            GROUP BY lab_room";

$labsResult = $conn->query($labsSql);
$labsData = [];
if ($labsResult) {
    while($row = $labsResult->fetch_assoc()) {
        $labsData[] = $row;
    }
}

// Fetch recent entries for the table display
$recentEntriesSql = "SELECT sit_in_records.*, students.First_Name, students.Last_Name
                     FROM sit_in_records 
                     JOIN students ON sit_in_records.IDNO = students.IDNO 
                     ORDER BY sit_in_records.time_in DESC LIMIT 10";
$recentEntries = $conn->query($recentEntriesSql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sit-in Records</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../css/admin-dark-mode.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#000080',
                        secondary: '#1e293b'
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js for visualizations -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
</head>
<!-- Navigation -->
<nav class="bg-primary shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center">
                <span class="text-white text-xl font-bold py-4">Sit-in Records</span>
                <div class="flex space-x-4">
                    <div class="hidden md:flex items-center space-x-4">
                        <a href="dashboard.php" class="nav-link text-white hover:text-gray-200">Dashboard</a>
                        <a href="search.php" class="nav-link text-white hover:text-gray-200">Search</a>
                        <a href="students.php" class="nav-link text-white hover:text-gray-200">Students</a>
                        <a href="sitin.php" class="nav-link text-white hover:text-gray-200">Sit-in</a>
                        
                        <!-- New Lab Dropdown -->
                        <div class="relative group">
                            <button class="nav-link text-white hover:text-gray-200 flex items-center">
                                Lab
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-800 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                <div class="py-1 rounded-md bg-white dark:bg-gray-800 shadow-xs">
                                    <a href="lab_resources.php" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Lab Resources</a>
                                    <a href="lab_schedule.php" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Set Lab Schedule</a>
                                    <a href="lab_points.php" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Lab Usage Points</a>
                                </div>
                            </div>
                        </div>

                        <a href="sit_in_records.php" class="nav-link text-white hover:text-gray-200">View Sit-in Records</a>
                        <a href="sit_in_reports.php" class="nav-link text-white hover:text-gray-200">Sit-in Reports</a>
                        <a href="feedback.php" class="nav-link text-white hover:text-gray-200">View Feedbacks</a>
                        <a href="../logout.php" class="nav-link text-white hover:text-gray-200">Logout</a>
                        <button id="darkModeToggle" class="p-2 rounded-lg text-white hover:text-gray-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 block dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </button>
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
<body class="bg-gray-100">
    <div class="max-w-7xl mx-auto py-6 px-4">
        <!-- Dashboard Header -->
        <h1 class="text-2xl font-bold text-black text-center mb-6 dark:text-white-80">Current Sit-in Records</h1>
        
        <!-- Dashboard Charts -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Programming Languages Chart -->
            <div class="bg-white rounded-lg shadow p-4">
                <div class="h-64">
                    <canvas id="languagesChart"></canvas>
                </div>
            </div>
            
            <!-- Lab Rooms Chart -->
            <div class="bg-white rounded-lg shadow p-4">
                <div class="h-64">
                    <canvas id="labsChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Dashboard Controls -->
        <div class="bg-white rounded-lg shadow p-4 mb-8 flex flex-col md:flex-row justify-between items-center">
            <div class="mb-4 md:mb-0">
                <label for="entriesPerPage" class="mr-2 text-gray-700">Show</label>
                <select id="entriesPerPage" class="border rounded px-2 py-1">
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span class="ml-2 text-gray-700">entries per page</span>
            </div>
            
            <div class="w-full md:w-auto">
                <label for="searchBox" class="mr-2 text-gray-700">Search:</label>
                <input type="text" id="searchBox" class="border rounded px-2 py-1 w-full md:w-64">
            </div>
        </div>
        
        <!-- Recent Entries Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden mb-8">
            <table class="min-w-full">
                <thead class="bg-white">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sit-in Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Purpose</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lab</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time In</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time Out</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php 
                    $count = 1;
                    date_default_timezone_set('Asia/Manila'); // Set timezone to Philippines
                    while($row = $result->fetch_assoc()): 
                        $statusColor = $row['status'] === 'completed' ? 'text-green-600' : 'text-blue-600';
                    ?>
                        <tr class="text-gray-700">
                            <td class="px-6 py-4"><?= $count++ ?></td>
                            <td class="px-6 py-4"><?= htmlspecialchars($row['IDNO']) ?></td>
                            <td class="px-6 py-4">
                                <?= htmlspecialchars($row['Last_Name'] . ', ' . $row['First_Name']) ?>
                                <br>
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    <?= htmlspecialchars($row['Course']) ?> - <?= htmlspecialchars($row['Year_lvl']) ?>
                                </span>
                                <br>
                                <span class="text-xs <?= $statusColor ?>">
                                    <?= ucfirst($row['status']) ?> - <?= $row['record_type'] === 'direct' ? 'Direct Sit-in' : 'Reservation' ?>
                                </span>
                            </td>
                            <td class="px-6 py-4"><?= htmlspecialchars($row['purpose']) ?></td>
                            <td class="px-6 py-4"><?= htmlspecialchars($row['lab_room']) ?></td>
                            <td class="px-6 py-4"><?= date('g:i A', strtotime($row['time_in'])) ?></td>
                            <td class="px-6 py-4">
                                <?php
                                if ($row['time_out']) {
                                    echo '<span class="text-green-600">'.date('g:i A', strtotime($row['time_out'])).'</span>';
                                } else {
                                    if ($row['status'] === 'active') {
                                        echo '<span class="text-yellow-600">Still Active</span>';
                                    } else {
                                        echo '<span class="text-green-600">Logged Out</span>';
                                    }
                                }
                                ?>
                            </td>
                            <td class="px-6 py-4"><?= date('Y-m-d', strtotime($row['time_in'])) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-white rounded-lg shadow p-4 flex items-center justify-between text-gray-700">
            <div>
                Showing 1 to 1 of 1 entry
            </div>
            <div class="flex gap-1">
                <a href="#" class="border px-3 py-1 rounded text-gray-700 hover:bg-gray-100">&lt;</a>
                <a href="#" class="border px-3 py-1 rounded bg-primary text-white">1</a>
                <a href="#" class="border px-3 py-1 rounded text-gray-700 hover:bg-gray-100">&gt;</a>
            </div>
        </div>
        
      

    <script>
        // Toggle mobile navigation
        function toggleNav() {
            const nav = document.getElement.getElementById('navbarNav');
            nav.classList.toggle('hidden');
        }
        
        // Chart.js Configuration
        document.addEventListener('DOMContentLoaded', function() {
            // Languages chart
            const languagesCtx = document.getElementById('languagesChart').getContext('2d');
            const languagesData = <?= json_encode($languagesData) ?>;
            
            new Chart(languagesCtx, {
                type: 'pie',
                data: {
                    labels: languagesData.map(item => item.language),
                    datasets: [{
                        data: languagesData.map(item => item.count),
                        backgroundColor: [
                            '#36A2EB', // C#
                            '#FF6384', // C
                            '#FFCE56', // Java
                            '#4BC0C0', // PHP
                            '#FF9F40', // ASP.Net
                            '#9966FF'  // Other languages
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Programming Languages Distribution'
                        }
                    }
                }
            });
            
            // Labs chart
            const labsCtx = document.getElementById('labsChart').getContext('2d');
            const labsData = <?= json_encode($labsData) ?>;
            
            new Chart(labsCtx, {
                type: 'pie',
                data: {
                    labels: labsData.map(item => item.lab_room),
                    datasets: [{
                        data: labsData.map(item => item.count),
                        backgroundColor: [
                            '#FFC0CB', // 524
                            '#FFD700', // 528
                            '#FFFFE0', // 528
                            '#E0FFFF', // 529
                            '#87CEEB', // 542
                            '#DDA0DD'  // Mac
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Lab Distribution'
                        }
                    }
                }
            });
        });
        
        // Search functionality
        document.getElementById('searchBox').addEventListener('keyup', function() {
            const searchText = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('tbody tr');
            
            tableRows.forEach(row => {
                const rowText = row.textContent.toLowerCase();
                row.style.display = rowText.includes(searchText) ? '' : 'none';
            });
        });
        
        // Entries per page functionality
        document.getElementById('entriesPerPage').addEventListener('change', function() {
            // This would typically trigger a server request to fetch the right number of entries
            // For demo purposes, we'll just log the value
            console.log('Show', this.value, 'entries per page');
        });
        
         // Dark mode toggle functionality
 document.addEventListener('DOMContentLoaded', function() {
            const darkModeToggle = document.getElementById('darkModeToggle');
            const html = document.documentElement;
            
            // Check for saved dark mode preference
            const darkMode = localStorage.getItem('adminDarkMode');
            if (darkMode === 'enabled') {
                html.classList.add('dark');
            }
            
            // Toggle dark mode
            darkModeToggle.addEventListener('click', function() {
                html.classList.toggle('dark');
                
                // Save preference
                if (html.classList.contains('dark')) {
                    localStorage.setItem('adminDarkMode', 'enabled');
                } else {
                    localStorage.setItem('adminDarkMode', null);
                }
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>