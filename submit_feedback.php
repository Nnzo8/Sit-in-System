<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$database = "users";

$conn = new mysqli($servername, $username, $password, $database);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idno = $_POST['idno'];
    $lab = $_POST['lab'];
    $date = $_POST['date'];
    $message = $_POST['message'];
    
    $sql = "INSERT INTO feedback (IDNO, lab, date, message) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $idno, $lab, $date, $message);
    
    if ($stmt->execute()) {
        header("Location: history.php?status=success");
    } else {
        header("Location: history.php?status=error");
    }
} else {
    header("Location: history.php");
}

$conn->close();
?>