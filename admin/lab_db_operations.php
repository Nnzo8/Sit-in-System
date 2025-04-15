<?php
session_start();
$conn = new mysqli("localhost", "root", "", "users");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Course Operations
if ($_POST['action'] === 'addCourse') {
    $name = $_POST['name'];
    $code = $_POST['code'];
    $lab = $_POST['lab'];
    $schedule = $_POST['schedule'];
    $instructor = $_POST['instructor'];
    
    $sql = "INSERT INTO lab_courses (course_name, course_code, lab, schedule, instructor) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $name, $code, $lab, $schedule, $instructor);
    echo json_encode(['success' => $stmt->execute()]);
}

$conn->close();
?>
