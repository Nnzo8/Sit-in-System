<?php
session_start();

// Check if user is admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../login.php");
    exit();
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

// Process announcement submission
if (isset($_POST['submit_announcement'])) {
    $message = trim($_POST['announcement_text']);
    $admin_username = $_SESSION['username']; // Get admin username from session
    $date = date('Y-m-d'); // Current date
    
    if (!empty($message)) {
        // Insert announcement into database
        $sql = "INSERT INTO announcements (admin_username, date, message) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $admin_username, $date, $message);
        
        if ($stmt->execute()) {
            $success_message = "Announcement posted successfully!";
        } else {
            $error_message = "Error: " . $stmt->error;
        }
        
        $stmt->close();
    } else {
        $error_message = "Announcement cannot be empty!";
    }
}

// Fetch announcements from database
$sql = "SELECT * FROM announcements ORDER BY date DESC LIMIT 10";
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
</head>
<body class="bg-gray-100">
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

    <div class="max-w-7xl mx-auto px-4 py-6">
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
                            <p class="text-2xl font-bold">
                                <?php
                                // Get count of current sit-ins
                                $sql = "SELECT COUNT(*) as count FROM sit_in_records WHERE status = 'active'";
                                $result = $conn->query($sql);
                                $row = $result->fetch_assoc();
                                echo $row['count'] ?? '0';
                                ?>
                            </p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg col-span-1 md:col-span-2">
                            <p class="text-gray-600">Total Sit-in:</p>
                            <p class="text-2xl font-bold">
                                <?php
                                // Get count of total sit-ins
                                $sql = "SELECT COUNT(*) as count FROM sit_in_records";
                                $result = $conn->query($sql);
                                $row = $result->fetch_assoc();
                                echo $row['count'] ?? '79';
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 h-64">
                    <canvas id="languageChart"></canvas>
                </div>
                <div class="flex justify-center mt-2 text-sm text-gray-600">
                    <div class="flex items-center mx-2">
                        <div class="w-3 h-3 bg-blue-400 mr-1"></div>
                        <span>C#</span>
                    </div>
                    <div class="flex items-center mx-2">
                        <div class="w-3 h-3 bg-pink-400 mr-1"></div>
                        <span>C</span>
                    </div>
                    <div class="flex items-center mx-2">
                        <div class="w-3 h-3 bg-orange-400 mr-1"></div>
                        <span>Java</span>
                    </div>
                    <div class="flex items-center mx-2">
                        <div class="w-3 h-3 bg-yellow-400 mr-1"></div>
                        <span>ASP.Net</span>
                    </div>
                    <div class="flex items-center mx-2">
                        <div class="w-3 h-3 bg-teal-400 mr-1"></div>
                        <span>PHP</span>
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
                                        CCS Admin | <?php echo date('Y-M-d', strtotime($announcement['date'])); ?>
                                    </p>
                                    <p class="mt-1"><?php echo htmlspecialchars($announcement['message']); ?></p>
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
            const ctxPie = document.getElementById('languageChart').getContext('2d');
            const languageChart = new Chart(ctxPie, {
                type: 'pie',
                data: {
                    labels: ['C#', 'C', 'Java', 'ASP.Net', 'PHP'],
                    datasets: [{
                        data: [15, 55, 20, 3, 7],
                        backgroundColor: [
                            '#38bdf8', // C# - blue
                            '#f472b6', // C - pink
                            '#fb923c', // Java - orange
                            '#facc15', // ASP.Net - yellow
                            '#2dd4bf'  // PHP - teal
                        ],
                        borderWidth: 1
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
            
            // Bar Chart for student year levels
            const ctxBar = document.getElementById('YearLevelChart').getContext('2d');
            const YearLevelChart = new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($YearLevelData['labels']); ?>,
                    datasets: [{
                        label: 'Number of Students',
                        data: <?php echo json_encode($YearLevelData['data']); ?>,
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.7)', // Blue
                            'rgba(75, 192, 192, 0.7)', // Teal
                            'rgba(153, 102, 255, 0.7)', // Purple
                            'rgba(255, 159, 64, 0.7)'  // Orange
                        ],
                        borderColor: [
                            'rgba(54, 162, 235, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
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
                            title: {
                                display: true,
                                text: 'Number of Students'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Year Level'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        title: {
                            display: false
                        }
                    }
                }
            });
        });

        function toggleNav() {
            document.getElementById('navbarNav').classList.toggle('hidden');
        }

        // Close nav when clicking outside
        document.addEventListener('click', function(event) {
            const nav = document.getElementById('navbarNav');
            const toggleBtn = document.querySelector('.mobile-menu-button');
            if (!nav.contains(event.target) && !toggleBtn.contains(event.target)) {
                nav.classList.add('hidden');
            }
        });
    </script>
</body>
</html>