<?php
session_start();
include '../header.php';
require_once '../includes/sitin_functions.php';

// Set timezone to Asia/Manila
date_default_timezone_set('Asia/Manila');

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

// Fetch active sit-in records from both sit_in_records and direct_sitin
$active_sql = "
    SELECT sr.id, sr.IDNO, sr.lab_room, sr.time_in, sr.purpose, s.First_Name, s.Last_Name, s.Course, s.Year_lvl
    FROM sit_in_records sr
    JOIN students s ON sr.IDNO = s.IDNO
    WHERE sr.status = 'active'
    UNION
    SELECT ds.id, ds.IDNO, ds.lab_room, ds.time_in, ds.purpose, s.First_Name, s.Last_Name, s.Course, s.Year_lvl
    FROM direct_sitin ds
    JOIN students s ON ds.IDNO = s.IDNO
    WHERE ds.status = 'active'
";
$active_result = $conn->query($active_sql);

if (!$active_result) {
    die("Error fetching active sit-in records: " . $conn->error);
}

// Prepare data for charts
$languages = [];
$lab_rooms = [];
while ($row = $active_result->fetch_assoc()) {
    $languages[] = $row['purpose'];
    $lab_rooms[] = $row['lab_room'];
}

$language_count = array_count_values($languages);
$lab_room_count = array_count_values($lab_rooms);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout_student'])) {
    $record_id = $_POST['record_id'];
    $table = $_POST['table']; // Identify the table (sit_in_records or direct_sitin)

    // Fetch the reservation details
    $fetch_sql = "SELECT * FROM $table WHERE id = ?";
    $stmt = $conn->prepare($fetch_sql);
    $stmt->bind_param('i', $record_id);
    $stmt->execute();
    $reservation_details = $stmt->get_result()->fetch_assoc();

    if ($reservation_details) {
        // Get current time with correct timezone
        $time_out = date('Y-m-d H:i:s');
        $update_sql = "UPDATE $table SET time_out = ?, status = 'completed' WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param('si', $time_out, $record_id);
        $stmt->execute();

        // Update remaining sessions in student_session table using the correct IDNO
        $update_sessions = "UPDATE student_session SET remaining_sessions = remaining_sessions - 1 
        WHERE id_number = ? AND remaining_sessions > 0";
        $stmt3 = $conn->prepare($update_sessions);
        $stmt3->bind_param('s', $reservation_details['IDNO']);  // Use IDNO from reservation_details
        $stmt3->execute();
        // Format the time for display
        $formatted_time_out = date('g:i A', strtotime($time_out));

        // Return the details of the completed reservation as JSON
        echo json_encode([
            'status' => 'success',
            'message' => 'Reservation completed successfully.',
            'data' => [
                'IDNO' => $reservation_details['IDNO'],
                'lab_room' => $reservation_details['lab_room'],
                'purpose' => $reservation_details['purpose'],
                'time_in' => date('g:i A', strtotime($reservation_details['time_in'])),
                'time_out' => $formatted_time_out
            ]
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Reservation not found.'
        ]);
    }
    exit();
}
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
    <link rel="stylesheet" href="style.css">
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
</head>
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

<body class="bg-gray-100">
    <div class="max-w-7xl mx-auto py-6 px-4">
        <!-- Analytics Section -->
        <div class="flex flex-col items-center justify-center mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 w-full max-w-5xl">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold mb-4 text-center">Programming Languages Distribution</h3>
                    <div class="flex flex-col items-center">
                        <div class="w-64 h-64">
                            <canvas id="languageChart"></canvas>
                        </div>
                        <!-- Language legend remains the same -->
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
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold mb-4 text-center">Lab Room Distribution</h3>
                    <div class="flex flex-col items-center">
                        <div class="w-64 h-64">
                            <canvas id="labRoomChart"></canvas>
                        </div>
                        <!-- Lab room legend remains the same -->
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
                            <?php 
                            $active_result->data_seek(0);
                            while ($row = $active_result->fetch_assoc()): 
                                $table = isset($row['pc_number']) ? 'sit_in_records' : 'direct_sitin'; // Determine table
                            ?>
                                <tr class="hover:bg-gray-50" id="student-row-<?= $row['id'] ?>">
                                    <td class="px-6 py-4">
                                        <p class="font-medium"><?= htmlspecialchars($row['First_Name'] . ' ' . $row['Last_Name']) ?></p>
                                        <p class="text-sm text-gray-500"><?= htmlspecialchars($row['Course']) ?> - <?= htmlspecialchars($row['Year_lvl']) ?></p>
                                    </td>
                                    <td class="px-6 py-4"><?= htmlspecialchars($row['lab_room']) ?></td>
                                    <td class="px-6 py-4"><?= date('g:i A', strtotime($row['time_in'])) ?></td>
                                    <td class="px-6 py-4"><?= htmlspecialchars($row['purpose']) ?></td>
                                    <td class="px-6 py-4">
                                        <form class="flex gap-2 timeout-form" data-id="<?= $row['id'] ?>" data-table="<?= $table ?>">
                                            <button type="button" 
                                                    class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600 flex items-center timeout-button">
                                                <i class="fas fa-sign-out-alt mr-1"></i> Timeout
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
    </div>

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

        // Handle timeout button click
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.timeout-button').forEach(button => {
                button.addEventListener('click', function() {
                    const form = this.closest('.timeout-form');
                    const recordId = form.getAttribute('data-id');
                    const table = form.getAttribute('data-table');
                    
                    // Get row data before removing
                    const row = document.getElementById(`student-row-${recordId}`);
                    const studentName = row.querySelector('td:nth-child(1) p:first-child').textContent.trim();
                    const purpose = row.querySelector('td:nth-child(4)').textContent.trim();
                    const labRoom = row.querySelector('td:nth-child(2)').textContent.trim();
                    
                    // Show confirmation dialog
                    Swal.fire({
                        title: 'Confirm Time Out',
                        text: `Are you sure you want to Time Out ${studentName}?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, Time Out',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Immediately remove row and update UI
                            row.remove();
                            updateCharts(purpose, labRoom);
                            
                            // Check if table is empty and update UI accordingly
                            if (document.querySelector('tbody').children.length === 0) {
                                const table = document.querySelector('table');
                                const container = table.parentElement;
                                table.remove();
                                container.innerHTML = `
                                    <div class="p-6 text-center text-gray-500">
                                        <i class="fas fa-info-circle text-blue-500 mb-2 text-2xl"></i>
                                        <p>No active sit-in students at this time.</p>
                                    </div>
                                `;
                            }

                            // Send AJAX request to update database
                            fetch(window.location.href, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded'
                                },
                                body: new URLSearchParams({
                                    logout_student: true,
                                    record_id: recordId,
                                    table: table
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.status === 'success') {
                                    Swal.fire({
                                        title: 'Success!',
                                        text: 'Reservation timed out successfully!',
                                        icon: 'success',
                                        confirmButtonColor: '#3085d6',
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                }
                            })
                            .catch(error => {
                                console.log('Error:', error);
                                Swal.fire({
                                    title: 'Success!',
                                    text: 'Reservation timed out successfully!',
                                    icon: 'success',
                                    confirmButtonColor: '#3085d6',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            });
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>