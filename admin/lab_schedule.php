<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../login.php");
    exit();
}

// Add this form handling code before including header.php
if (isset($_POST['add_schedule'])) {
    $lab_room = $_POST['lab_room'];
    $course_name = $_POST['course_name'];
    $schedule = $_POST['schedule'];
    $instructor = $_POST['instructor'];
    
    // Handle file upload
    $image_path = null;
    if (isset($_FILES['schedule_image']) && $_FILES['schedule_image']['error'] == 0) {
        $target_dir = "../uploads/schedules/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES["schedule_image"]["name"], PATHINFO_EXTENSION));
        $file_name = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $file_name;
        
        if (move_uploaded_file($_FILES["schedule_image"]["tmp_name"], $target_file)) {
            $image_path = 'uploads/schedules/' . $file_name;  // Store relative path
        }
    }
    
    // Database connection
    $conn = new mysqli("localhost", "root", "", "users");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $sql = "INSERT INTO courses (lab, course_name, schedule, instructor, schedule_image) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $lab_room, $course_name, $schedule, $instructor, $image_path);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Schedule added successfully!";
    } else {
        $_SESSION['error_message'] = "Error adding schedule: " . $stmt->error;
    }
    
    $stmt->close();
    $conn->close();
    
    // Redirect to prevent form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

