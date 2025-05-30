<?php
session_start();
include '../header.php';

// Define static admin credentials
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'admin123');

// Check if user is logged in as admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    // Check if admin credentials are being submitted
    if (isset($_POST['username']) && isset($_POST['password'])) {
        if ($_POST['username'] === ADMIN_USERNAME && $_POST['password'] === ADMIN_PASSWORD) {
            $_SESSION['is_admin'] = true;
            $_SESSION['username'] = ADMIN_USERNAME;
        } else {
            header("Location: ../login.php");
            exit();
        }
    } else {
        header("Location: ../login.php");
        exit();
    }
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "users"; // Using the same database as your student page

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch programming languages data from sit-in records
$sql = "SELECT purpose, COUNT(*) as count 
        FROM (
            SELECT purpose FROM sit_in_records 
            UNION ALL 
            SELECT purpose FROM direct_sitin
        ) as combined_records 
        GROUP BY purpose";
$lang_result = $conn->query($sql);

$languageData = [
    'labels' => [],
    'data' => [],
    'colors' => []
];

$chartColors = [
    'ASP.Net' => '#FF6384',
    'C' => '#36A2EB',
    'C++' => '#FFCE56',
    'C#' => '#4BC0C0',
    'Java' => '#9d1a67',
    'PHP' => '#682cbd',
    'Python' => '#c371cd'
];

if ($lang_result) {
    while ($row = $lang_result->fetch_assoc()) {
        $languageData['labels'][] = $row['purpose'];
        $languageData['data'][] = (int)$row['count'];
        $languageData['colors'][] = $chartColors[$row['purpose']] ?? '#' . substr(md5($row['purpose']), 0, 6);
    }
}

// Process announcement deletion
if (isset($_POST['delete_announcement']) && isset($_POST['announcement_id'])) {
    if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
        $announce_id = (int)$_POST['announcement_id']; // Cast to integer for safety
        
        // Check if announcement exists first
        $check_sql = "SELECT announce_id FROM announcements WHERE announce_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("i", $announce_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            // Announcement exists, proceed with deletion
            $sql = "DELETE FROM announcements WHERE announce_id = ? LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $announce_id);
            
            if ($stmt->execute()) {
                $success_message = "Announcement deleted successfully!";
            } else {
                $error_message = "Error deleting announcement: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error_message = "Announcement not found!";
        }
        $check_stmt->close();
    } else {
        $error_message = "Unauthorized access!";
    }
}

// Process announcement submission
if (isset($_POST['submit_announcement'])) {
    if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
        $message = trim($_POST['announcement_text']);
        $admin_username = ADMIN_USERNAME; // Changed from admin to ADMIN_USERNAME constant
        date_default_timezone_set('Asia/Manila'); // Set timezone to Philippines
        $date = date('Y-m-d');
        $time = date('H:i:s'); // Current time
        
        if (!empty($message)) {
            $sql = "INSERT INTO announcements (admin_username, date, time, message) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $admin_username, $date, $time, $message);
            
            if ($stmt->execute()) {
                $success_message = "Announcement posted successfully!";
            } else {
                $error_message = "Error: " . $stmt->error;
            }
            
            $stmt->close();
        } else {
            $error_message = "Announcement cannot be empty!";
        }
    } else {
        $error_message = "Unauthorized access!";
    }
}

// Fetch announcements from database - modify this section
$sql = "SELECT * FROM announcements ORDER BY date DESC, time DESC LIMIT 10";
$result = $conn->query($sql);
$announcements = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $announcements[] = $row;
    }
}

// Fetch student year level data for the bar graph
$sql = "SELECT Year_lvl, COUNT(*) as count FROM students GROUP BY Year_lvl ORDER BY Year_lvl";
$YearLevelResult = $conn->query($sql);

if (!$YearLevelResult) {
    // Log the error for debugging
    error_log("Query failed: " . $conn->error);
    $YearLevelData = [
        'labels' => ['1st Year', '2nd Year', '3rd Year', '4th Year'],
        'data' => [0, 0, 0, 0]
    ];
} else {
    $YearLevelData = [
        'labels' => [],
        'data' => []
    ];
    
    while ($row = $YearLevelResult->fetch_assoc()) {
        $YearLevelData['labels'][] = $row['Year_lvl'] . " Year";
        $YearLevelData['data'][] = (int)$row['count'];
    }
    
    // If no data was found, use empty values
    if (empty($YearLevelData['labels'])) {
        $YearLevelData = [
            'labels' => ['1st Year', '2nd Year', '3rd Year', '4th Year'],
            'data' => [0, 0, 0, 0]
        ];
    }
}

