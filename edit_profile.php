<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="homestyle.css">
</head>
<body>
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
                    <a class="nav-link" href="about.php">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="edit_profile.php">Edit</a>
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

<?php
session_start();
if (!isset($_SESSION['username'])) {
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
    $username = $_SESSION['username'];
    $newfirstname = $_POST['First_Name'];
    $newlastname = $_POST['Last_Name'];
    $newmidname = $_POST['Mid_Name'];
    $newcourse = $_POST['Course'];
    $newyearlvl = $_POST['Year_lvl'];
    $newidno = $_POST['IDNO'];
    
    // Handle image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['profile_image']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (in_array(strtolower($filetype), $allowed)) {
            $target_dir = "uploads/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $new_filename = uniqid() . '.' . $filetype;
            $target_file = $target_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                $sql = "UPDATE students SET IDNO=?, Last_Name=?, First_Name=?, Mid_Name=?, Course=?, Year_lvl=?, profile_image=? WHERE Username=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("isssssss", $newidno, $newlastname, $newfirstname, $newmidname, $newcourse, $newyearlvl, $target_file, $username);
            }
        } else {
            $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $messageType = "danger";
        }
    } else {
        // Update without changing the image
        $sql = "UPDATE students SET IDNO=?, Last_Name=?, First_Name=?, Mid_Name=?, Course=?, Year_lvl=? WHERE Username=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssss", $newidno, $newlastname, $newfirstname, $newmidname, $newcourse, $newyearlvl, $username);
    }

    if (isset($stmt) && $stmt->execute()) {
        $_SESSION['First_Name'] = $newfirstname;
        $_SESSION['Last_Name'] = $newlastname;
        $_SESSION['Mid_Name'] = $newmidname;
        $_SESSION['Course'] = $newcourse;
        $_SESSION['Year_lvl'] = $newyearlvl;
        $_SESSION['IDNO'] = $newidno;
        $message = "Profile updated successfully!";
        $messageType = "success";
    } else {
        $message = "Error updating profile.";
        $messageType = "danger";
    }
}

// Get current user data
$username = $_SESSION['username'];
$sql = "SELECT * FROM students WHERE Username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

?>

<div class="container">
    <h2 class="text-center mb-4">Edit Profile</h2>
    
    <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?>" role="alert">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group mb-3">
            <label for="profile_image">Profile Picture</label>
            <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*">
            <?php if (!empty($user['profile_image'])): ?>
                <img src="<?php echo $user['profile_image']; ?>" alt="Current Profile Picture" class="mt-2" style="max-width: 100px;">
            <?php endif; ?>
        </div>

        <div class="form-group mb-3">
            <label for="IDNO">ID Number</label>
            <input type="number" class="form-control" id="IDNO" name="IDNO"
       value="<?php echo isset($user['IDNO']) ? htmlspecialchars($user['IDNO']) : ''; ?>" required>
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
            <label for="Course">Course</label>
            <select class="form-control" id="Course" name="Course" required>
                <option value="">Select Course</option>
                <option value="BSIT" <?php echo (isset($user['Course']) && $user['Course'] == 'BSIT') ? 'selected' : ''; ?>>BSIT</option>
                <option value="BSCS" <?php echo (isset($user['Course']) && $user['Course'] == 'BSCS') ? 'selected' : ''; ?>>BSCS</option>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
