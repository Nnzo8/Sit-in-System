<?php
session_start();
include 'header.php';
require_once 'includes/sitin_functions.php';

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "users";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle reservation submission
if(isset($_POST['submit_reservation'])) {
    $student_id = $_SESSION['IDNO'];
    $lab_room = $_POST['lab_room'];
    $pc_number = 1; // You can make this dynamic if needed
    $purpose = $_POST['purpose'];
    $date = $_POST['date'];
    $time = $_POST['time_in'];
    $time_in = $date . ' ' . $time . ':00';

    // Check if student has active sit-in
    $active_sitin = getActiveSitIn($conn, $student_id);
    
    if($active_sitin) {
        $error = "You already have an active sit-in session.";
    } else {
        if(createSitInRecord($conn, $student_id, $lab_room, $pc_number, $purpose, $time_in)) {
            $success = "Sit-in reservation created successfully!";
            echo "<script>
                setTimeout(function() {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Your reservation has been submitted and is pending approval',
                        icon: 'success',
                        confirmButtonColor: '#000080'
                    });
                }, 100);
            </script>";
            
            // Update remaining sessions
            $sql = "UPDATE student_session SET remaining_sessions = remaining_sessions - 1 
                   WHERE id_number = ? AND remaining_sessions > 0";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $student_id);
            $stmt->execute();
        } else {
            $error = "Error creating reservation.";
        }
    }
}

// Get student's sit-in history
$history = getSitInHistory($conn, $_SESSION['IDNO'], 5);

// Get student's remaining sessions
$idno = $_SESSION['IDNO'];
$sql = "SELECT remaining_sessions FROM student_session WHERE id_number = ?";  // Changed IDNO to id_number
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $idno);
$stmt->execute();
$result = $stmt->get_result();
$sessions = $result->fetch_assoc();

// If no record exists, create one with default value
if (!$sessions) {
    $sql = "INSERT INTO student_session (id_number, remaining_sessions) VALUES (?, 30)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $idno);
    $stmt->execute();
    $remainingSessions = 30;
} else {
    $remainingSessions = $sessions['remaining_sessions'];
}

// Get student info for the form
$studentName = $_SESSION['firstname'] . ' ' . $_SESSION['lastname'];
?>
<title>Reservation</title>
<!-- Navigation -->
<nav class="bg-primary shadow-lg">
    <div class="max-w-7xl mx-auto px-4">
    <div class="flex justify-between items-center">
    <span class="text-white text-xl font-bold py-4">Reservation</span>
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
                        <option value="Lab 524">Lab 524</option>
                        <option value="Lab 526">Lab 526</option>
                        <option value="Lab 528">Lab 528</option>
                        <option value="Lab 530">Lab 530</option>
                        <option value="Lab 542">Lab 542</option>
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
                    <input type="time" 
                           name="time_in" 
                           class="form-control" 
                           min="07:30"
                           max="20:00"
                           value="07:30"
                           required> 
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
                        <p class="text-sm text-red-600 mt-1"> Low sessions remaining!</p>
                    <?php endif; ?>
                </div>
            </div>

            <button type="submit" class="w-full btn-primary" <?php echo $remainingSessions <= 0 ? 'disabled' : ''; ?> name="submit_reservation">
                <?php echo $remainingSessions <= 0 ? 'No Sessions Available' : 'Submit Reservation'; ?>
            </button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Add form submission handling
document.querySelector('form').addEventListener('submit', function(e) {
    const timeInput = document.querySelector('input[name="time_in"]');
    const selectedTime = timeInput.value;
    const [hours, minutes] = selectedTime.split(':').map(Number);
    const time = hours * 60 + minutes;

    // Convert lab hours to minutes for comparison
    const minTime = 7 * 60 + 30;  // 7:30 AM
    const maxTime = 20 * 60;      // 8:00 PM

    if (time < minTime || time > maxTime) {
        e.preventDefault();
        alert('Please select a time between 7:30 AM and 8:00 PM');
        return false;
    }

    if (<?php echo $remainingSessions; ?> <= 0) {
        e.preventDefault();
        alert('You have no remaining sessions available.');
        return false;
    }
});
</script>