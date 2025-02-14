<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="homestyle.css">
</head>
<body>
<?php
session_start(); 
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
$sql = "SELECT profile_image FROM students WHERE First_Name = ?";
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
?>

<!-- Add the overlay div -->
<div class="nav-overlay" onclick="closeNav()"></div>

<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" onclick="toggleNav()" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="edit_profile.php">Edit</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="reservation.php">Reservation</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="history.php">History</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="login.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

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
<header>
    <h1>Welcome to CCS Sit-in Monitoring System, <?php echo $username ?></h1>
</header>

<div class="flex-container">
    <!-- User Information -->
    <div class="container">
        <div class="center-container">
            <h5>User Information</h5>
        </div>
        <img src="<?php echo htmlspecialchars($profileImage); ?>" class="img-fluid rounded-circle">
        <p><b>Name:</b> <?php echo $_SESSION['firstname'] . " " . $_SESSION['lastname']; ?></p>
        <p><b>Course:</b> <?php echo $_SESSION['course']; ?></p>
        <p><b>Year Level:</b> <?php echo $_SESSION['yearlvl']; ?></p>
    </div>

    <!-- Announcements -->
    <div class="container">
        <div class="center-container">
            <h5>Announcements</h5>
        </div>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">First Semester Enrollment</h5>
                <p class="card-text">Enrollment for the first semester is now open. Please proceed to the registrar's office for enrollment.</p>
            </div>
        </div>
    </div>

    <!-- Rules and Regulations -->
    <div class="container">
        <div class="center-container">
            <h5>Rules and Regulations</h5>
        </div>
        <p>Please follow these guidelines while attending sit-in sessions:</p>
        <ul>
            <li>Maintain silence in the classroom.</li>
            <li>Be punctual.</li>
            <li>Respect faculty and staff.</li>
            <li>No food or drinks allowed inside the classroom.</li>
        </ul>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+6p6m5Y5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r