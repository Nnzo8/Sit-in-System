<?php 
session_start();
include 'header.php';
?>

<div class="min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
        <div class="flex justify-center space-x-4 mb-6">
            <img src="imgs/uc.png" alt="UC Logo" class="h-20">
            <img src="imgs/ccs.png" alt="CCS Logo" class="h-20">
        </div>
        
        <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">
            CCS Sit-in Monitoring System
        </h1>

        <?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "users";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT Password, First_Name, Last_Name, Course, Year_lvl, IDNO, Email, Address FROM students WHERE Username='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['Password'])) {
            $_SESSION['username'] = $username;
            $_SESSION['firstname'] = $row['First_Name'];
            $_SESSION['lastname'] = $row['Last_Name'];
            $_SESSION['course'] = $row['Course'];
            $_SESSION['yearlvl'] = $row['Year_lvl'];
            $_SESSION['IDNO'] = $row['IDNO'];
            $_SESSION['Email'] = $row['Email'];
            $_SESSION['Address'] = $row['Address'];
            header("Location: index.php");
            exit();
        } else {
            echo "<div class='alert alert-danger'>Invalid password</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Invalid username or password</div>";
    }
}

$conn->close();
?>

        <form action="login.php" method="post" class="space-y-4">
            <div>
                <input type="text" name="username" placeholder="Username" 
                    class="form-control" required>
            </div>
            <div>
                <input type="password" name="password" placeholder="Password" 
                    class="form-control" required>
            </div>
            <div class="flex flex-col items-center space-y-4">
                <button type="submit" name="submit" 
                    class="btn-primary w-full">Login</button>
                <a href="registration.php" 
                    class="text-primary hover:text-blue-700">Register</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>