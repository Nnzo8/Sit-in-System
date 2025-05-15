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

$pcs = [];
if(isset($_POST['check_availability'])) {
    $lab_room = $_POST['lab_room'];
    $date = $_POST['date'];
    $time = $_POST['time_in'];
    $time_in = $date . ' ' . $time . ':00';
    $pcs = getAvailablePCs($conn, $lab_room, $time_in);
}

// Handle reservation submission
if(isset($_POST['submit_reservation'])) {
    $student_id = $_SESSION['IDNO'];
    $lab_room = $_POST['lab_room'];
    $pc_number = $_POST['pc_number'];
    $purpose = $_POST['purpose'];
    $date = $_POST['date'];
    $time = $_POST['time_in'];
    $time_out = date('H:i', strtotime($time . ' +1 hour')); // Set time_out to 1 hour after time_in

    // Check if student has an existing reservation for the same time slot
    $check_sql = "SELECT * FROM reservation WHERE IDNO = ? AND reservation_date = ? AND 
                  ((time_in <= ? AND time_out >= ?) OR (time_in <= ? AND time_out >= ?))";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ssssss", $student_id, $date, $time, $time, $time_out, $time_out);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        echo "<script>
            setTimeout(function() {
                Swal.fire({
                    title: 'Error!',
                    text: 'You already have a reservation for this time slot',
                    icon: 'error',
                    confirmButtonColor: '#000080'
                });
            }, 100);
        </script>";
    } else {
        // Insert new reservation
        $insert_sql = "INSERT INTO reservation (reservation_date, time_in, time_out, pc, lab, purpose, IDNO, status) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')";
        $stmt = $conn->prepare($insert_sql);
        $time_in_int = (int)str_replace(':', '', $time);
        $time_out_int = (int)str_replace(':', '', $time_out);
        $stmt->bind_param("siiissi", $date, $time_in_int, $time_out_int, $pc_number, $lab_room, $purpose, $student_id);
        
        if($stmt->execute()) {
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
        } else {
            echo "<script>
                setTimeout(function() {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Error creating reservation: " . $conn->error . "',
                        icon: 'error',
                        confirmButtonColor: '#000080'
                    });
                }, 100);
            </script>";
        }
    }
}