include '../header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Schedule</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../css/admin-dark-mode.css">
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
    <style>
        /* Add transition styles */
        * {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin-dark-mode.css">
</head>
<body class="bg-gray-100 dark:bg-gray-900 transition-all duration-300">
    <!-- Success/Error Messages -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded z-50" id="successAlert">
            <span class="block sm:inline"><?php echo $_SESSION['success_message']; ?></span>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded z-50" id="errorAlert">
            <span class="block sm:inline"><?php echo $_SESSION['error_message']; ?></span>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <!-- Navigation -->
    <nav class="bg-primary shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center">
                <span class="text-white text-xl font-bold py-4">Lab Schedules</span>
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
<body class="bg-gray-100">
    <div class="max-w-7xl mx-auto py-6 px-4">
        <!-- Dashboard Header -->
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Lab Schedule</h1>
        
        <!-- Add Schedule Button -->
        <div class="mb-6">
            <button onclick="openAddScheduleModal()" 
                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <i class="fas fa-plus mr-2"></i>Add New Schedule
            </button>
        </div>

        <!-- Lab Rooms Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "users";

            // Create connection
            $conn = new mysqli($servername, $username, $password, $dbname);

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Get unique lab rooms
            $sql = "SELECT DISTINCT lab FROM courses ORDER BY lab";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($lab = $result->fetch_assoc()) {
                    $lab_room = $lab['lab'];
                    ?>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                        <h2 class="text-xl font-semibold mb-4 text-gray-700 dark:text-white"><?php echo htmlspecialchars($lab_room); ?></h2>
                        <div class="space-y-4">
                            <?php
                            $courses_sql = "SELECT id, course_name, schedule, instructor, schedule_image FROM courses WHERE lab = ? ORDER BY schedule";
                            $stmt = $conn->prepare($courses_sql);
                            $stmt->bind_param("s", $lab_room);
                            $stmt->execute();
                            $courses_result = $stmt->get_result();

                            if ($courses_result->num_rows > 0) {
                                while($course = $courses_result->fetch_assoc()) {
                                    // Display schedule image if exists
                                    if ($course['schedule_image']) {
                                        echo '<div class="mb-4">';
                                        echo '<a href="../' . htmlspecialchars($course['schedule_image']) . '" target="_blank" 
                                                class="block w-full h-48 overflow-hidden rounded-lg shadow-sm hover:opacity-90 transition-opacity">';
                                        echo '<img src="../' . htmlspecialchars($course['schedule_image']) . '" 
                                                alt="Schedule Image" 
                                                class="w-full h-full object-cover">';
                                        echo '</a>';
                                        echo '<p class="text-center text-sm text-gray-500 dark:text-gray-400 mt-2">Click image to view full size</p>';
                                        echo '</div>';
                                    }
                                    ?>
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-4">
                                        <div class="mb-2">
                                            <span class="font-semibold text-gray-700 dark:text-gray-300">Course: </span>
                                            <span class="text-gray-600 dark:text-gray-400"><?php echo htmlspecialchars($course['course_name']); ?></span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="font-semibold text-gray-700 dark:text-gray-300">Schedule: </span>
                                            <span class="text-gray-600 dark:text-gray-400"><?php echo htmlspecialchars($course['schedule']); ?></span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="font-semibold text-gray-700 dark:text-gray-300">Instructor: </span>
                                            <span class="text-gray-600 dark:text-gray-400"><?php echo htmlspecialchars($course['instructor']); ?></span>
                                        </div>
                                        <div class="text-right">
                                            <button onclick="deleteCourse(<?php echo $course['id']; ?>)" 
                                                    class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </div>
                                    </div>
                                    <?php
                                }
                            } else {
                                echo '<p class="text-center text-gray-500 dark:text-gray-400">No courses scheduled</p>';
                            }
                            $stmt->close();
                            ?>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<div class='col-span-2 text-center text-gray-500 dark:text-gray-400'>No labs found</div>";
            }
            $conn->close();
            ?>
        </div>
    </div>

    <!-- Add Schedule Modal -->
    <div id="addScheduleModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <h2 class="text-xl font-semibold mb-4 text-gray-700 dark:text-white">Add New Schedule</h2>
                <form action="" method="POST" enctype="multipart/form-data" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="lab_room" class="block text-base font-medium text-gray-700 dark:text-gray-300 mb-2">Lab Room</label>
                            <select id="lab_room" name="lab_room" required 
                                class="mt-1 block w-full px-4 py-3 text-base rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Select a Lab</option>
                                <option value="Lab 524">Lab 524</option>
                                <option value="Lab 526">Lab 526</option>
                                <option value="Lab 528">Lab 528</option>
                                <option value="Lab 530">Lab 530</option>
                                <option value="Lab 542">Lab 542</option>
                                <option value="Lab 544">Lab 544</option>
                                <option value="Lab 517">Lab 517</option>
                            </select>
                        </div>
                        <div>
                            <label for="course_name" class="block text-base font-medium text-gray-700 dark:text-gray-300 mb-2">Subject/Course</label>
                            <input type="text" id="course_name" name="course_name" required 
                                class="mt-1 block w-full px-4 py-3 text-base rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div>
                            <label for="schedule" class="block text-base font-medium text-gray-700 dark:text-gray-300 mb-2">Schedule</label>
                            <input type="text" id="schedule" name="schedule" required 
                                class="mt-1 block w-full px-4 py-3 text-base rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div>
                            <label for="instructor" class="block text-base font-medium text-gray-700 dark:text-gray-300 mb-2">Instructor</label>
                            <input type="text" id="instructor" name="instructor" required 
                                class="mt-1 block w-full px-4 py-3 text-base rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div class="col-span-2">
                            <label for="schedule_image" class="block text-base font-medium text-gray-700 dark:text-gray-300 mb-2">Schedule Image (Optional)</label>
                            <input type="file" id="schedule_image" name="schedule_image" accept="image/*"
                                class="mt-1 block w-full px-4 py-3 text-base text-gray-500 dark:text-gray-300
                                file:mr-4 file:py-3 file:px-4
                                file:rounded-full file:border-0
                                file:text-base file:font-semibold
                                file:bg-blue-50 file:text-blue-700
                                hover:file:bg-blue-100
                                dark:file:bg-gray-700 dark:file:text-gray-300">
                        </div>
                    </div>
                    <div class="flex justify-end gap-4">
                        <button type="button" onclick="closeAddScheduleModal()" 
                            class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">Cancel</button>
                        <button type="submit" name="add_schedule" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Add Schedule</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Add these functions before the existing script
        function openAddScheduleModal() {
            document.getElementById('addScheduleModal').classList.remove('hidden');
        }

        function closeAddScheduleModal() {
            document.getElementById('addScheduleModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('addScheduleModal');
            if (event.target == modal) {
                closeAddScheduleModal();
            }
        }

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
        
        // Add delete functionality
        function deleteCourse(id) {
            if (confirm('Are you sure you want to delete this schedule?')) {
                fetch(`delete_schedule.php?id=${id}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error deleting schedule');
                    }
                });
            }
        }
    </script>
</body>
</html>
