<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
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

if (isset($_SESSION['message']) && isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    echo "<div class='alert alert-success'>" . $_SESSION['message'] . " Welcome $username!</div>";
    unset($_SESSION['message']);
    unset($_SESSION['username']);
}
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light" style="box-shadow: rgba(0, 0, 0, 0.1) 0px 4px 12px; border-radius: 8px; margin-bottom: 10px;">
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about.php">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="login.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<header>
    <h1>Welcome to CCS Sit-in Monitoring System</h1>
</header>
<div class="container" style="max-width: 400px; margin-left: 0;">
    <div class="row align-items-center" style="background-color: #f8f9fa; display: flex; padding: 15px; border-radius: 8px; box-shadow: rgba(0, 0, 0, 0.1) 0px 4px 12px;">
        <div class="col-md-4">
            <img src="C:\xampp\htdocs\SIT-IN\imgs\elgato.jpg" alt="User Image" class="img-fluid rounded-circle">
        </div>
        <div class="col-md-8">
            <h5>User Information</h5>
            <p>Name: <?php echo $username; ?></p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+6p6m5Y5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r5v5r