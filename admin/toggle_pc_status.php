<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

header('Content-Type: application/json');

// Database connection
$conn = new mysqli("localhost", "root", "", "users");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$lab_room = $data['lab_room'];
$pc_number = $data['pc_number'];
$should_disable = $data['should_disable'];
$reason = $data['reason'] ?? null;

// Check if record exists
$check_sql = "SELECT * FROM pc_status WHERE lab_room = ? AND pc_number = ?";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("si", $lab_room, $pc_number);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Update existing record
    $sql = "UPDATE pc_status SET is_disabled = ?, disabled_reason = ?, disabled_at = NOW() WHERE lab_room = ? AND pc_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issi", $should_disable, $reason, $lab_room, $pc_number);
} else {
    // Insert new record
    $sql = "INSERT INTO pc_status (lab_room, pc_number, is_disabled, disabled_reason) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siis", $lab_room, $pc_number, $should_disable, $reason);
}

$success = $stmt->execute();
echo json_encode(['success' => $success]);