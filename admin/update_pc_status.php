<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['lab_room'], $data['pc_number'], $data['status'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$conn = new mysqli("localhost", "root", "", "users");

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$lab_room = $conn->real_escape_string($data['lab_room']);
$pc_number = (int)$data['pc_number'];
$status = $conn->real_escape_string($data['status']);
$reason = isset($data['reason']) ? $conn->real_escape_string($data['reason']) : '';
$is_disabled = ($status === 'disabled') ? 1 : 0;

$sql = "INSERT INTO pc_status (lab_room, pc_number, is_disabled, disabled_reason) 
        VALUES (?, ?, ?, ?) 
        ON DUPLICATE KEY UPDATE 
        is_disabled = VALUES(is_disabled), 
        disabled_reason = VALUES(disabled_reason)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("siis", $lab_room, $pc_number, $is_disabled, $reason);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}

$conn->close();
?>