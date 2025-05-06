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

// Combined query for active sessions and disabled PCs
$sql = "SELECT 
    COALESCE(s.pc_number, d.pc_number) as pc_number,
    CASE 
        WHEN d.is_disabled = 1 THEN 'disabled'
        WHEN s.IDNO IS NOT NULL THEN 'in-use'
        ELSE 'available'
    END as status,
    s.IDNO as student_id,
    s.time_in,
    d.is_disabled,
    d.disabled_reason
FROM (
    SELECT 1 as pc_number UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION 
    SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10 UNION 
    SELECT 11 UNION SELECT 12 UNION SELECT 13 UNION SELECT 14 UNION SELECT 15 UNION 
    SELECT 16 UNION SELECT 17 UNION SELECT 18 UNION SELECT 19 UNION SELECT 20 UNION 
    SELECT 21 UNION SELECT 22 UNION SELECT 23 UNION SELECT 24 UNION SELECT 25 UNION 
    SELECT 26 UNION SELECT 27 UNION SELECT 28 UNION SELECT 29 UNION SELECT 30
) numbers
LEFT JOIN (
    SELECT pc_number, IDNO, time_in
    FROM direct_sitin 
    WHERE lab_room = ? AND status = 'active'
    UNION 
    SELECT pc_number, IDNO, time_in
    FROM sit_in_records 
    WHERE lab_room = ? AND status = 'active'
) s ON s.pc_number = numbers.pc_number
LEFT JOIN pc_status d ON d.pc_number = numbers.pc_number AND d.lab_room = ?
ORDER BY numbers.pc_number";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $lab, $lab, $lab);
$stmt->execute();
$result = $stmt->get_result();

$pc_status = [];
while ($row = $result->fetch_assoc()) {
    $pc_status[] = $row;
}

echo json_encode($pc_status);
$conn->close();