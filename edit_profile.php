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
                    <a class="nav-link" href="history.php">History</a>
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
    
    // Get current user data to check existing profile image
    $currentUserQuery = "SELECT profile_image FROM students WHERE First_Name = ?";
    $stmt = $conn->prepare($currentUserQuery);
    $stmt->bind_param("s", $firstname);
    $stmt->execute();
    $currentUser = $stmt->get_result()->fetch_assoc();
    
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
            
            // Delete old profile image if it exists
            if (!empty($currentUser['profile_image']) && file_exists($currentUser['profile_image'])) {
                unlink($currentUser['profile_image']);
            }
            
            $new_filename = uniqid() . '.' . $filetype;
            $target_file = $target_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                // Update with new image
                $sql = "UPDATE students SET IDNO=?, Last_Name=?, First_Name=?, Mid_Name=?, Course=?, Year_lvl=?, profile_image=? WHERE First_Name=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("isssssss", $newidno, $newlastname, $newfirstname, $newmidname, $newcourse, $newyearlvl, $target_file, $firstname);
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
        $sql = "UPDATE students SET IDNO=?, Last_Name=?, First_Name=?, Mid_Name=?, Course=?, Year_lvl=? WHERE First_Name=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssss", $newidno, $newlastname, $newfirstname, $newmidname, $newcourse, $newyearlvl, $firstname);
    }

    if (isset($stmt) && $stmt->execute()) {
        $_SESSION['firstname'] = $newfirstname; // Update session with new firstname
        $_SESSION['First_Name'] = $newfirstname;
        $_SESSION['Last_Name'] = $newlastname;
        $_SESSION['Mid_Name'] = $newmidname;
        $_SESSION['Course'] = $newcourse;
        $_SESSION['Year_lvl'] = $newyearlvl;
        $_SESSION['IDNO'] = $newidno;
        $message = "Profile updated successfully!";
        $messageType = "success";
        
        // Redirect to refresh the page with new data
        header("Location: edit_profile.php");
        exit();
    } else {
        $message = "Error updating profile: " . $conn->error;
        $messageType = "danger";
    }
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

<div class="editcontainer">
    <h2 class="text-center mb-4">Edit Profile</h2>
    <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?>" role="alert">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
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

<script>
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
