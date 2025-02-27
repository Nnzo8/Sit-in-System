<?php
session_start();
include 'header.php';
$servername = "localhost";
$username = "root";
$password = "";
$database = "users";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the username is already stored in the session
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
} else {
    // If not, check if it was passed as a GET parameter (e.g., after login)
    if (isset($_GET['username'])) {
        $username = $_GET['username'];
        $_SESSION['username'] = $username; // Store it in the session
    } else {
        // If not in session or GET, handle the case where the user is not logged in
        $username = "Guest"; // Or redirect to login page
    }
}
// Get current user data
$firstname = $_SESSION['firstname'];
$sql = "SELECT s.profile_image, ss.remaining_sessions 
        FROM students s 
        LEFT JOIN student_session ss ON s.IDNO = ss.id_number 
        WHERE s.First_Name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $firstname);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User not found!";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History</title>
</head>
<body>
<div class="nav-overlay" onclick="closeNav()"></div>

<!-- Navigation -->
<nav class="bg-primary shadow-lg">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-end">
            <div class="flex space-x-4">
                <div class="hidden md:flex items-center space-x-4">
                    <a href="index.php" class="nav-link text-white hover:text-gray-200">Home</a>
                    <a href="edit_profile.php" class="nav-link text-white hover:text-gray-200">Edit</a>
                    <a href="reservation.php" class="nav-link text-white hover:text-gray-200">Reservation</a>
                    <a href="history.php" class="nav-link text-white hover:text-gray-200">History</a>
                    <a href="login.php" class="nav-link text-white hover:text-gray-200">Logout</a>
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

<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6 slide-in-top">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Reservation History</h2>
        
        <!-- Add your history table here -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IDNO</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sit Purpose</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lab Room</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Login</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Log out</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add this script before the closing body tag -->
<script>
function toggleNav() {
    document.getElementById('navbarNav').classList.toggle('show');
    document.querySelector('.nav-overlay').classList.toggle('show');
}

function closeNav() {
    document.getElementById('navbarNav').classList.remove('show');
    document.querySelector('.nav-overlay').classList.remove('show');
}

// Close nav when clicking outside
document.addEventListener('click', function(event) {
    const nav = document.getElementById('navbarNav');
    const toggleBtn = document.querySelector('.navbar-toggler');
    if (!nav.contains(event.target) && !toggleBtn.contains(event.target)) {
        closeNav();
    }
});
</script>
</body>
</html>