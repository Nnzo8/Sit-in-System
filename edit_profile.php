<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit</title>
    <link rel="stylesheet" href="">
</head>
<body>
<div class="nav-overlay" onclick="closeNav()"></div>

<?php 
session_start();
include 'header.php';

if (!isset($_SESSION['firstname'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$database = "users";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_SESSION['firstname'];
    $newfirstname = $_POST['First_Name'];
    $newlastname = $_POST['Last_Name'];
    $newmidname = $_POST['Mid_Name'];
    $newcourse = $_POST['Course'];
    $newyearlvl = $_POST['Year_lvl'];
    $newidno = $_POST['IDNO'];
    $newemail = $_POST['Email'];
    $newaddress = $_POST['Address'];
    
    // Get current user data to check existing profile image
    $currentUserQuery = "SELECT profile_image FROM students WHERE First_Name = ?";
    $stmt = $conn->prepare($currentUserQuery);
    $stmt->bind_param("s", $firstname);
    $stmt->execute();
    $currentUser = $stmt->get_result()->fetch_assoc();
    
    // Handle image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'jfif'];
        $filename = $_FILES['profile_image']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (in_array(strtolower($filetype), $allowed)) {
            $target_dir = "uploads/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            // Delete old profile image if it exists
            if (!empty($currentUser['profile_image']) && file_exists($currentUser['profile_image'])) {
                unlink($currentUser['profile_image']);
            }
            
            $new_filename = uniqid() . '.' . $filetype;
            $target_file = $target_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                // Update with new image
                $sql = "UPDATE students SET IDNO=?, Last_Name=?, First_Name=?, Mid_Name=?, Course=?, Year_lvl=?, Email=?, Address=?, profile_image=? WHERE First_Name=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("isssssssss", $newidno, $newlastname, $newfirstname, $newmidname, $newcourse, $newyearlvl, $newemail, $newaddress, $target_file, $firstname);
            } else {
                $message = "Error uploading file.";
                $messageType = "danger";
            }
        } else {
            $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $messageType = "danger";
        }
    } else {
        // Update without changing the image
        $sql = "UPDATE students SET IDNO=?, Last_Name=?, First_Name=?, Mid_Name=?, Course=?, Year_lvl=?, Email=?, Address=? WHERE First_Name=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssssss", $newidno, $newlastname, $newfirstname, $newmidname, $newcourse, $newyearlvl, $newemail, $newaddress, $firstname);
    }

    if (isset($stmt) && $stmt->execute()) {
        $_SESSION['firstname'] = $newfirstname; // Update session with new firstname
        $_SESSION['First_Name'] = $newfirstname;
        $_SESSION['Last_Name'] = $newlastname;
        $_SESSION['Mid_Name'] = $newmidname;
        $_SESSION['Course'] = $newcourse;
        $_SESSION['Year_lvl'] = $newyearlvl;
        $_SESSION['IDNO'] = $newidno;
        $_SESSION['Email'] = $newemail;
        $_SESSION['Address'] = $newaddress;

        // Store the success message in the session
        $_SESSION['success_message'] = "Profile updated successfully!";
        
        // Redirect to refresh the page
        header("Location: edit_profile.php");
        exit();
    } else {
        $message = "Error updating profile: " . $conn->error;
        $messageType = "danger";
    }
}

// Display success message if available
if (isset($_SESSION['success_message'])) {
    $message = $_SESSION['success_message'];
    $messageType = "success";
    unset($_SESSION['success_message']); // Clear the message after displaying it
}

// Get current user data
$firstname = $_SESSION['firstname'];
$sql = "SELECT * FROM students WHERE First_Name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $firstname);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User not found!";
    exit();
}

// Update session variables
$_SESSION['lastname'] = $user['Last_Name'];
$_SESSION['course'] = $user['Course'];
$_SESSION['yearlvl'] = $user['Year_lvl'];

?>

<!-- Add this alert div at the top of the content area -->
<div id="alertMessage" class="fixed top-4 right-4 max-w-sm transition-opacity duration-300 opacity-0 z-50">
    <div class="bg-white rounded-lg shadow-lg p-4 flex items-center border-l-4 border-green-500">
        <div class="text-green-500 rounded-full bg-green-100 p-2 mr-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <div>
            <span id="alertText" class="text-gray-800 font-medium"></span>
        </div>
    </div>
</div>

<!-- Navigation -->
<nav class="bg-primary shadow-lg">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center">
        <span class="text-white text-xl font-bold py-4">Edit Profile</span>
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

