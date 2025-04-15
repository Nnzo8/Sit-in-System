<?php
session_start();
include '../db_connection.php';

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    exit(json_encode(['success' => false, 'message' => 'Unauthorized']));
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'add':
            try {
                $sql = "INSERT INTO courses (course_name, course_code, lab, schedule, instructor) 
                        VALUES (:course_name, :course_code, :lab, :schedule, :instructor)";
                $stmt = $conn->prepare($sql);
                
                $result = $stmt->execute([
                    ':course_name' => $_POST['courseName'],
                    ':course_code' => $_POST['courseCode'],
                    ':lab' => $_POST['lab'],
                    ':schedule' => $_POST['schedule'],
                    ':instructor' => $_POST['instructor']
                ]);

                echo json_encode(['success' => $result]);
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            break;

        case 'edit':
            try {
                $sql = "UPDATE courses SET 
                        course_name = :course_name,
                        course_code = :course_code,
                        lab = :lab,
                        schedule = :schedule,
                        instructor = :instructor
                        WHERE id = :id";
                $stmt = $conn->prepare($sql);
                
                $result = $stmt->execute([
                    ':course_name' => $_POST['courseName'],
                    ':course_code' => $_POST['courseCode'],
                    ':lab' => $_POST['lab'],
                    ':schedule' => $_POST['schedule'],
                    ':instructor' => $_POST['instructor'],
                    ':id' => $_POST['id']
                ]);

                echo json_encode(['success' => $result]);
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            break;

        case 'delete':
            $id = $_POST['id'];
            $sql = "DELETE FROM courses WHERE id=?";
            $stmt = $conn->prepare($sql);
            $result = $stmt->execute([$id]);
            
            echo json_encode(['success' => $result]);
            break;

        case 'get':
            $id = $_POST['id'];
            $sql = "SELECT * FROM courses WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$id]);
            $course = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo json_encode($course);
            break;
    }
} else {
    // GET request - fetch all courses
    try {
        $stmt = $conn->query("SELECT * FROM courses ORDER BY created_at DESC");
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($courses);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
