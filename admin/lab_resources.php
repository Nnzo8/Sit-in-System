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
    <title>Lab Resources</title>
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
<body class="bg-gray-100 dark:bg-gray-900">
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
    
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6 ">Lab Resources Management</h1>
        
        <!-- Resource Management Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Computer Status Section -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-700 dark:text-white">Computer Status</h2>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-300">Lab 524:</span>
                        <span class="text-green-500">30/30 Working</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-300">Lab 526:</span>
                        <span class="text-yellow-500">28/30 Working</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-300">Lab 530:</span>
                        <span class="text-yellow-500">12/30 Working</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-300">Lab 542:</span>
                        <span class="text-yellow-500">25/30 Working</span>
                    </div>
                </div>
                <button class="mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Update Status
                </button>
            </div>

            <!-- Software Management -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-700 dark:text-white">Software Management</h2>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-300">Visual Studio</span>
                        <span class="text-green-500">Installed</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-300">Eclipse IDE</span>
                        <span class="text-green-500">Installed</span>
                    </div>
                    <!-- Add more software -->
                </div>
                <button class="mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Manage Software
                </button>
            </div>
        </div>

        <!-- Courses Section -->
        <div class="mt-8">
            <h2 class="text-xl font-semibold mb-4 text-gray-700 dark:text-white">Courses Using Lab Resources</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Course Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700 dark:text-white">Integrative Programming</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Course Code: 20370</p>
                        </div>
                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-green-900 dark:text-green-300">Active</span>
                    </div>
                    <div class="mt-4">
                        <p class="text-sm text-gray-600 dark:text-gray-300">Lab: 526</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Schedule: MW 4:30-7:00 PM</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Instructor: Mr. Wilson Gayo</p>
                    </div>
                </div>
                <!-- Course Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700 dark:text-white">Information Management(DB Sys.2)</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Course Code: 23145</p>
                        </div>
                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-green-900 dark:text-green-300">Active</span>
                    </div>
                    <div class="mt-4">
                        <p class="text-sm text-gray-600 dark:text-gray-300">Lab: 530</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Schedule: FS 4:00-6:30 PM</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Instructor: Ms. Beverly Lahaylahay</p>
                    </div>
                </div>
                <!-- Course Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700 dark:text-white">Information Assurance and Security</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Course Code: 98761</p>
                        </div>
                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-green-900 dark:text-green-300">Active</span>
                    </div>
                    <div class="mt-4">
                        <p class="text-sm text-gray-600 dark:text-gray-300">Lab: 530A</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Schedule: MW 7:00-9:30 PM</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Instructor: Mr. Huebert Ferolino</p>
                    </div>
                </div>
                <!-- Course Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700 dark:text-white">System Integration and Architecture</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Course Code: 87521</p>
                        </div>
                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-green-900 dark:text-green-300">Active</span>
                    </div>
                    <div class="mt-4">
                        <p class="text-sm text-gray-600 dark:text-gray-300">Lab: 524</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Schedule: TTH 10:30-1:00 PM</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Instructor: Mr. Jeff Salimbangon</p>
                    </div>
                </div>
                <!-- Course Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700 dark:text-white">Technopreneurship</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Course Code: 12345</p>
                        </div>
                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-green-900 dark:text-green-300">Active</span>
                    </div>
                    <div class="mt-4">
                        <p class="text-sm text-gray-600 dark:text-gray-300">Lab: 542</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Schedule: TTH 3:00-4:30 PM</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Instructor: Ms. Leah Ybanez</p>
                    </div>
                </div>
                <!-- Course Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700 dark:text-white">IT-Fretrends</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Course Code: 64821</p>
                        </div>
                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-green-900 dark:text-green-300">Active</span>
                    </div>
                    <div class="mt-4">
                        <p class="text-sm text-gray-600 dark:text-gray-300">Lab: 542</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Schedule: TTH 1:00-3:00 PM</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Instructor: Mr. Franz Caminade</p>
                    </div>
                </div>
                <!-- Add Course Card -->
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg shadow-lg p-6 border-2 border-dashed border-gray-300 dark:border-gray-700 flex items-center justify-center">
                    <button onclick="openAddCourseModal()" class="text-gray-500 dark:text-gray-400 hover:text-primary">
                        <i class="fas fa-plus text-2xl"></i>
                        <p class="mt-2">Add New Course</p>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Course Modal -->
    <div id="addCourseModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Add New Course</h3>
                <form id="addCourseForm">
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="courseName">
                            Course Name
                        </label>
                        <input type="text" id="courseName" name="courseName" required
                            class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="courseCode">
                            Course Code
                        </label>
                        <input type="text" id="courseCode" name="courseCode" required
                            class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="lab">
                            Lab
                        </label>
                        <input type="text" id="lab" name="lab" required
                            class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="schedule">
                            Schedule
                        </label>
                        <input type="text" id="schedule" name="schedule" required
                            class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="instructor">
                            Instructor
                        </label>
                        <input type="text" id="instructor" name="instructor" required
                            class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div class="flex justify-end gap-4">
                        <button type="button" onclick="closeAddCourseModal()"
                            class="px-4 py-2 bg-red-500 text-white-700 rounded hover:bg-gray-400">Cancel</button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Add Course</button>
                    </div>
                </form>
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

        // Modal functions
        function openAddCourseModal() {
            document.getElementById('addCourseModal').classList.remove('hidden');
        }

        function closeAddCourseModal() {
            document.getElementById('addCourseModal').classList.add('hidden');
        }

        // Form submission
        document.getElementById('addCourseForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('add_course.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Course added successfully!');
                    window.location.reload();
                } else {
                    alert('Error adding course: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error: ' + error);
            });
        });
    </script>
</body>
</html>