<!-- Main Content -->
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6 slide-in-top">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Edit Profile</h2>
        <form id="profileForm" method="POST" enctype="multipart/form-data" class="space-y-6">
            <div class="form-group mb-3 text-center">
                <label for="profile_image">Profile Picture</label>
                <div class="profile-image-container">
                    <?php if (!empty($user['profile_image']) && file_exists($user['profile_image'])): ?>
                        <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" 
                             alt="Current Profile Picture" 
                             class="profile-preview mb-2" 
                             style="max-width: 150px; border-radius: 50%;">
                    <?php else: ?>
                        <img src="https://cdn-icons-png.flaticon.com/512/2815/2815428.png" 
                             alt="Default Profile Picture" 
                             class="profile-preview mb-2" 
                             style="max-width: 150px; border-radius: 50%;">
                    <?php endif; ?>
                    <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*" onchange="previewImage(this);">
                </div>
            </div>

            <div class="form-group mb-3">
                <label for="IDNO">ID Number</label>
                <input type="number" class="form-control" id="IDNO" name="IDNO"
       value="<?php echo isset($user['IDNO']) ? htmlspecialchars($user['IDNO']) : ''; ?>" 
       readonly>
            </div>

            <div class="form-group mb-3">
                <label for="First_Name">First Name</label>
                <input type="text" class="form-control" id="First_Name" name="First_Name"
       value="<?php echo isset($user['First_Name']) ? htmlspecialchars($user['First_Name']) : ''; ?>" required>
            </div>

            <div class="form-group mb-3">
                <label for="Mid_Name">Middle Name</label>
                <input type="text" class="form-control" id="Mid_Name" name="Mid_Name"
       value="<?php echo isset($user['Mid_Name']) ? htmlspecialchars($user['Mid_Name']) : ''; ?>" required>
            </div>

            <div class="form-group mb-3">
                <label for="Last_Name">Last Name</label>
                <input type="text" class="form-control" id="Last_Name" name="Last_Name"
       value="<?php echo isset($user['Last_Name']) ? htmlspecialchars($user['Last_Name']) : ''; ?>" required>
            </div>

            <div class="form-group mb-3">
                <label for="Email">Email</label>
                <input type="Email" class="form-control" id="Email" name="Email"
                    value="<?php echo isset($user['Email']) ? htmlspecialchars($user['Email']) : ''; ?>" required>
            </div>

            <div class="form-group mb-3">
                <label for="Address">Address</label>
                <textarea class="form-control" id="Address" name="Address" rows="3" required><?php echo isset($user['Address']) ? htmlspecialchars($user['Address']) : ''; ?></textarea>
            </div>

            <div class="form-group mb-3">
                <label for="Course">Course</label>
                <select class="form-control" id="Course" name="Course" required>
                    <option value="">Select Course</option>
                    <?php
                    $courses = [
                        'BSIT', 'BSCS', 'BSHM', 'BSBA', 
                        'College of Customs Administration', 
                        'College of Education', 
                        'College of Engineering',
                        'College of Arts and Sciences',
                        'College of Nursing'
                    ];
                    foreach ($courses as $course) {
                        $selected = ($user['Course'] == $course) ? 'selected' : '';
                        echo "<option value=\"$course\" $selected>$course</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group mb-3">
                <label for="Year_lvl">Year Level</label>
                <input type="number" class="form-control" id="Year_lvl" name="Year_lvl" min="1" max="4"
       value="<?php echo isset($user['Year_lvl']) ? htmlspecialchars($user['Year_lvl']) : ''; ?>" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Update Profile</button>
        </form>
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

// Update the form submission handler
document.getElementById('profileForm').addEventListener('submit', function(event) {
    event.preventDefault();
    const formData = new FormData(this);

    fetch('edit_profile.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        if (data.includes('Profile updated successfully')) {
            showAlert('Profile updated successfully!', 'success');
            // Reload the page after 2 seconds
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            showAlert('Error updating profile. Please try again.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error updating profile. Please try again.', 'error');
    });
});

// Add this new function to handle the alert
function showAlert(message, type = 'success') {
    const alert = document.getElementById('alertMessage');
    const alertText = document.getElementById('alertText');
    
    // Set the message
    alertText.textContent = message;
    
    // Update alert styling based on type
    if (type === 'error') {
        alert.querySelector('div').classList.replace('border-green-500', 'border-red-500');
        alert.querySelector('div:first-child').classList.replace('text-green-500', 'text-red-500');
        alert.querySelector('div:first-child').classList.replace('bg-green-100', 'bg-red-100');
    }
    
    // Show the alert
    alert.classList.remove('opacity-0');
    
    // Hide the alert after 3 seconds
    setTimeout(() => {
        alert.classList.add('opacity-0');
    }, 3000);
}

function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        
        reader.onload = function(e) {
            var preview = document.querySelector('.profile-preview');
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<style>
.profile-image-container {
    text-align: center;
    margin-bottom: 20px;
}

.profile-preview {
    display: block;
    margin: 0 auto 10px;
    border: 2px solid #ddd;
    padding: 3px;
    background: #fff;
    max-width: 150px;
    height: 150px;
    object-fit: cover;
    border-radius: 50%;
}

#profile_image {
    max-width: 300px;
    margin: 0 auto;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
