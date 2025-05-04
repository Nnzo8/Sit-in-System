<?php
session_start();
include 'header.php';

if (!isset($_SESSION['firstname'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$database = "users";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get courses from the database
$sql = "SELECT * FROM courses ORDER BY course_name";
$result = $conn->query($sql);

if (!$result) {
    die("Error fetching courses: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Resources</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#000080',
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .dark {
            background-color: #1a1a1a;
            color: #ffffff;
        }
        .dark .bg-white {
            background-color: #2d2d2d !important;
            color: #ffffff;
        }
        .dark .text-gray-800 {
            color: #ffffff;
        }
        .dark .resource-card {
            background-color: #2d2d2d;
            border-color: #4a4a4a;
        }
    </style>
</head>
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
<body class="light">
        <!-- Navigation -->
        <nav class="bg-primary shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center">
                <span class="text-white text-xl font-bold py-4">Dashboard</span>
                <div class="flex space-x-4">
                    <div class="hidden md:flex items-center space-x-4">
                        <a href="index.php" class="nav-link text-white hover:text-gray-200">Home</a>
                        <a href="edit_profile.php" class="nav-link text-white hover:text-gray-200">Edit</a>
                        <a href="resources.php" class="nav-link text-white hover:text-gray-200">Resources</a>
                        <a href="reservation.php" class="nav-link text-white hover:text-gray-200">Reservation</a>
                        <a href="history.php" class="nav-link text-white hover:text-gray-200">History</a>
                        <a href="login.php" class="nav-link text-white hover:text-gray-200">Logout</a>
                        <button id="darkModeToggle" class="p-2 rounded-lg text-white hover:text-gray-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" id="darkModeIcon">
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
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <!-- Mobile menu -->
        <div class="hidden md:hidden" id="navbarNav">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="index.php" class="block nav-link">Home</a>
                <a href="edit_profile.php" class="block nav-link">Edit</a>
                <a href="reservation.php" class="block nav-link">Reservation</a>
                <a href="history.php" class="block nav-link">History</a>
                <a href="login.php" class="block nav-link">Logout</a>
            </div>
        </div>
    </nav>
    <body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Available Lab Courses</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php
            if ($result->num_rows > 0) {
                while($course = $result->fetch_assoc()) {
                    echo '<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow duration-300">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-700 dark:text-white">'.htmlspecialchars($course['course_name']).'</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Course Code: '.htmlspecialchars($course['course_code']).'</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="text-sm text-gray-600 dark:text-gray-300">Lab: '.htmlspecialchars($course['lab']).'</p>
                            <p class="text-sm text-gray-600 dark:text-gray-300">Schedule: '.htmlspecialchars($course['schedule']).'</p>
                            <p class="text-sm text-gray-600 dark:text-gray-300">Instructor: '.htmlspecialchars($course['instructor']).'</p>
                        </div>
                    </div>';
                }
            } else {
                echo '<p class="col-span-3 text-center text-gray-500 dark:text-gray-400">No courses available at the moment.</p>';
            }
            ?>
        </div>
    </div>

    <!-- Dark mode toggle script -->
    <script>
        function initDarkMode() {
            const darkModeToggle = document.getElementById('darkModeToggle');
            const html = document.querySelector('html');
            const body = document.querySelector('body');
            const darkModeIcon = document.getElementById('darkModeIcon');
            
            // Check for saved dark mode preference
            const darkMode = localStorage.getItem('darkMode');
            
            if (darkMode === 'enabled') {
                html.classList.add('dark');
                body.classList.add('dark');
                updateDarkModeIcon(true);
            }
            
            darkModeToggle.addEventListener('click', () => {
                const isDarkMode = html.classList.contains('dark');
                
                if (isDarkMode) {
                    html.classList.remove('dark');
                    body.classList.remove('dark');
                    localStorage.setItem('darkMode', 'disabled');
                    updateDarkModeIcon(false);
                } else {
                    html.classList.add('dark');
                    body.classList.add('dark');
                    localStorage.setItem('darkMode', 'enabled');
                    updateDarkModeIcon(true);
                }
            });
        }

        function updateDarkModeIcon(isDark) {
            const darkModeIcon = document.getElementById('darkModeIcon');
            if (isDark) {
                darkModeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                </path>`;
            } else {
                darkModeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
                </path>`;
            }
        }

        document.addEventListener('DOMContentLoaded', initDarkMode);
    </script>
</body>
</html>
