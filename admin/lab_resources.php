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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin-dark-mode.css">
    <style>
        /* Add transition styles */
        * {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
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
<body class="bg-gray-100 dark:bg-gray-900 transition-all duration-300">
    <!-- Navigation -->
    <nav class="bg-primary shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center">
                <span class="text-white text-xl font-bold py-4">Lab Resources</span>
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
    <body class="bg-gray-100">
    <div class="max-w-7xl mx-auto py-6 px-4">
        <!-- Dashboard Header -->
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Lab Resources</h1>
        
        <!-- Lab Resources Section -->
        <div class="mb-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-white">Lab Resources</h2>
            </div>
            <div id="resourcesContainer" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php
                // Connect to database
                $conn = new mysqli("localhost", "root", "", "users");
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Fetch resources
                $sql = "SELECT * FROM lab_resources ORDER BY id DESC";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo '<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">';
                        // Image section
                        if ($row['image_path']) {
                            echo '<div class="h-48 w-full overflow-hidden">';
                            echo '<img src="../' . htmlspecialchars($row['image_path']) . '" 
                                      alt="' . htmlspecialchars($row['resource_name']) . '" 
                                      class="w-full h-full object-cover">';
                            echo '</div>';
                        } else {
                            echo '<div class="h-48 w-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">';
                            echo '<i class="fas fa-image text-4xl text-gray-400 dark:text-gray-500"></i>';
                            echo '</div>';
                        }
                        // Details section
                        echo '<div class="p-4">';
                        echo '<h3 class="text-lg font-semibold text-gray-800 dark:text-white">' . 
                             htmlspecialchars($row['resource_name']) . '</h3>';
                        echo '<p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Code: ' . 
                             htmlspecialchars($row['resource_code']) . '</p>';
                        if ($row['website_url']) {
                            echo '<a href="' . htmlspecialchars($row['website_url']) . '" 
                                    target="_blank" 
                                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm mt-2 inline-block">
                                    <i class="fas fa-external-link-alt mr-1"></i>Visit Website
                                 </a>';
                        }
                        // Edit and Delete buttons
                        echo '<div class="flex justify-end gap-2 mt-4">';
                        echo '<button onclick="editResource(' . $row['id'] . ')" 
                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                <i class="fas fa-edit"></i>
                              </button>';
                        echo '<button onclick="deleteResource(' . $row['id'] . ')" 
                                class="text-red-600 hover:text-red-800 dark:text-red-400">
                                <i class="fas fa-trash"></i>
                              </button>';
                        echo '</div>';
                        echo '</div>'; // Close details section
                        echo '</div>'; // Close card
                    }
                }
                $conn->close();
                ?>
                <!-- Add Resource Card -->
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg shadow-lg p-6 border-2 border-dashed border-gray-300 dark:border-gray-700 flex items-center justify-center cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700" onclick="openAddResourceModal()">
                    <div class="text-center">
                        <i class="fas fa-plus text-2xl text-gray-500 dark:text-gray-400"></i>
                        <p class="mt-2 text-gray-500 dark:text-gray-400">Add New Resource</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Resource Modal -->
        <div id="addResourceModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Add Lab Resource</h3>
                    <form id="addResourceForm" enctype="multipart/form-data">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Resource Name</label>
                                <input type="text" name="resource_name" required
                                    class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Resource Code</label>
                                <input type="text" name="resource_code" required
                                    class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Website URL</label>
                                <input type="url" name="website_url" required
                                    class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="https://example.com">
                            </div>
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Resource Image</label>
                                <input type="file" name="image" accept="image/*"
                                    class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary/80">
                            </div>
                        </div>
                        <div class="flex justify-end gap-4 mt-6">
                            <button type="button" onclick="closeAddResourceModal()"
                                class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">Cancel</button>
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Add Resource</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Resource Modal -->
        <div id="editResourceModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Edit Lab Resource</h3>
                    <form id="editResourceForm" enctype="multipart/form-data">
                        <input type="hidden" id="editResourceId" name="id">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Resource Name</label>
                                <input type="text" id="editResourceName" name="resource_name" required
                                    class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Resource Code</label>
                                <input type="text" id="editResourceCode" name="resource_code" required
                                    class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Website URL</label>
                                <input type="url" id="editWebsiteUrl" name="website_url" required
                                    class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="https://example.com">
                            </div>
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Update Image</label>
                                <input type="file" name="image" accept="image/*"
                                    class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary/80">
                            </div>
                        </div>
                        <div class="flex justify-end gap-4 mt-6">
                            <button type="button" onclick="closeEditResourceModal()"
                                class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">Cancel</button>
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Add this before existing script tags -->
        <script>
            function openAddResourceModal() {
                document.getElementById('addResourceModal').classList.remove('hidden');
            }

            function closeAddResourceModal() {
                document.getElementById('addResourceModal').classList.add('hidden');
            }

            document.getElementById('addResourceForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                formData.append('action', 'add');

                fetch('lab_resource_operations.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Resource added successfully!');
                        closeAddResourceModal();
                        location.reload();
                    } else {
                        alert('Error adding resource: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    alert('Error: ' + error);
                });
            });

            // Close modal when clicking outside
            document.getElementById('addResourceModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeAddResourceModal();
                }
            });

            function editResource(id) {
                fetch('lab_resource_operations.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=get&id=${id}`
                })
                .then(response => response.json())
                .then(resource => {
                    document.getElementById('editResourceId').value = resource.id;
                    document.getElementById('editResourceName').value = resource.resource_name;
                    document.getElementById('editResourceCode').value = resource.resource_code;
                    document.getElementById('editWebsiteUrl').value = resource.website_url;
                    document.getElementById('editResourceModal').classList.remove('hidden');
                });
            }

            function closeEditResourceModal() {
                document.getElementById('editResourceModal').classList.add('hidden');
            }

            document.getElementById('editResourceForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                formData.append('action', 'edit');

                fetch('lab_resource_operations.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Resource updated successfully!');
                        closeEditResourceModal();
                        location.reload();
                    } else {
                        alert('Error updating resource: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    alert('Error: ' + error);
                });
            });

            function deleteResource(id) {
                if (confirm('Are you sure you want to delete this resource?')) {
                    fetch('lab_resource_operations.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=delete&id=${id}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Error deleting resource: ' + (data.message || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        alert('Error: ' + error);
                    });
                }
            }

            // Close modal when clicking outside
            document.getElementById('editResourceModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeEditResourceModal();
                }
            });
        </script>

      

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

                
                <div id="pcGrid" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mt-4">
    <p class="col-span-full text-center text-gray-500 dark:text-gray-400">Please select a lab room</p>
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
    <div class="flex items-center">
        <div class="w-4 h-4 rounded bg-gray-300 dark:bg-gray-600 mr-2"></div>
        <span class="text-sm text-gray-600 dark:text-gray-300">Under Maintenance</span>
    </div>
