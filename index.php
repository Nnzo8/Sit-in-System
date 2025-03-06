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

$profileImage = !empty($user['profile_image']) && file_exists($user['profile_image']) 
    ? $user['profile_image'] 
    : "https://cdn-icons-png.flaticon.com/512/2815/2815428.png";
    
// Set default value of 30 if no sessions are found
$remainingSessions = $user['remaining_sessions'] ?? 30;
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    hr {
        border: none;
        height: 1px;
        background-color: #000000; 
        margin: 10px 0;
    }
    
    .scrollable-content {
        max-height: 500px;
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: #718096 #EDF2F7;
    }
    
    /* Webkit browsers custom scrollbar */
    .scrollable-content::-webkit-scrollbar {
        width: 8px;
    }
    
    .scrollable-content::-webkit-scrollbar-track {
        background: #EDF2F7;
        border-radius: 4px;
    }
    
    .scrollable-content::-webkit-scrollbar-thumb {
        background: #718096;
        border-radius: 4px;
    }
</style>
<title>Home</title>
<!-- Navigation -->
<nav class="bg-primary shadow-lg">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center">
            <span class="text-white text-xl font-bold py-4">Dashboard</span>
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

<!-- Header -->
<header class="text-black py-6 slide-in-top text-center">
    <div class="container text-center">
        <h1>Welcome to CCS Sit-in Monitoring System, <?php echo htmlspecialchars($firstname) ?></h1>
    </div>
</header>

<!-- Main Content -->
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- User Information Card -->
        <div class="bg-white rounded-lg shadow-lg p-6 h-fit slide-in-top animation-delay-100"> 
            <h5 class="text-xl font-semibold mb-4 text-center">User Information</h5>
            <div class="flex flex-col items-center">
                <img src="<?php echo htmlspecialchars($profileImage); ?>" 
                     alt="Profile" 
                     class="w-24 h-24 rounded-full mb-3 object-cover">
                <div class="space-y-1">
                <hr>
                    <p><i class="fas fa-id-card mr-2"></i><span class="font-semibold">ID:</span> <?php echo htmlspecialchars($_SESSION['IDNO']); ?></p>
                    <p><i class="fas fa-user mr-2"></i><span class="font-semibold">Name:</span> <?php echo htmlspecialchars($_SESSION['firstname'] . " " . $_SESSION['lastname']); ?></p>
                    <p><i class="fas fa-graduation-cap mr-2"></i><span class="font-semibold">Course:</span> <?php echo htmlspecialchars($_SESSION['course']); ?></p>
                    <p><i class="fas fa-calendar mr-2"></i><span class="font-semibold">Year:</span> <?php echo htmlspecialchars($_SESSION['yearlvl']); ?></p>
                    <p><i class="fas fa-envelope mr-2"></i><span class="font-semibold">Email:</span> <?php echo htmlspecialchars($_SESSION['Email'] ?? 'Not set'); ?></p>
                    <p class="max-w-xs break-words"><i class="fas fa-home mr-2"></i><span class="font-semibold">Address:</span> <?php echo htmlspecialchars($_SESSION['Address'] ?? 'Not set'); ?></p>
                    <p><i class="fas fa-clock mr-2"></i><span class="font-semibold">Remaining Sessions:</span> <?php echo htmlspecialchars($remainingSessions); ?></p>
                </div>
            </div>
        </div>

        <!-- Announcements Card -->
        <div class="bg-white rounded-lg shadow-lg p-6 h-fit slide-in-top animation-delay-200">
            <h5 class="text-xl font-semibold mb-4 text-center">Announcements</h5>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h6 class="font-semibold text-blue-800 mb-2">UC-CCS ADMIN</h6>
                <p class="text-blue-600">The College of Computer Studies will open the registration of students for the Sit-in privilege starting tomorrow. Thank you! Lab Supervisor</p>
            </div>
        </div>

        <!-- Rules and Regulations Card -->
        <div class="bg-white rounded-lg shadow-lg p-6 h-fit slide-in-top animation-delay-300">
            <h5 class="text-xl font-semibold mb-4 text-center">Laboratory Rules and Regulations</h5>
            <div class="space-y-2 text-sm scrollable-content">
                <p class="font-semibold mb-2">University of Cebu - College of Information & Computer Studies</p>
                <p class="mb-4">To avoid embarrassment and maintain camaraderie with your friends and superiors at our laboratories, please observe the following:</p>
                <ul class="list-decimal pl-5 space-y-2">
                    <li>Maintain silence, proper decorum, and discipline inside the laboratory.</li>
                    <li>Games are not allowed inside the lab.</li>
                    <li>Surfing the Internet is allowed only with permission.</li>
                    <li>Getting access to other websites not related to the course (especially pornographic and illicit sites) is strictly prohibited.</li>
                    <li>Deleting computer files and changing the set-up of the computer is a major offense.</li>
                    <li>Observe computer time usage carefully. A fifteen-minute allowance is given for each use. Otherwise, the unit will be given to those who wish to "sit-in".</li>
                    <li>Observe proper decorum while inside the laboratory.</li>
                    <p>Do not get inside the lab unless the instructor is present.
                    All bags, knapsacks, and the likes must be deposited at the counter.
                    Follow the seating arrangement of your instructor.
                    At the end of class, all software programs must be closed.
                    Return all chairs to their proper places after using.</p>
                    <li>Chewing gum, eating, drinking, smoking, and other forms of vandalism are prohibited inside the lab.</li>
                    <li>Anyone causing a continual disturbance will be asked to leave the lab. Acts or gestures offensive to the members of the community, including public display of physical intimacy, are not tolerated.</li>
                    <li>Persons exhibiting hostile or threatening behavior such as yelling, swearing, or disregarding requests made by lab personnel will be asked to leave the lab.</li>
                    <li>For serious offense, the lab personnel may call the Civil Security Office (CSU) for assistance.</li>
                    <li>Any technical problem or difficulty must be addressed to the laboratory supervisor, student assistant or instructor immediately.</li>
                </ul>
                <hr>
                <p class="font-semibold mb-2">Disciplinary Actions: </p>
                <ul class="list-decimal pl-5 space-y-2">
                    <li>First Offense - The Head or the Dean or OIC recommends to the Guidance Center for a suspension from classes for each offender.</li>
                    <li>Second and Subsequent Offenses - A recommendation for a heavier sanction will be endorsed to the Guidance Center.</li>
                </ul>       
            </div>
        </div>
    </div>
</div>

<script>
function toggleNav() {
    document.getElementById('navbarNav').classList.toggle('hidden');
}

// Close nav when clicking outside
document.addEventListener('click', function(event) {
    const nav = document.getElementById('navbarNav');
    const toggleBtn = document.querySelector('.mobile-menu-button');
    if (!nav.contains(event.target) && !toggleBtn.contains(event.target)) {
        nav.classList.add('hidden');
    }
});
</script>

</body>
</html>