<?php
session_start();
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

if ($conn->connect_error) {
die("Connection failed: " . $conn->connect_error);
}

// Add these new functions at the top
function getAvailableLabs($conn) {
$sql = "SELECT DISTINCT lab_room FROM lab_rooms WHERE is_active = 1";
$result = $conn->query($sql);
$labs = array();
while($row = $result->fetch_assoc()) {
$labs[] = $row['lab_room'];
}
return $labs;
}

function getProgrammingLanguages() {
return ['ASP.Net', 'C', 'C++', 'C#', 'Java', 'PHP', 'Python'];
}

// Add these validation functions after database connection
function isLabAvailable($conn, $lab_room, $reservation_time) {
$check_sql = "SELECT COUNT(*) as count FROM sit_in_records
WHERE lab_room = ? AND status IN ('active', 'pending')
AND DATE(time_in) = DATE(?)
AND HOUR(time_in) = HOUR(?)";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param('sss', $lab_room, $reservation_time, $reservation_time);
$stmt->execute();
$result = $stmt->get_result();
$count = $result->fetch_assoc()['count'];

// Get lab capacity
$capacity_sql = "SELECT capacity FROM lab_rooms WHERE lab_room = ?";
$stmt = $conn->prepare($capacity_sql);
$stmt->bind_param('s', $lab_room);
$stmt->execute();
$capacity = $stmt->get_result()->fetch_assoc()['capacity'];

return $count < $capacity;
}

function hasExistingReservation($conn, $student_id, $reservation_time) {
$check_sql = "SELECT COUNT(*) as count FROM sit_in_records
WHERE IDNO = ? AND status IN ('active', 'pending')
AND DATE(time_in) = DATE(?)";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param('ss', $student_id, $reservation_time);
$stmt->execute();
$result = $stmt->get_result();
return $result->fetch_assoc()['count'] > 0;
}

// Add new helper functions
function getLabRoomCapacity($conn, $lab_room) {
$sql = "SELECT capacity FROM lab_rooms WHERE lab_room = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $lab_room);
$stmt->execute();
return $stmt->get_result()->fetch_assoc()['capacity'] ?? 0;
}

