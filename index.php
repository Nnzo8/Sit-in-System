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

if (isset($_SESSION['message']) && isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    echo "<div class='alert alert-success'>" . $_SESSION['message'] . " Welcome $username!</div>";
    unset($_SESSION['message']);
    unset($_SESSION['username']);
}
?>
   <div class="container-nav" style="background-color: #fff; padding: 15px; box-shadow: rgba(0, 0, 0, 0.1) 0px 4px 12px; border-radius: 8px; margin-bottom: 10px;">
    <a href="index.php">Home</a>
    <a href="about.php">About</a>
    <a href="login.php">Logout</a>
</div>
    <header>
        <h1>Welcome to CCS Sit-in Monitoring System</h1>
    </header>
</body>
</html>