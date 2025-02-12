<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSS Sit-in Monitoring System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
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

    $sql = "SELECT Password, First_Name, Last_Name, Course, Year_lvl FROM students WHERE Username='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['Password'])) {
            $_SESSION['username'] = $username;
            $_SESSION['username'] = $row['First_Name'];
            $_SESSION['firstname'] = $row['First_Name'];
            $_SESSION['lastname'] = $row['Last_Name'];
            $_SESSION['course'] = $row['Course'];
            $_SESSION['yearlvl'] = $row['Year_lvl'];
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
        <div class="logo-container">
        <img src="imgs/uc.png" alt="Logo" class="logo uclogo">
        <img src="imgs/ccs.png" alt="Logo" class="logo">
        </div>
        <header class="">CCS Sit-in Monitoring System</header>
            <form action="login.php" method="post">
            <div class="form-group">
            <input type="username" class="form-control" name="username" placeholder="Username " required>
            </div>
            <div class="form-group">
            <input type="password" class="form-control" name="password" placeholder="Password " required>
            </div>
            <div class="btns">
            <div class="form-btn">
            <input type="submit" class="btn btn-primary" value="Login" name="submit">
            </div>
            <div class="text">
            <a href="registration.php" class="register-link">Register</a>
            </div>
            </div>
        </form>
    </div>
</body>
</html>