<?php
session_start();
$conn = new mysqli("localhost", "root", "", "users");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Course Operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'addCourse') {
        $name = $_POST['name'];
        $code = $_POST['code'];
        $lab = $_POST['lab'];
        $schedule = $_POST['schedule'];
        $instructor = $_POST['instructor'];
        
        $sql = "INSERT INTO courses (course_name, course_code, lab, schedule, instructor) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $name, $code, $lab, $schedule, $instructor);
        
        $response = ['success' => $stmt->execute()];
        if (!$response['success']) {
            $response['error'] = $stmt->error;
        }
        
        echo json_encode($response);
    }
}

$conn->close();
?>