function getCurrentOccupancy($conn, $lab_room, $time) {
$sql = "SELECT COUNT(*) as count FROM sit_in_records
WHERE lab_room = ? AND status IN ('active', 'pending')
AND DATE(time_in) = DATE(?)
AND HOUR(time_in) = HOUR(?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('sss', $lab_room, $time, $time);
$stmt->execute();
return $stmt->get_result()->fetch_assoc()['count'] ?? 0;
}

// Handle reservation submission
if(isset($_POST['reserve_submit'])) {
$student_id = $_POST['student_id'];
$lab_room = $_POST['lab_room'];
$purpose = $_POST['purpose'];

// Use current time instead of user input
$time_in_datetime = date('Y-m-d H:i:s');

// Input validation
$errors = [];

// Check if lab is currently open (7 AM to 8 PM)
$current_hour = (int)date('H');
if ($current_hour < 7 || $current_hour >= 20) {
$errors[] = "Lab rooms are only available between 7:00 AM and 8:00 PM.";
}

// If no errors, proceed with direct sit-in
if (empty($errors)) {
$insert_sql = "INSERT INTO direct_sitin (IDNO, lab_room, purpose, time_in, status) 
VALUES (?, ?, ?, ?, 'active')";
$stmt = $conn->prepare($insert_sql);
$stmt->bind_param('ssss', $student_id, $lab_room, $purpose, $time_in_datetime);

if($stmt->execute()) {
echo json_encode([
'status' => 'success', 
'message' => 'Direct sit-in created successfully!',
'time' => date('g:i A', strtotime($time_in_datetime))
]);
} else {
echo json_encode(['status' => 'error', 'message' => 'Failed to create direct sit-in']);
}
} else {
echo json_encode(['status' => 'error', 'message' => implode(' ', $errors)]);
}
exit();
}

// Handle AJAX search request
if(isset($_POST['search_id'])) {
$search_id = $_POST['search_id'];
$sql = "SELECT s.*, COALESCE(ss.remaining_sessions, 30) as remaining_sessions, s.profile_image 
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="../css/admin-dark-mode.css">
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
                <span class="text-white text-xl font-bold py-4">Student Search</span>
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
<!-- Search Button -->
<div class="max-w-7xl mx-auto px-4 py-6">
    <button onclick="openSearchModal()" 
        class="group relative bg-primary hover:bg-blue-700 text-white px-8 py-4 rounded-lg 
        transition-all duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-lg 
        flex items-center space-x-3 overflow-hidden">
        <div class="absolute inset-0 w-3 bg-blue-600 transition-all duration-300 ease-in-out transform 
            group-hover:w-full opacity-50"></div>
        <i class="fas fa-search text-xl relative z-10"></i>
        <span class="font-semibold text-lg relative z-10">Search Student</span>
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
<div id="resultsModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 hidden overflow-y-auto h-full w-full modal-animation flex items-center justify-center">
    <div class="relative mx-auto p-8 border-0 w-11/12 md:w-3/4 lg:w-2/3 xl:w-1/2 shadow-2xl rounded-xl bg-white dark:bg-gray-800 modal-content-animation max-h-[90vh] overflow-y-auto">
        <!-- Close button -->
        <button onclick="closeResultsModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors duration-200">
            <i class="fas fa-times text-xl"></i>
        </button>

        <!-- Header -->
        <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-6 pb-2 border-b border-gray-200">Student Information</h3>
        
        <!-- Content container -->
        <div id="studentInfo" class="space-y-6">
            <!-- Dynamic content will be inserted here -->
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
// Get profile image path from database or use default
let profileImage = student.profile_image && student.profile_image !== '' 
? '../' + student.profile_image  // Add parent directory prefix
: "https://cdn-icons-png.flaticon.com/512/2815/2815428.png";

let html = `
<input type="hidden" id="student_data" value='${JSON.stringify(student)}'>
<div class="flex flex-col md:flex-row gap-8">
    <div class="flex-shrink-0 flex justify-center">
        <div class="relative group">
            <img src="${profileImage}" 
                alt="Profile" 
                class="w-48 h-48 rounded-xl object-cover border-4 border-blue-200 shadow-lg transform transition-transform duration-300 group-hover:scale-105"
                onerror="this.src='https://cdn-icons-png.flaticon.com/512/2815/2815428.png'">
            <div class="absolute inset-0 bg-blue-500 opacity-0 group-hover:opacity-10 rounded-xl transition-opacity duration-300"></div>
        </div>
    </div>
    <div class="flex-grow space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div class="p-4 bg-gray-50 dark:bg-gray-700 dark:text-white rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200">
                    <p class="flex items-center">
                        <i class="fas fa-id-card text-blue-500 w-6"></i>
                        <span class="ml-3"><strong>ID Number:</strong> ${student.IDNO}</span>
                    </p>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-700 dark:text-white rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200">
                    <p class="flex items-center">
                        <i class="fas fa-user text-blue-500 w-6"></i>
                        <span class="ml-3"><strong>Name:</strong> ${student.First_Name} ${student.Last_Name}</span>
                    </p>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-700 dark:text-white rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200">
                    <p class="flex items-center">
                        <i class="fas fa-graduation-cap text-blue-500 w-6"></i>
                        <span class="ml-3"><strong>Course:</strong> ${student.Course}</span>
                    </p>
                </div>
            </div>
            <div class="space-y-4">
                <div class="p-4 bg-gray-50 dark:bg-gray-700 dark:text-white rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200">
                    <p class="flex items-center">
                        <i class="fas fa-layer-group text-blue-500 w-6"></i>
                        <span class="ml-3"><strong>Year Level:</strong> ${student.Year_lvl}</span>
                    </p>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-700 dark:text-white rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200">
                    <p class="flex items-center">
                        <i class="fas fa-envelope text-blue-500 w-6"></i>
                        <span class="ml-3"><strong>Email:</strong> ${student.Email || 'Not set'}</span>
                    </p>
                </div>
                <div class="p-4 bg-blue-50 dark:bg-blue-900 dark:text-white rounded-lg hover:bg-blue-100 dark:hover:bg-blue-800 transition-colors duration-200">
                    <p class="flex items-center">
                        <i class="fas fa-clock text-blue-500 w-6"></i>
                        <span class="ml-3 font-bold text-blue-800 dark:text-blue-200">
                            <strong>Remaining Sessions:</strong> ${student.remaining_sessions}
                        </span>
                    </p>
                </div>
            </div>
        </div>
        <button onclick="openReservationModal('${student.IDNO}', '${student.First_Name} ${student.Last_Name}')"
            class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-lg 
            transform transition-all duration-200 hover:scale-[1.02] hover:shadow-lg flex items-center justify-center space-x-2">
            <i class="fas fa-sign-in-alt"></i>
            <span>Start Sit-in Session</span>
        </button>
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

// Update the openReservationModal function in the JavaScript
function openReservationModal(studentId, studentName) {
document.getElementById('reservationModal').classList.remove('hidden');
document.getElementById('student_id').value = studentId;
document.getElementById('student_name').value = studentName;

// Get the remaining sessions from the existing student info
let studentInfo = JSON.parse(document.getElementById('student_data').value);
document.getElementById('remaining_sessions').textContent = studentInfo.remaining_sessions;
}

function closeReservationModal() {
document.getElementById('reservationModal').classList.add('hidden');
}

<?php if(isset($message)): ?>
Swal.fire({
title: '<?= $success ? 'Success!' : 'Error!' ?>',
text: '<?= $message ?>',
icon: '<?= $success ? 'success' : 'error' ?>',
confirmButtonColor: '#3085d6'
});
<?php endif; ?>
</script>

<!-- Reservation Modal -->
<div id="reservationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
<div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
<div class="mt-3 text-center">
<h3 class="text-lg leading-6 font-medium text-gray-900">New Reservation</h3>
<form id="reservationForm" method="POST" class="mt-4">
<input type="hidden" id="student_id" name="student_id">
<div class="mb-4">
<label class="block text-gray-700 text-sm font-bold mb-2">Student Name:</label>
<input type="text" id="student_name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight" readonly>
</div>
<div class="mb-4">
<label class="block text-gray-700 text-sm font-bold mb-2">Remaining Sessions:</label>
<p id="remaining_sessions" class="text-lg font-bold text-blue-600"></p>
</div>
<div class="mb-4">
<label class="block text-gray-700 text-sm font-bold mb-2">Lab Room:</label>
<select name="lab_room" id="lab_room" class="shadow border rounded w-full py-2 px-3 text-gray-700" required>
<option value="">Select Lab Room</option>
<option value="Lab 524">Lab 524</option>
<option value="Lab 526">Lab 526</option>
<option value="Lab 528">Lab 528</option>
<option value="Lab 530">Lab 530</option>
<option value="Lab 542">Lab 542</option>
</select>
</div>

<div class="mb-4">
<label class="block text-gray-700 text-sm font-bold mb-2">Purpose:</label>
<select name="purpose" class="shadow border rounded w-full py-2 px-3 text-gray-700" required>
<?php foreach(getProgrammingLanguages() as $language): ?>
<option value="<?= htmlspecialchars($language) ?>"><?= htmlspecialchars($language) ?></option>
<?php endforeach; ?>
</select>
</div>
<div class="flex justify-between mt-4">
<button type="button" onclick="closeReservationModal()"
class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
Cancel
</button>
<button type="submit" name="reserve_submit"
class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
Submit
</button>
</div>
<!-- Add warning messages div -->
<div id="warningMessages" class="mt-4 text-sm text-red-600 hidden"></div>
</form>
</div>
</div>
</div>
<script>
// Enhanced success/error messages
<?php if(isset($message)): ?>
Swal.fire({
title: '<?= $success ? 'Success!' : 'Error!' ?>',
html: '<?= $message ?>',
icon: '<?= $success ? 'success' : 'error' ?>',
confirmButtonColor: '#3085d6',
timer: <?= $success ? '3000' : 'null' ?>,
timerProgressBar: <?= $success ? 'true' : 'false' ?>
});
<?php endif; ?>
// Add form submission handler
document.getElementById('reservationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    let formData = new FormData(this);
    formData.append('reserve_submit', '1');

    fetch('search.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.status === 'success') {
            Swal.fire({
                title: 'Success!',
                text: data.message,
                icon: 'success',
                confirmButtonColor: '#3085d6'
            }).then((result) => {
                if (result.isConfirmed) {
                    closeReservationModal();
                    location.reload();
                }
            });
        } else {
            Swal.fire({
                title: 'Error!',
                text: data.message,
                icon: 'error',
                confirmButtonColor: '#3085d6'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: 'Error!',
            text: 'An error occurred while submitting the reservation',
            icon: 'error',
            confirmButtonColor: '#3085d6'
        });
    });
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
</body>
</html>
<!-- Add this CSS in the head section or your stylesheet -->
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