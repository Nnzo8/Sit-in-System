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
    <body class="bg-gray-100">
    <div class="max-w-7xl mx-auto py-6 px-4">
        <!-- Dashboard Header -->
        <h1 class="text-2xl font-bold text-black text-center mb-6 dark:text-white">Current Sit-in Records</h1>
        <!-- Courses Section -->
        <div class="mt-8 mb-8">
            <h2 class="text-xl font-semibold mb-4 text-gray-700 dark:text-white">Courses Using Lab Resources</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
       
                <!-- Add Course Card -->
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg shadow-lg p-6 border-2 border-dashed border-gray-300 dark:border-gray-700 flex items-center justify-center">
                    <button onclick="openAddCourseModal()" class="text-gray-500 dark:text-gray-400 hover:text-primary">
                        <i class="fas fa-plus text-2xl"></i>
                        <p class="mt-2">Add New Course</p>
                    </button>
                </div>
            </div>
        </div>

        <!-- PC Status Monitor Section -->
        <div class="mt-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-700 dark:text-white">
                        <i class="fas fa-desktop mr-2"></i>PC Status Monitor
                    </h2>
                    <select id="labFilter" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Select Lab Room</option>
                        <option value="Lab 524">Lab 524</option>
                        <option value="Lab 526">Lab 526</option>
                        <option value="Lab 528">Lab 528</option>
                        <option value="Lab 530">Lab 530</option>
                        <option value="Lab 542">Lab 542</option>
                        <option value="Lab 544">Lab 544</option>
                        <option value="Lab 517">Lab 517</option>
                    </select>
                </div>

                <div id="pcGrid" class="grid grid-cols-6 gap-4 mt-4">
                    <p class="col-span-6 text-center text-gray-500 dark:text-gray-400">Please select a lab room</p>
                </div>

                <!-- Status Legend -->
                <div class="mt-6 flex gap-4 justify-center">
                    <div class="flex items-center">
                        <div class="w-4 h-4 rounded bg-green-100 dark:bg-green-900 mr-2"></div>
                        <span class="text-sm text-gray-600 dark:text-gray-300">Available</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 rounded bg-red-100 dark:bg-red-900 mr-2"></div>
                        <span class="text-sm text-gray-600 dark:text-gray-300">In Use</span>
                    </div>
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
                        <select id="lab" name="lab" required
                            class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
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

    <!-- Edit Course Modal -->
    <div id="editCourseModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Edit Course</h3>
                <form id="editCourseForm">
                    <input type="hidden" id="editCourseId">
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="editCourseName">
                            Course Name
                        </label>
                        <input type="text" id="editCourseName" name="courseName" required
                            class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <!-- Same fields as add form but with edit prefix -->
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="editCourseCode">
                            Course Code
                        </label>
                        <input type="text" id="editCourseCode" name="courseCode" required
                            class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="editLab">
                            Lab
                        </label>
                        <select id="editLab" name="lab" required
                            class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
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
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="editSchedule">
                            Schedule
                        </label>
                        <input type="text" id="editSchedule" name="schedule" required
                            class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="editInstructor">
                            Instructor
                        </label>
                        <input type="text" id="editInstructor" name="instructor" required
                            class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div class="flex justify-end gap-4">
                        <button type="button" onclick="closeEditCourseModal()"
                            class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Cancel</button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Save Changes</button>
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

        function openEditCourseModal() {
            document.getElementById('editCourseModal').classList.remove('hidden');
        }

        function closeEditCourseModal() {
            document.getElementById('editCourseModal').classList.add('hidden');
        }

        // Form submission for adding course
        document.getElementById('addCourseForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'add');
            
            fetch('course_operations.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Course added successfully!');
                    closeAddCourseModal();
                    loadCourses();
                    this.reset();
                } else {
                    alert('Error adding course: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                alert('Error: ' + error);
                console.error('Error:', error);
            });
        });

        // Load courses from database
        function loadCourses() {
            fetch('course_operations.php')
                .then(response => response.json())
                .then(courses => {
                    const coursesContainer = document.querySelector('.grid.grid-cols-1.md\\:grid-cols-3');
                    let coursesHTML = '';

                    courses.forEach(course => {
                        coursesHTML += `
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-700 dark:text-white">${course.course_name}</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Course Code: ${course.course_code}</p>
                                    </div>
                                    <div class="flex gap-2">
                                        <button onclick="editCourse(${course.id})" class="text-blue-500 hover:text-blue-700">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteCourse(${course.id})" class="text-red-500 hover:text-red-700">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <p class="text-sm text-gray-600 dark:text-gray-300">Lab: ${course.lab}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">Schedule: ${course.schedule}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">Instructor: ${course.instructor}</p>
                                </div>
                            </div>
                        `;
                    });

                    // Add the "Add Course" card
                    coursesHTML += `
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg shadow-lg p-6 border-2 border-dashed border-gray-300 dark:border-gray-700 flex items-center justify-center">
                            <button onclick="openAddCourseModal()" class="text-gray-500 dark:text-gray-400 hover:text-primary">
                                <i class="fas fa-plus text-2xl"></i>
                                <p class="mt-2">Add New Course</p>
                            </button>
                        </div>
                    `;

                    coursesContainer.innerHTML = coursesHTML;
                });
        }

        // Load courses when page loads
        document.addEventListener('DOMContentLoaded', loadCourses);

        // Edit course functions
        function editCourse(id) {
            fetch('course_operations.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=get&id=${id}`
            })
            .then(response => response.json())
            .then(course => {
                document.getElementById('editCourseId').value = course.id;
                document.getElementById('editCourseName').value = course.course_name;
                document.getElementById('editCourseCode').value = course.course_code;
                document.getElementById('editLab').value = course.lab;
                document.getElementById('editSchedule').value = course.schedule;
                document.getElementById('editInstructor').value = course.instructor;
                document.getElementById('editCourseModal').classList.remove('hidden');
            });
        }

        // Edit Course Form Submission
        document.getElementById('editCourseForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'edit');
            formData.append('id', document.getElementById('editCourseId').value);
            
            fetch('course_operations.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Course updated successfully!');
                    closeEditCourseModal();
                    loadCourses();
                } else {
                    alert('Error updating course: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                alert('Error: ' + error);
                console.error('Error:', error);
            });
        });

        // Delete course function
        function deleteCourse(id) {
            if (confirm('Are you sure you want to delete this course?')) {
                fetch('course_operations.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=delete&id=${id}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadCourses();
                    }
                });
            }
        }

        // Add this after your existing scripts
        document.addEventListener('DOMContentLoaded', function() {
            const labFilter = document.getElementById('labFilter');
            const pcGrid = document.getElementById('pcGrid');

            // Function to create PC status elements
            function createPCGrid(labRoom) {
                fetch(`get_pc_status.php?lab=${labRoom}`)
                    .then(response => response.json())
                    .then(data => {
                        pcGrid.innerHTML = '';
                        for (let i = 1; i <= 30; i++) {
                            const pcStatus = data.find(pc => pc.pc_number === i) || { status: 'available' };
                            const isInUse = pcStatus.status === 'in-use';
                            
                            const pcElement = document.createElement('div');
                            pcElement.className = `p-4 rounded-lg ${isInUse ? 'bg-red-100 dark:bg-red-900' : 'bg-green-100 dark:bg-green-900'} text-center`;
                            pcElement.innerHTML = `
                                <i class="fas fa-desktop text-2xl ${isInUse ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400'}"></i>
                                <p class="mt-2 font-semibold ${isInUse ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400'}">PC ${i}</p>
                                <p class="text-sm ${isInUse ? 'text-red-500 dark:text-red-300' : 'text-green-500 dark:text-green-300'}">
                                    ${isInUse ? 'In Use' : 'Available'}
                                </p>
                                ${isInUse ? `<p class="text-xs text-red-500 dark:text-red-300">User: ${pcStatus.student_id || 'N/A'}</p>` : ''}
                            `;
                            pcGrid.appendChild(pcElement);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }

            // Event listener for lab filter changes
            labFilter.addEventListener('change', function() {
                if (this.value) {
                    createPCGrid(this.value);
                } else {
                    pcGrid.innerHTML = '<p class="col-span-6 text-center text-gray-500 dark:text-gray-400">Please select a lab room</p>';
                }
            });

            // Auto-refresh status every 30 seconds
            setInterval(() => {
                if (labFilter.value) {
                    createPCGrid(labFilter.value);
                }
            }, 30000);
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const labFilter = document.getElementById('labFilter');
            const pcGrid = document.getElementById('pcGrid');

            function createPCGrid(labRoom) {
                fetch(`get_pc_status.php?lab=${encodeURIComponent(labRoom)}`)
                    .then(response => response.json())
                    .then(data => {
                        pcGrid.innerHTML = ''; // Clear existing grid
                        
                        // Create 6x5 grid for 30 PCs
                        for (let i = 1; i <= 30; i++) {
                            // Find if this PC is in use
                            const pcStatus = data.find(pc => parseInt(pc.pc_number) === i);
                            const isInUse = pcStatus ? true : false;
                            
                            const pcElement = document.createElement('div');
                            pcElement.className = `
                                p-4 rounded-lg 
                                ${isInUse ? 'bg-red-100 dark:bg-red-900' : 'bg-green-100 dark:bg-green-900'} 
                                text-center transition-all duration-200 transform hover:scale-105 hover:shadow-lg
                            `;
                            
                            pcElement.innerHTML = `
                                <i class="fas fa-desktop text-2xl ${isInUse ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400'}"></i>
                                <p class="mt-2 font-semibold ${isInUse ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400'}">
                                    PC ${i}
                                </p>
                                <p class="text-sm ${isInUse ? 'text-red-500 dark:text-red-300' : 'text-green-500 dark:text-green-300'}">
                                    ${isInUse ? 'In Use' : 'Available'}
                                </p>
                                ${isInUse ? `
                                    <p class="text-xs text-red-500 dark:text-red-300 mt-1">
                                        ID: ${pcStatus.student_id}
                                    </p>
                                ` : ''}
                            `;
                            
                            pcGrid.appendChild(pcElement);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        pcGrid.innerHTML = '<p class="col-span-6 text-center text-red-500">Error loading PC status</p>';
                    });
            }

            // Event listener for lab filter changes
            labFilter.addEventListener('change', function() {
                if (this.value) {
                    createPCGrid(this.value);
                } else {
                    pcGrid.innerHTML = '<p class="col-span-6 text-center text-gray-500 dark:text-gray-400">Please select a lab room</p>';
                }
            });

            // Auto-refresh every 30 seconds if a lab is selected
            setInterval(() => {
                if (labFilter.value) {
                    createPCGrid(labFilter.value);
                }
            }, 30000);
        });
    </script>
</body>
</html>