</div>

    <script>
        // Mobile navigation toggle function
        function toggleNav() {
            const navbarNav = document.getElementById('navbarNav');
            navbarNav.classList.toggle('hidden');
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
                            const isUnderMaintenance = pcStatus.status === 'disabled';
                            
                            const pcElement = document.createElement('div');
                            pcElement.className = `p-4 rounded-lg ${
                                isUnderMaintenance ? 'bg-gray-300 dark:bg-gray-600' :
                                isInUse ? 'bg-red-100 dark:bg-red-900' : 
                                'bg-green-100 dark:bg-green-900'
                            } text-center`;
                            pcElement.innerHTML = `
                                <i class="fas fa-desktop text-2xl ${
                                    isUnderMaintenance ? 'text-gray-600 dark:text-gray-400' :
                                    isInUse ? 'text-red-600 dark:text-red-400' : 
                                    'text-green-600 dark:text-green-400'
                                }"></i>
                                <p class="mt-2 font-semibold ${
                                    isUnderMaintenance ? 'text-gray-600 dark:text-gray-400' :
                                    isInUse ? 'text-red-600 dark:text-red-400' : 
                                    'text-green-600 dark:text-green-400'
                                }">PC ${i}</p>
                                <p class="text-sm ${
                                    isUnderMaintenance ? 'text-gray-500 dark:text-gray-300' :
                                    isInUse ? 'text-red-500 dark:text-red-300' : 
                                    'text-green-500 dark:text-green-300'
                                }">
                                    ${isUnderMaintenance ? 'Under Maintenance' : isInUse ? 'In Use' : 'Available'}
                                </p>
                                ${isInUse ? `<p class="text-xs text-red-500 dark:text-red-300">User: ${pcStatus.student_id || 'N/A'}</p>` : ''}
                                ${isUnderMaintenance && pcStatus.disabled_reason ? `<p class="text-xs text-gray-500 dark:text-gray-300">Reason: ${pcStatus.disabled_reason}</p>` : ''}
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
  <script>
window.updatePcStatus = function() {
    const pcNumber = document.getElementById('selectedPcNumber').textContent;
    const status = document.querySelector('input[name="pcStatus"]:checked').value;
    // Convert 'under-maintenance' to 'disabled' for database compatibility
    const dbStatus = status === 'under-maintenance' ? 'disabled' : status;
    const reason = document.getElementById('maintenanceReason').value;
    const labRoom = document.getElementById('labFilter').value;

    fetch('update_pc_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            lab_room: labRoom,
            pc_number: pcNumber,
            status: dbStatus,
            reason: reason
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closePcStatusModal();
            createPCGrid(labRoom); // Refresh the grid
        } else {
            alert('Successfully updated PC status: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Successfully updated PC status');
    });
};

document.addEventListener('DOMContentLoaded', function() {
    const labFilter = document.getElementById('labFilter');
    
    function createPCGrid(labRoom) {
        fetch(`get_pc_status.php?lab=${encodeURIComponent(labRoom)}`)
            .then(response => response.json())
            .then(data => {
                const pcGrid = document.getElementById('pcGrid');
                pcGrid.innerHTML = '';
                
                for (let i = 1; i <= 30; i++) {
                    const pcStatus = data.find(pc => parseInt(pc.pc_number) === i) || { status: 'available' };
                    const pcElement = document.createElement('div');
                    pcElement.className = `p-4 rounded-lg cursor-pointer ${
                        pcStatus.status === 'disabled' ? 'bg-gray-300 dark:bg-gray-600' :
                        pcStatus.status === 'in-use' ? 'bg-red-100 dark:bg-red-900' :
                        'bg-green-100 dark:bg-green-900'
                    } text-center hover:scale-105 transition-transform`;
                    
                    const statusDisplay = pcStatus.status === 'disabled' ? 'Under Maintenance' : pcStatus.status;
                    
                    pcElement.innerHTML = `
                        <i class="fas fa-desktop text-2xl"></i>
                        <p class="mt-2 font-semibold">PC ${i}</p>
                        <p class="text-sm">${statusDisplay}</p>
                        ${pcStatus.student_id ? `<p class="text-xs">User: ${pcStatus.student_id}</p>` : ''}
                        ${pcStatus.disabled_reason ? `<p class="text-xs">Reason: ${pcStatus.disabled_reason}</p>` : ''}
                    `;
                    
                    pcElement.addEventListener('click', () => {
                        openPcStatusModal(i, pcStatus.status || 'available');
                    });
                    
                    pcGrid.appendChild(pcElement);
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // Event listener for lab selection
    labFilter.addEventListener('change', function() {
        if (this.value) {
            createPCGrid(this.value);
        }
    });

    // Make sure modal functions are in global scope
    window.openPcStatusModal = function(pcNumber, currentStatus) {
        const modal = document.getElementById('pcStatusModal');
        const statusInputs = modal.querySelectorAll('input[name="pcStatus"]');
        
        document.getElementById('selectedPcNumber').textContent = pcNumber;
        modal.classList.remove('hidden');
        
        // Set current status (map 'disabled' to 'under-maintenance')
        statusInputs.forEach(input => {
            if ((currentStatus === 'disabled' && input.value === 'under-maintenance') || 
                (currentStatus === 'available' && input.value === 'available')) {
                input.checked = true;
            }
        });
        
        // If PC is under maintenance, show the reason field and populate it
        if (currentStatus === 'disabled') {
            document.getElementById('maintenanceReasonContainer').classList.remove('hidden');
            // Try to get the reason from the PC card if available
            const pcCards = document.querySelectorAll('#pcGrid > div');
            for (let card of pcCards) {
                if (card.querySelector('p').textContent.includes('PC ' + pcNumber)) {
                    const reasonElement = card.querySelector('p:last-child');
                    if (reasonElement && reasonElement.textContent.startsWith('Reason:')) {
                        document.getElementById('maintenanceReason').value = 
                            reasonElement.textContent.replace('Reason:', '').trim();
                        break;
                    }
                }
            }
        } else {
            document.getElementById('maintenanceReasonContainer').classList.add('hidden');
            document.getElementById('maintenanceReason').value = '';
        }
    };

    window.closePcStatusModal = function() {
        document.getElementById('pcStatusModal').classList.add('hidden');
    };

    // Add event listeners for radio buttons
    const radioButtons = document.querySelectorAll('input[name="pcStatus"]');
    radioButtons.forEach(radio => {
        radio.addEventListener('change', function() {
            document.getElementById('maintenanceReasonContainer').classList.toggle(
                'hidden',
                this.value !== 'under-maintenance'
            );
        });
    });
});
</script>
<!-- Add this modal HTML before the closing body tag -->
<div id="pcStatusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Manage PC Status</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">PC: <span id="selectedPcNumber"></span></p>
            
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Status</label>
                <div class="space-y-2">
                    <label class="flex items-center space-x-2">
                        <input type="radio" name="pcStatus" value="available" class="form-radio">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Available</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="radio" name="pcStatus" value="under-maintenance" class="form-radio">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Under Maintenance</span>
                    </label>
                </div>
            </div>
            
            <div id="maintenanceReasonContainer" class="mb-4 hidden">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Reason for Maintenance</label>
                <textarea id="maintenanceReason" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
            </div>
            
            <div class="flex justify-end gap-4">
                <button onclick="closePcStatusModal()" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Cancel</button>
                <button onclick="updatePcStatus()" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Update</button>
            </div>
        </div>
    </div>
</div>
</body>
</html>
