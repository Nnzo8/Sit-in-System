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

// Fetch recent approved/declined reservations that haven't been read
$sql = "SELECT 
            r.reservation_id,
            r.status,
            r.time_in,
            r.pc,
            r.lab,
            r.reservation_date,
            r.purpose,
            r.notification_read,
            CURRENT_TIMESTAMP as created_at
        FROM reservation r 
        WHERE r.IDNO = ? 
        AND r.status IN ('approved', 'declined')
        AND (r.notification_read = 0 OR r.notification_read IS NULL)
        ORDER BY r.reservation_date DESC, r.time_in DESC 
        LIMIT 10";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $time_in = sprintf("%04d", $row['time_in']);
    $hour = substr($time_in, 0, 2);
    $minute = substr($time_in, 2, 2);
    $formatted_time = $hour . ':' . $minute;
    
    $status_text = $row['status'] === 'approved' ? 'has been APPROVED' : 'has been DECLINED';
    $extra_text = $row['status'] === 'approved' 
        ? "You may now proceed to the laboratory. Purpose: {$row['purpose']}" 
        : "Please make another reservation.";
    
    $message = "Your sit-in reservation for {$row['lab']} (PC {$row['pc']}) on " . 
               date('F j, Y', strtotime($row['reservation_date'])) . 
               " at " . date('g:i A', strtotime($formatted_time)) . 
               " {$status_text}. {$extra_text}";
    
    $notifications[] = [
        'id' => $row['reservation_id'],
        'message' => $message,
        'time' => date('M j, Y g:i A', strtotime($row['created_at'])),
        'status' => $row['status']
    ];
}

// Mark notifications as read
if (!empty($notifications)) {
    $notification_ids = array_column($notifications, 'id');
    $ids_string = implode(',', $notification_ids);
    $update_sql = "UPDATE reservation SET notification_read = 1 WHERE reservation_id IN ($ids_string)";
    $conn->query($update_sql);
}

header('Content-Type: application/json');
echo json_encode(['notifications' => $notifications]);
$conn->close();
