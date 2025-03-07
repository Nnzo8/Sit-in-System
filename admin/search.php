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
$database = "users";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle AJAX search request
if(isset($_POST['search_id'])) {
    $search_id = $_POST['search_id'];
    $sql = "SELECT s.*, ss.remaining_sessions 
            FROM students s 
            LEFT JOIN student_session ss ON s.IDNO = ss.id_number 
            WHERE s.IDNO = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $search_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        $student = $result->fetch_assoc();
        echo json_encode(['status' => 'success', 'data' => $student]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No student found with that ID']);
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Search</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

    <!-- Search Button -->
    <div class="max-w-7xl mx-auto px-4 py-6">
        <button onclick="openSearchModal()" class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-blue-900 transition-colors flex items-center">
            <i class="fas fa-search mr-2"></i>
            Search Student
        </button>
    </div>

    <!-- Search Modal -->
    <div id="searchModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full modal-animation">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white modal-content-animation">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Search Student</h3>
                <div class="mt-2 px-7 py-3">
                    <input type="text" id="searchInput" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter Student ID">
                </div>
                <div class="items-center px-4 py-3">
                    <button id="searchButton" class="px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                        Search
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Modal -->
    <div id="resultsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full modal-animation">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white modal-content-animation">
            <div class="flex justify-between items-start">
                <h3 class="text-lg font-medium text-gray-900">Student Information</h3>
                <button onclick="closeResultsModal()" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="studentInfo" class="mt-4">
            </div>
        </div>
    </div>
    <style>
        .modal-animation {
            animation: modalFade 0.3s ease-in-out;
        }
        
        .modal-content-animation {
            animation: modalSlide 0.3s ease-in-out;
        }
        
        @keyframes modalFade {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        
        @keyframes modalSlide {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
    <script>
        function openSearchModal() {
            document.getElementById('searchModal').classList.remove('hidden');
        }

        function closeSearchModal() {
            document.getElementById('searchModal').classList.add('hidden');
            document.getElementById('searchInput').value = '';
        }

        function closeResultsModal() {
            document.getElementById('resultsModal').classList.add('hidden');
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            let searchModal = document.getElementById('searchModal');
            let resultsModal = document.getElementById('resultsModal');
            if (event.target == searchModal) {
                closeSearchModal();
            }
            if (event.target == resultsModal) {
                closeResultsModal();
            }
        }

        // Handle search
        document.getElementById('searchButton').addEventListener('click', function() {
            let searchId = document.getElementById('searchInput').value.trim();
            if(!searchId) {
                alert('Please enter a student ID');
                return;
            }

            $.ajax({
                url: 'search.php',
                type: 'POST',
                data: { search_id: searchId },
                success: function(response) {
                    let data = JSON.parse(response);
                    if(data.status === 'success') {
                        let student = data.data;
                        let html = `
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-3">
                                    <p><strong>ID Number:</strong> ${student.IDNO}</p>
                                    <p><strong>Name:</strong> ${student.First_Name} ${student.Last_Name}</p>
                                    <p><strong>Course:</strong> ${student.Course}</p>
                                    <p><strong>Year Level:</strong> ${student.Year_lvl}</p>
                                </div>
                                <div class="space-y-3">
                                    <p><strong>Email:</strong> ${student.Email || 'Not set'}</p>
                                    <p><strong>Address:</strong> ${student.Address || 'Not set'}</p>
                                    <p><strong>Remaining Sessions:</strong> ${student.remaining_sessions || '30'}</p>
                                </div>
                            </div>
                        `;
                        document.getElementById('studentInfo').innerHTML = html;
                        closeSearchModal();
                        document.getElementById('resultsModal').classList.remove('hidden');
                    } else {
                        alert(data.message);
                    }
                },
                error: function() {
                    alert('Error occurred while searching');
                }
            });
        });

        // Allow Enter key to trigger search
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('searchButton').click();
            }
        });
    </script>
</body>
</html>