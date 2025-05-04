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

// After database connection, add this query to fetch sit-in history
$sql = "
    SELECT 
        'sit_in_records' as source,
        sr.IDNO,
        CONCAT(s.First_Name, ' ', s.Last_Name) as full_name,
        sr.purpose,
        sr.lab_room,
        sr.time_in,
        sr.time_out,
        DATE(sr.time_in) as date
    FROM sit_in_records sr
    JOIN students s ON sr.IDNO = s.IDNO
    WHERE s.First_Name = ?
    UNION ALL
    SELECT 
        'direct_sitin' as source,
        ds.IDNO,
        CONCAT(s.First_Name, ' ', s.Last_Name) as full_name,
        ds.purpose,
        ds.lab_room,
        ds.time_in,
        ds.time_out,
        DATE(ds.time_in) as date
    FROM direct_sitin ds
    JOIN students s ON ds.IDNO = s.IDNO
    WHERE s.First_Name = ?
    ORDER BY time_in DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $firstname, $firstname);
$stmt->execute();
$history_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History</title>
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
<body class="bg-gray-100 dark:bg-gray-900 transition-all duration-300 light">
<div class="nav-overlay" onclick="closeNav()"></div>

<!-- Navigation -->
<nav class="bg-primary shadow-lg">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center">
            <span class="text-white text-xl font-bold py-4">History</span>
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
    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] === 'success'): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">Your feedback has been submitted successfully.</span>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 slide-in-top">
        <h2 class="text-2xl font-bold text-center text-gray-800 dark:text-white mb-6">Reservation History</h2>
        
        <!-- Add your history table here -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">IDNO</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">Sit Purpose</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">Lab Room</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">Login</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">Log out</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">Action</th>
                    </tr>
                    <?php ?>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                    <?php if ($history_result->num_rows > 0): ?>
                        <?php while ($row = $history_result->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-white">
                                    <?= htmlspecialchars($row['IDNO']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    <?= htmlspecialchars($row['full_name']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-white">
                                    <?= htmlspecialchars($row['purpose']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-white">
                                    <?= htmlspecialchars($row['lab_room']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-white">
                                    <?= date('g:i A', strtotime($row['time_in'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-white">
                                    <?= $row['time_out'] ? date('g:i A', strtotime($row['time_out'])) : 'Active' ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-white">
                                    <?= date('M d, Y', strtotime($row['date'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <button onclick="openFeedbackModal('<?= htmlspecialchars($row['IDNO']) ?>', '<?= htmlspecialchars($row['lab_room']) ?>')" 
                                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                        Feedback
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-white">
                                No sit-in history found
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Feedback Modal -->
<div id="feedbackModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center">
    <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-xl w-96">
        <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">Submit Feedback</h2>
        <form id="feedbackForm" action="submit_feedback.php" method="POST">
            <input type="hidden" id="feedbackIdno" name="idno">
            <input type="hidden" id="feedbackLab" name="lab">
            <input type="hidden" name="date" value="<?= date('Y-m-d') ?>">
            
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-white text-sm font-bold mb-2" for="message">
                    Your Feedback
                </label>
                <textarea 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-white dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="message"
                    name="message"
                    rows="4"
                    required
                ></textarea>
            </div>
            
            <div class="flex justify-end space-x-2">
                <button 
                    type="button"
                    onclick="closeFeedbackModal()"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
                >
                    Cancel
                </button>
                <button 
                    type="submit"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                >
                    Submit
                </button>
            </div>
        </form>
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

function openFeedbackModal(idno, lab) {
    document.getElementById('feedbackModal').classList.remove('hidden');
    document.getElementById('feedbackModal').classList.add('flex');
    document.getElementById('feedbackIdno').value = idno;
    document.getElementById('feedbackLab').value = lab;
}

function closeFeedbackModal() {
    document.getElementById('feedbackModal').classList.add('hidden');
    document.getElementById('feedbackModal').classList.remove('flex');
}

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