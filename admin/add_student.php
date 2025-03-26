<?php
session_start();

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../login.php");
    exit();
}

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$database = "users";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $idno = $_POST['idno'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $course = $_POST['course'];
    $yearlevel = $_POST['yearlevel'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Securely hash the password

    // Prepare SQL statement to prevent SQL injection
    $sql = "INSERT INTO students (IDNO, First_Name, Last_Name, Course, Year_lvl, username, password) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $idno, $firstname, $lastname, $course, $yearlevel, $username, $password);

    // Execute the statement
    if ($stmt->execute()) {
        // Redirect back to students page with success message
        header("Location: students.php?add=success");
        exit();
    } else {
        // Redirect back with error message
        header("Location: students.php?add=error");
        exit();
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>
