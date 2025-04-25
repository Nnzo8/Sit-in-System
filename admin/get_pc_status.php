<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "users");

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

$lab = isset($_GET['lab']) ? $_GET['lab'] : '';

if (empty($lab)) {
    http_response_code(400);
    echo json_encode(['error' => 'Lab parameter is required']);
    exit();
}

// Get active sit-ins for the specified lab
$sql = "SELECT 
    pc_number,
    IDNO as student_id,
    'in-use' as status,
    time_in
FROM direct_sitin 
WHERE lab_room = ? AND status = 'active'
UNION 
SELECT 
    pc_number,
    IDNO as student_id,
    'in-use' as status,
    time_in
FROM sit_in_records 
WHERE lab_room = ? AND status = 'active'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $lab, $lab);
$stmt->execute();
$result = $stmt->get_result();

$pc_status = [];
while ($row = $result->fetch_assoc()) {
    $pc_status[] = $row;
}

echo json_encode($pc_status);
$conn->close();