// Get student's reservation history - updated to use reservation table
$history_sql = "SELECT * FROM reservation WHERE IDNO = ? ORDER BY reservation_date DESC, time_in DESC LIMIT 5";
$stmt = $conn->prepare($history_sql);
$stmt->bind_param("i", $_SESSION['IDNO']);
$stmt->execute();
$history = $stmt->get_result();

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
<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <title>Reservation</title>
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
    <link rel="stylesheet" href="css/user-dark-mode.css">
    <style>
        * {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
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
</head>
<body class="light">
    <!-- Navigation -->
    <nav class="bg-primary shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center">
                <span class="text-white text-xl font-bold py-4">Reservation</span>
                <div class="flex space-x-4">
                    <div class="hidden md:flex items-center space-x-4">
                        <a href="index.php" class="nav-link text-white hover:text-gray-200">Home</a>
                        <a href="edit_profile.php" class="nav-link text-white hover:text-gray-200">Edit</a>
                        <a href="resources.php" class="nav-link text-white hover:text-gray-200">Resources</a>
                        <a href="reservation.php" class="nav-link text-white hover:text-gray-200">Reservation</a>
                        <a href="history.php" class="nav-link text-white hover:text-gray-200">History</a>
                        <a href="login.php" class="nav-link text-white hover:text-gray-200">Logout</a>
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
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 slide-in-top">
            <h2 class="text-2xl font-bold text-center text-gray-800 dark:text-white mb-6">Make a Reservation</h2>

            <form action="reservation.php" method="post" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">IDNO: </label>
                        <input type="text" name="idno" class="form-control bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white" value="<?php echo htmlspecialchars($idno); ?>" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Lab Room: </label>
                        <select name="lab_room" class="form-control bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white" required id="lab_room">
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
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Student Name: </label>
                        <input type="text" name="student_name" class="form-control bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white" value="<?php echo htmlspecialchars($studentName); ?>" readonly>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date: </label>
                        <input type="date" name="date" class="form-control bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white" required id="date">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Time In: </label>
                        <input type="time" 
                               name="time_in" 
                               class="form-control bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white" 
                               id="time_in"
                               min="07:30"
                               max="20:00"
                               value="07:30"
                               required> 
                        <small class="text-gray-500 dark:text-gray-400">Lab hours: 7:30 AM - 8:00 PM</small>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Purpose: </label>
                        <select name="purpose" class="form-control bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white" required>
                            <option value="">Select Purpose</option>
                            <option value="ASP.Net">ASP.Net</option>
                            <option value="C">C</option>
                            <option value="C++">C++</option>
                            <option value="C#">C#</option>
                            <option value="Java">Java</option>
                            <option value="PHP">PHP</option>
                            <option value="Python">Python</option>
                            <area value="Database">Database</option>
                            <option value="Digital Logic and Design">Digital Logic and Design</option>
                            <option value="Embedded System & IOT">Embedded System & IOT</option>
                            <option value="SysArch">SysArch</option>
                            <option value="Computer Application">Computer Application</option>
                            <option value="Webdev">Webdev</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">PC Number: </label>
                        <select name="pc_number" class="form-control bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white" required id="pc_select">
                            <option value="">Select Lab Room First</option>
                        </select>
                    </div>

                    <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg col-span-2">
                        <label class="block text-sm font-medium text-blue-700 dark:text-blue-300">Remaining Sessions: </label>
                        <p class="text-2xl font-bold text-blue-800 dark:text-blue-100"><?php echo $remainingSessions; ?></p>
                        <?php if ($remainingSessions <= 5): ?>
                            <p class="text-sm text-red-600 dark:text-red-400 mt-1"> Low sessions remaining!</p>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="w-full btn-primary" 
                            <?php echo $remainingSessions <= 0 ? 'disabled' : ''; ?> 
                            name="submit_reservation">
                        <?php echo $remainingSessions <= 0 ? 'No Sessions Available' : 'Submit Reservation'; ?>
                    </button>
                </div>
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

    // Update PC availability when lab, date, or time changes
    document.addEventListener('DOMContentLoaded', function() {
        const labSelect = document.getElementById('lab_room');
        const dateInput = document.getElementById('date');
        const timeInput = document.getElementById('time_in');
        const pcSelect = document.getElementById('pc_select');

        // Set current date as minimum date
        const today = new Date().toISOString().split('T')[0];
        dateInput.min = today;
        dateInput.value = today;

        function updatePCAvailability() {
            if (labSelect.value) {
                const currentDate = dateInput.value || today;
                const currentTime = timeInput.value || '07:30';
                
                // Create form data
                const formData = new FormData();
                formData.append('lab_room', labSelect.value);
                formData.append('date', currentDate);
                formData.append('time_in', currentTime);

                // Fetch PC availability including disabled status
                fetch('check_pc_availability.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    pcSelect.innerHTML = '<option value="">Select PC</option>';
                    
                    data.forEach(pc => {
                        const option = document.createElement('option');
                        option.value = pc.number;
                        
                        if (pc.is_disabled) {
                            option.textContent = `PC ${pc.number} (Under Maintenance${pc.disabled_reason ? ': ' + pc.disabled_reason : ''})`;
                            option.disabled = true;
                            option.classList.add('text-red-500');
                        } else {
                            option.textContent = `PC ${pc.number} (${pc.available ? 'Available' : 'In Use'})`;
                            option.disabled = !pc.available;
                        }
                        
                        pcSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    pcSelect.innerHTML = '<option value="">Error loading PCs</option>';
                });
            } else {
                pcSelect.innerHTML = '<option value="">Select Lab Room First</option>';
            }
        }

        // Update PC availability when lab room changes
        labSelect.addEventListener('change', updatePCAvailability);
        
        // Also update when date or time changes
        dateInput.addEventListener('change', () => {
            if (labSelect.value) updatePCAvailability();
        });
        timeInput.addEventListener('change', () => {
            if (labSelect.value) updatePCAvailability();
        });

        // Preserve selections if they exist
        <?php if(isset($_POST['lab_room'])): ?>
        labSelect.value = <?= json_encode($_POST['lab_room']) ?>;
        updatePCAvailability();
        <?php endif; ?>
        
        <?php if(isset($_POST['date'])): ?>
        dateInput.value = <?= json_encode($_POST['date']) ?>;
        <?php endif; ?>
        
        <?php if(isset($_POST['time_in'])): ?>
        timeInput.value = <?= json_encode($_POST['time_in']) ?>;
        <?php endif; ?>
    });

    document.addEventListener('DOMContentLoaded', function() {
        const darkModeToggle = document.getElementById('darkModeToggle');
        const html = document.documentElement;
        
        const darkMode = localStorage.getItem('userDarkMode');
        if (darkMode === 'enabled') {
            html.classList.add('dark');
        }
        
        darkModeToggle.addEventListener('click', function() {
            html.classList.toggle('dark');
            if (html.classList.contains('dark')) {
                localStorage.setItem('userDarkMode', 'enabled');
            } else {
                localStorage.setItem('userDarkMode', null);
            }
        });
    });
    </script>
</body>
</html>