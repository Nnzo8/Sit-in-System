<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../login.php");
    exit();
}
include '../header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Points</title>
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
    <link rel="stylesheet" href="../css/admin-dark-mode.css">
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
<body class="bg-gray-100 dark:bg-gray-900">
    
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6 dark:text-white">Lab Usage Points System</h1>
        
        <!-- Points Management -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Points Overview -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-700 dark:text-white">Lab Hours System</h2>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-300">Session Reward:</span>
                        <span class="font-bold dark:text-white">+1 session per hour</span>
                    </div>
                </div>
            </div>

            <!-- Student Rankings -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 md:col-span-2">
                <h2 class="text-xl font-semibold mb-4 text-gray-700 dark:text-white">Top Students by Hours</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Rank</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Student</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Total Hours</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Sessions Left</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                            <?php
                            $servername = "localhost";
                            $username = "root";
                            $password = "";
                            $dbname = "users"; // Changed from sit_in_db to users
                            
                            // Create connection
                            $conn = new mysqli($servername, $username, $password, $dbname);
                            
                            // Check connection
                            if ($conn->connect_error) {
                                die("Connection failed: " . $conn->connect_error);
                            }

                            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_session'])) {
                                $student_id = $_POST['student_id'];
                                
                                // Check current sessions first
                                $check_sql = "SELECT remaining_sessions FROM student_session WHERE id_number = ?";
                                $check_stmt = $conn->prepare($check_sql);
                                $check_stmt->bind_param("s", $student_id);
                                $check_stmt->execute();
                                $result = $check_stmt->get_result();
                                $current_sessions = $result->fetch_assoc()['remaining_sessions'];
                                
                                if ($current_sessions >= 30) {
                                    echo "<script>alert('Maximum sessions (30) reached!');</script>";
                                } else {
                                    // Update sessions in the database
                                    $update_sql = "UPDATE student_session 
                                                   SET remaining_sessions = remaining_sessions + 1 
                                                   WHERE id_number = ?";
                                    $stmt = $conn->prepare($update_sql);
                                    $stmt->bind_param("s", $student_id);
                                    
                                    if ($stmt->execute()) {
                                        echo "<script>alert('Session added successfully!');</script>";
                                    } else {
                                        echo "<script>alert('Error adding session!');</script>";
                                    }
                                }
                            }

                            $sql = "SELECT 
                                    s.IDNO as student_id,
                                    s.First_Name as first_name,
                                    s.Last_Name as last_name,
                                    ss.remaining_sessions,
                                    COALESCE(
                                        SUM(
                                            TIMESTAMPDIFF(HOUR, 
                                                COALESCE(d.time_in, r.time_in), 
                                                COALESCE(d.time_out, r.time_out)
                                            )
                                        ), 0
                                    ) as total_hours
                                FROM students s
                                LEFT JOIN student_session ss ON s.IDNO = ss.id_number
                                LEFT JOIN direct_sitin d ON s.IDNO = d.IDNO
                                LEFT JOIN sit_in_records r ON s.IDNO = r.IDNO
                                WHERE (d.status = 'completed' OR r.status = 'completed')
                                GROUP BY s.IDNO, s.First_Name, s.Last_Name, ss.remaining_sessions
                                ORDER BY total_hours DESC
                                LIMIT 5";

                            $result = $conn->query($sql);
                            
                            if (!$result) {
                                echo "Error: " . $conn->error;
                            } else {
                                $rank = 1;
                                if ($result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                    ?>
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 text-gray-500 dark:text-gray-300"><?php echo $rank++; ?></td>
                                        <td class="px-6 py-4 text-gray-800 dark:text-gray-200">
                                            <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>
                                        </td>
                                        <td class="px-6 py-4 text-gray-800 dark:text-gray-200"><?php echo $row['total_hours']; ?></td>
                                        <td class="px-6 py-4 text-gray-800 dark:text-gray-200"><?php echo $row['remaining_sessions']; ?></td>
                                        <td class="px-6 py-4">
                                            <?php if ($row['remaining_sessions'] >= 30): ?>
                                                <span class="text-gray-400">Max sessions reached</span>
                                            <?php else: ?>
                                                <form method="POST" class="inline">
                                                    <input type="hidden" name="student_id" value="<?php echo $row['student_id']; ?>">
                                                    <button type="submit" name="add_session" 
                                                        class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm">
                                                        Add Session
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php
                                    }
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No records found</td>
                                    </tr>
                                    <?php
                                }
                            }
                            $conn->close();
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Mobile navigation toggle function
        function toggleNav() {
            const navbarNav = document.getElementById('navbarNav');
            navbarNav.classList.toggle('hidden');
        }

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
</body>
</html>