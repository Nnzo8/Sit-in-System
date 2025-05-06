<?php
session_start();
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "users");

if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed']));
}

$lab_room = $_POST['lab_room'] ?? '';
$date = $_POST['date'] ?? '';
$time_in = $_POST['time_in'] ?? '';

$datetime = $date . ' ' . $time_in;

// Get both active reservations and disabled PCs
$sql = "SELECT 
    number.pc as number,
    CASE 
        WHEN ps.is_disabled = 1 THEN false
        WHEN r.pc IS NOT NULL THEN false
        ELSE true
    END as available,
    ps.is_disabled,
    ps.disabled_reason
FROM 
    (SELECT 1 as pc UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION 
     SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10 UNION 
     SELECT 11 UNION SELECT 12 UNION SELECT 13 UNION SELECT 14 UNION SELECT 15 UNION 
     SELECT 16 UNION SELECT 17 UNION SELECT 18 UNION SELECT 19 UNION SELECT 20 UNION 
     SELECT 21 UNION SELECT 22 UNION SELECT 23 UNION SELECT 24 UNION SELECT 25 UNION 
     SELECT 26 UNION SELECT 27 UNION SELECT 28 UNION SELECT 29 UNION SELECT 30) number
LEFT JOIN (
    SELECT pc FROM reservation 
    WHERE lab = ? AND reservation_date = ? AND status = 'pending'
    UNION
    SELECT pc_number FROM sit_in_records 
    WHERE lab_room = ? AND DATE(time_in) = ? AND status = 'active'
) r ON number.pc = r.pc
LEFT JOIN pc_status ps ON number.pc = ps.pc_number AND ps.lab_room = ?
ORDER BY number.pc";

$stmt = $conn->prepare($sql);
$stmt->bind_param('sssss', $lab_room, $date, $lab_room, $date, $lab_room);
$stmt->execute();
$result = $stmt->get_result();

$pcs = [];
while ($row = $result->fetch_assoc()) {
    $pcs[] = $row;
}

echo json_encode($pcs);
