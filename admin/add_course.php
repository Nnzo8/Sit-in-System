<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    die(json_encode(['success' => false, 'message' => 'Unauthorized access']));
}

// Database connection
include '../db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courseName = $_POST['courseName'];
    $courseCode = $_POST['courseCode'];
    $lab = $_POST['lab'];
    $schedule = $_POST['schedule'];
    $instructor = $_POST['instructor'];

    try {
        $sql = "INSERT INTO courses (course_name, course_code, lab, schedule, instructor) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $courseName, $courseCode, $lab, $schedule, $instructor);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
