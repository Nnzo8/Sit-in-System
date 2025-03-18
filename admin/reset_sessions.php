<?php
session_start();

// Check if user is admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "users");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Update all sessions to 30
$sql = "UPDATE student_session SET remaining_sessions = 30";

if ($conn->query($sql)) {
    header("Location: students.php?reset=success");
} else {
    header("Location: students.php?reset=error");
}

$conn->close();
?>
