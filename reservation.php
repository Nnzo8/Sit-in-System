<?php
session_start();
include 'header.php';

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "users";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get student's remaining sessions
$idno = $_SESSION['IDNO'];
$sql = "SELECT remaining_sessions FROM student_session WHERE id_number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idno);
$stmt->execute();
$result = $stmt->get_result();
$sessions = $result->fetch_assoc();

// Set default value if no record found
$remainingSessions = $sessions['remaining_sessions'] ?? 30;

// Get student info for the form
$studentName = $_SESSION['firstname'] . ' ' . $_SESSION['lastname'];
?>

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
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Make a Reservation</h2>

        <form action="reservation.php" method="post" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">IDNO: </label>
                    <input type="text" name="idno" class="form-control" value="<?php echo htmlspecialchars($idno); ?>" readonly>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Lab Room: </label>
                    <select name="lab_room" class="form-control" required>
                        <option value="">Select Lab Room</option>
                        <option value="Lab 522">Lab 522</option>
                        <option value="Lab 524">Lab 524</option>
                        <option value="Lab 530">Lab 530</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Student Name: </label>
                    <input type="text" name="student_name" class="form-control" value="<?php echo htmlspecialchars($studentName); ?>" readonly>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Date: </label>
                    <input type="date" name="date" class="form-control" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Time In: </label>
                    <input type="time" name="time_in" class="form-control" 
                        min="07" max="20" step="3600" required> 
                    <small class="text-gray-500">Lab hours: 7:30 AM - 8:00 PM</small>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Purpose: </label>
                    <textarea name="purpose" class="form-control" rows="3" required></textarea>
                </div>
                <div class="bg-blue-50 p-4 rounded-lg col-span-2">
                    <label class="block text-sm font-medium text-blue-700">Remaining Sessions: </label>
                    <p class="text-2xl font-bold text-blue-800"><?php echo $remainingSessions; ?></p>
                    <?php if ($remainingSessions <= 5): ?>
                        <p class="text-sm text-red-600 mt-1">⚠️ Low sessions remaining!</p>
                    <?php endif; ?>
                </div>
            </div>

            <button type="submit" class="w-full btn-primary" <?php echo $remainingSessions <= 0 ? 'disabled' : ''; ?>>
                <?php echo $remainingSessions <= 0 ? 'No Sessions Available' : 'Submit Reservation'; ?>
            </button>
        </form>
    </div>
</div>

<script>
// Add form submission handling
document.querySelector('form').addEventListener('submit', function(e) {
    if (<?php echo $remainingSessions; ?> <= 0) {
        e.preventDefault();
        alert('You have no remaining sessions available.');
        return false;
    }
});
</script>