// Get count of current sit-ins (modify the statistics section)
$sql = "SELECT COUNT(*) as count FROM direct_sitin WHERE status = 'active'";
$result = $conn->query($sql);
$current_sitins = $result->fetch_assoc()['count'];

// Get count of total sit-ins from both tables
$sql = "SELECT (SELECT COUNT(*) FROM sit_in_records) + (SELECT COUNT(*) FROM direct_sitin) as total_count";
$result = $conn->query($sql);
$total_sitins = $result->fetch_assoc()['total_count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
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
    <!-- Add Chart.js for pie chart and bar graph -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <link rel="stylesheet" href="../css/admin-dark-mode.css">
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@4/dark.css" rel="stylesheet">
    <style>
        /* Add transition styles */
        * {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 transition-all duration-300">
   <!-- Navigation -->
   <nav class="bg-primary shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center">
                <span class="text-white text-xl font-bold py-4">Admin Dashboard</span>
                <div class="flex space-x-4">
                    <div class="hidden md:flex items-center space-x-4">
                        <a href="dashboard.php" class="nav-link text-white hover:text-gray-200">Dashboard</a>
                        <a href="search.php" class="nav-link text-white hover:text-gray-200">Search</a>
                        <a href="students.php" class="nav-link text-white hover:text-gray-200">Students</a>
                        <!-- Replace the sitin link with this dropdown -->
                        <div class="relative group">
                            <button class="nav-link text-white hover:text-gray-200 flex items-center">
                                Sit-in
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-800 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                <div class="py-1 rounded-md bg-white dark:bg-gray-800 shadow-xs">
                                    <a href="sitin.php" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Lab Sit-ins</a>
                                    <a href="sitin_logs.php" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Sit-in Logs</a>
                                </div>
                            </div>
                        </div>
                        
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
                        <!-- Add notification bell before dark mode toggle -->
                        <button id="notificationBell" class="p-2 rounded-lg text-white hover:text-gray-200 relative">
                            <i class="fas fa-bell"></i>
                            <span id="notification-count" class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-500 rounded-full">0</span>
                        </button>
                        <button id="darkModeToggle" class="p-2 rounded-lg text-white hover:text-gray-200">
                            <!-- Sun icon - Shows in light mode -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <!-- Moon icon - Shows in dark mode -->
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

    <div class="max-w-7xl mx-auto px-4 py-6">
        <!-- Add notification modal here, before the grid -->
        <div id="notificationModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
            <div class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-11/12 max-w-2xl">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                        <h3 class="text-xl font-semibold text-blue-600 dark:text-blue-400">
                            <i class="fas fa-bell mr-2"></i>Notifications
                        </h3>
                        <button id="closeNotificationModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="p-4 max-h-[70vh] overflow-y-auto">
                        <div id="notifications-container" class="space-y-4">
                            <!-- Notifications will be loaded here -->
                            <p class="text-gray-500 dark:text-gray-400 italic text-center py-4">Loading notifications...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Statistics Panel -->
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-xl font-semibold mb-4 text-blue-600">
                    <i class="fas fa-chart-bar mr-2"></i>Statistics
                </h3>
                <div class="mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <p class="text-gray-600">Students Registered:</p>
                            <p class="text-2xl font-bold">
                                <?php
                                // Get count of registered students
                                $sql = "SELECT COUNT(*) as count FROM students";
                                $result = $conn->query($sql);
                                $row = $result->fetch_assoc();
                                echo $row['count'] ?? '180';
                                ?>
                            </p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <p class="text-gray-600">Currently Sit-in:</p>
                            <p class="text-2xl font-bold"><?php echo $current_sitins; ?></p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg col-span-1 md:col-span-2">
                            <p class="text-gray-600">Total Sit-in:</p>
                            <p class="text-2xl font-bold"><?php echo $total_sitins; ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 h-64">
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

            <!-- Announcements Panel -->
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-xl font-semibold mb-4 text-blue-600">
                    <i class="fas fa-bullhorn mr-2"></i>Announcement
                </h3>
                
                <!-- Display success/error messages -->
                <?php if (isset($success_message)): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline"><?php echo $success_message; ?></span>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($error_message)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline"><?php echo $error_message; ?></span>
                    </div>
                <?php endif; ?>
                
                <div class="mb-6">
                    <h4 class="text-lg mb-2">New Announcement</h4>
                    <form action="" method="post">
                        <textarea 
                            class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                            rows="4" 
                            placeholder="Enter announcement here..."
                            name="announcement_text"
                            required
                        ></textarea>
                        <button 
                            type="submit" 
                            class="mt-2 px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500"
                            name="submit_announcement"
                        >Submit</button>
                    </form>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-2 pb-2 border-b border-gray-200">Posted Announcement</h4>
                    
                    <div class="space-y-4 mt-4">
                        <?php if (empty($announcements)): ?>
                            <p class="text-gray-500 italic">No announcements have been posted yet.</p>
                        <?php else: ?>
                            <?php foreach ($announcements as $announcement): ?>
                                <div class="border-b border-gray-200 pb-4">
                                    <p class="text-gray-600 text-sm">
                                        CCS Admin | <?php 
                                            date_default_timezone_set('Asia/Manila');
                                            $datetime = strtotime($announcement['date'] . ' ' . $announcement['time']);
                                            echo date('F d, Y', $datetime) . ' at ' . 
                                                 date('h:i A', $datetime); 
                                        ?>
                                    </p>
                                    <p class="mt-1"><?php echo htmlspecialchars($announcement['message']); ?></p>
                                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
                                        <form action="" method="post" class="mt-2">
                                            <input type="hidden" name="announcement_id" value="<?php echo $announcement['announce_id']; ?>">
                                            <button type="submit" name="delete_announcement" 
                                                class="text-red-500 text-sm hover:text-red-700"
                                                onclick="return confirm('Are you sure you want to delete this announcement?')">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Student Year Level Graph Panel -->
            <div class="bg-white p-6 rounded-lg shadow-lg md:col-span-2">
                <h3 class="text-xl font-semibold mb-4 text-blue-600">
                    <i class="fas fa-graduation-cap mr-2"></i>Student Year Level Distribution
                </h3>
                <div class="h-64">
                    <canvas id="yearLevelChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialize Charts
        document.addEventListener('DOMContentLoaded', function() {
            // Pie Chart for programming languages
            const languageData = {
                labels: <?php echo json_encode($languageData['labels']); ?>,
                data: <?php echo json_encode($languageData['data']); ?>,
                colors: <?php echo json_encode($languageData['colors']); ?>
            };

            const ctxPie = document.getElementById('languageChart').getContext('2d');
            const languageChart = new Chart(ctxPie, {
                type: 'pie',
                data: {
                    labels: languageData.labels,
                    datasets: [{
                        data: languageData.data,
                        backgroundColor: languageData.colors
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

            // Update the legend dynamically
            const legendContainer = document.querySelector('.grid-cols-2.gap-2');
            legendContainer.innerHTML = ''; // Clear existing legend items
            
            languageData.labels.forEach((label, index) => {
                const div = document.createElement('div');
                div.className = 'flex items-center';
                div.innerHTML = `
                    <span class="w-3 h-3 inline-block mr-2" style="background-color: ${languageData.colors[index]};"></span>
                    <span class="text-xs">${label}</span>
                `;
                legendContainer.appendChild(div);
            });

            // Bar Chart for student year levels
            const ctxBar = document.getElementById('yearLevelChart').getContext('2d');
            const yearLevelChart = new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($YearLevelData['labels']); ?>,
                    datasets: [{
                        label: 'Number of Students',
                        data: <?php echo json_encode($YearLevelData['data']); ?>,
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.7)',  // 1st Year - Blue
                            'rgba(255, 99, 132, 0.7)',  // 2nd Year - Red
                            'rgba(75, 192, 192, 0.7)',  // 3rd Year - Green
                            'rgba(255, 159, 64, 0.7)'   // 4th Year - Orange
                        ],
                        borderColor: [
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 99, 132, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            },
                            title: {
                                display: true,
                                text: 'Number of Students',
                                font: {
                                    weight: 'bold'
                                }
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Year Level',
                                font: {
                                    weight: 'bold'
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                title: (tooltipItems) => {
                                    return tooltipItems[0].label + ' Students';
                                }
                            }
                        }
                    }
                }
            });
        });

        // Single document ready handler for all initializations
        document.addEventListener('DOMContentLoaded', function() {
            // Dark mode initialization
            const darkModeToggle = document.getElementById('darkModeToggle');
            const html = document.documentElement;
            
            // Check for saved dark mode preference
            if (localStorage.getItem('adminDarkMode') === 'enabled') {
                html.classList.add('dark');
                updateDarkModeIcons(true);
            }
            
            // Toggle dark mode
            darkModeToggle.addEventListener('click', function() {
                const isDarkMode = html.classList.toggle('dark');
                localStorage.setItem('adminDarkMode', isDarkMode ? 'enabled' : null);
                updateDarkModeIcons(isDarkMode);
            });

            // Initialize other components
            initializeNotifications();
            initializeNavigation();
        });

        function updateDarkModeIcons(isDarkMode) {
            const sunIcon = darkModeToggle.querySelector('.dark\\:block');
            const moonIcon = darkModeToggle.querySelector('.block');
            
            if (isDarkMode) {
                sunIcon.classList.remove('hidden');
                moonIcon.classList.add('hidden');
            } else {
                sunIcon.classList.add('hidden');
                moonIcon.classList.remove('hidden');
            }
        }

        function initializeNavigation() {
            const nav = document.getElementById('navbarNav');
            const toggleBtn = document.querySelector('.mobile-menu-button');

            // Toggle nav
            toggleBtn?.addEventListener('click', () => {
                nav.classList.toggle('hidden');
            });

            // Close nav when clicking outside
            document.addEventListener('click', function(event) {
                if (!nav.contains(event.target) && !toggleBtn.contains(event.target)) {
                    nav.classList.add('hidden');
                }
            });
        }

        function initializeNotifications() {
            // Add this right after your existing document.ready function
            const notificationBell = document.getElementById('notificationBell');
            const notificationModal = document.getElementById('notificationModal');
            const closeNotificationModal = document.getElementById('closeNotificationModal');

            notificationBell.addEventListener('click', () => {
                notificationModal.classList.remove('hidden');
                // Fetch latest notifications when opening modal
                fetchNotifications();
            });

            closeNotificationModal.addEventListener('click', () => {
                notificationModal.classList.add('hidden');
            });

            // Close modal when clicking outside
            notificationModal.addEventListener('click', (e) => {
                if (e.target === notificationModal) {
                    notificationModal.classList.add('hidden');
                }
            });

            // Add escape key listener
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !notificationModal.classList.contains('hidden')) {
                    notificationModal.classList.add('hidden');
                }
            });
        }

        // Update your existing fetchNotifications function to include approve/decline buttons
        function fetchNotifications() {
            fetch('fetch_notifications.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                const container = document.getElementById('notifications-container');
                const count = document.getElementById('notification-count');
                
                if (data.error) {
                    throw new Error(data.error);
                }
                
                count.textContent = data.length;
                
                if (data.length === 0) {
                    container.innerHTML = '<p class="text-gray-500 dark:text-gray-400 italic text-center py-4">No pending reservations</p>';
                    return;
                }

                container.innerHTML = data.map(notification => `
                    <div class="bg-blue-50 dark:bg-gray-700 p-4 rounded-lg">
                        <div class="flex flex-col space-y-3">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-user-clock text-blue-500 text-2xl"></i>
                                </div>
                                <div class="flex-grow">
                                    <p class="font-semibold text-gray-800 dark:text-white">
                                        ${notification.First_Name} ${notification.Last_Name} (${notification.IDNO})
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">
                                        ${notification.Course} - ${notification.Year_lvl} Year
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">
                                        Lab ${notification.lab} | PC ${notification.pc} | Purpose: ${notification.purpose}
                                    </p>
                                    <p class="text-sm text-blue-600 dark:text-blue-400 font-medium">
                                        <i class="fas fa-calendar-alt mr-1"></i> ${notification.reservation_date}
                                        <i class="fas fa-clock ml-2 mr-1"></i> ${notification.formatted_time}
                                    </p>
                                </div>
                            </div>
                            <div class="flex justify-end space-x-2">
                                <button onclick="handleReservation(${notification.reservation_id}, 'approve')" 
                                    class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                                    <i class="fas fa-check mr-2"></i>Approve
                                </button>
                                <button onclick="handleReservation(${notification.reservation_id}, 'decline')" 
                                    class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                                    <i class="fas fa-times mr-2"></i>Decline
                                </button>
                            </div>
                        </div>
                    </div>
                `).join('');
            })
            .catch(error => {
                console.error('Error fetching notifications:', error);
                document.getElementById('notifications-container').innerHTML = 
                    `<p class="text-red-500 italic text-center py-4">Error loading notifications: ${error.message}</p>`;
            });
        }

        // Add new function to handle reservation actions
        function handleReservation(recordId, action) {
            fetch('handle_reservation.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=${action}&record_id=${recordId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Refresh notifications after action
                    fetchNotifications();
                } else {
                    console.error('Error:', data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        }

        // Fetch notifications every 30 seconds
        setInterval(fetchNotifications, 30000);
        // Initial fetch
        fetchNotifications();
    </script>
    <style>
    .group:hover .group-hover\:opacity-100 {
        opacity: 1;
    }
    .group:hover .group-hover\:visible {
        visibility: visible;
    }
    .nav-link {
        position: relative;
        padding: 0.5rem;
    }
    .nav-link:after {
        content: '';
        position: absolute;
        width: 0;
        height: 2px;
        bottom: 0;
        left: 0;
        background-color: white;
        transition: width 0.3s ease;
    }
    .nav-link:hover:after {
        width: 100%;
    }
</style>
</body>
</html>