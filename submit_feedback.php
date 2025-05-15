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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idno = $_POST['idno'];
    $lab = $_POST['lab'];
    $message = $_POST['message'];
    $date = $_POST['date'];    $sql = "INSERT INTO feedback (IDNO, lab, message, date) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $idno, $lab, $message, $date);

    if ($stmt->execute()) {
        header("Location: history.php?status=success");
    } else {
        header("Location: history.php?status=error");
    }
}

$conn->close();
?>