<?php
session_start();
$conn = new mysqli("localhost", "root", "", "users");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Computer Status Operations
if ($_POST['action'] === 'addComputer') {
    $lab = $_POST['lab'];
    $total = $_POST['total'];
    $working = $_POST['working'];
    
    $sql = "INSERT INTO lab_computers (lab_number, total_units, working_units) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $lab, $total, $working);
    echo json_encode(['success' => $stmt->execute()]);
}

if ($_POST['action'] === 'updateComputer') {
    $id = $_POST['id'];
    $working = $_POST['working'];
    
    $sql = "UPDATE lab_computers SET working_units = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $working, $id);
    echo json_encode(['success' => $stmt->execute()]);
}

if ($_POST['action'] === 'deleteComputer') {
    $id = $_POST['id'];
    
    $sql = "DELETE FROM lab_computers WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    echo json_encode(['success' => $stmt->execute()]);
}

// Software Management Operations
if ($_POST['action'] === 'addSoftware') {
    $name = $_POST['name'];
    $status = $_POST['status'];
    
    $sql = "INSERT INTO lab_software (software_name, status) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $name, $status);
    echo json_encode(['success' => $stmt->execute()]);
}

if ($_POST['action'] === 'updateSoftware') {
    $id = $_POST['id'];
    $status = $_POST['status'];
    
    $sql = "UPDATE lab_software SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $id);
    echo json_encode(['success' => $stmt->execute()]);
}

if ($_POST['action'] === 'deleteSoftware') {
    $id = $_POST['id'];
    
    $sql = "DELETE FROM lab_software WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    echo json_encode(['success' => $stmt->execute()]);
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
