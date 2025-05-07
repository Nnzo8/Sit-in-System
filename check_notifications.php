<?php
session_start();

if (!isset($_SESSION['IDNO'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$database = "users";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$student_id = $_SESSION['IDNO'];

// Get recent reservation updates
$sql = "SELECT 
            id,
            status,
            reservation_date,
            time_in,
            time_out,
            pc,
            lab,
            created_at,
            is_notified
        FROM reservation 
        WHERE IDNO = ? 
        AND status IN ('approved', 'declined')
        AND is_notified = 0
        ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $status = ucfirst($row['status']);
    $message = "Your reservation for {$row['lab']} (PC {$row['pc']}) on " . 
               date('F j, Y', strtotime($row['reservation_date'])) . 
               " at " . date('g:i A', strtotime($row['time_in'])) . 
               " has been {$row['status']}.";
               
    $notifications[] = [
        'id' => $row['id'],
        'message' => $message,
        'time' => date('M j, Y g:i A', strtotime($row['created_at'])),
        'status' => $row['status']
    ];
}

// Mark notifications as read
if (!empty($notifications)) {
    $update_sql = "UPDATE reservation 
                   SET is_notified = 1 
                   WHERE IDNO = ? 
                   AND status IN ('approved', 'declined')
                   AND is_notified = 0";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
}

echo json_encode(['notifications' => $notifications]);
$conn->close();
