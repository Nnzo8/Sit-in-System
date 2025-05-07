<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$database = "users";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['reservation_id']) || !isset($data['status'])) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit();
}

$reservation_id = $data['reservation_id'];
$status = $data['status'];

$sql = "UPDATE reservation SET status = ?, is_notified = 0 WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $status, $reservation_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}

$conn->close();
?>
