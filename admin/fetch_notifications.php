<?php
session_start();
require_once '../includes/db_connection.php';

// Check if user is admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

try {
    // Fetch pending reservations with student details
    $sql = "SELECT 
        r.reservation_id, r.IDNO, r.purpose, r.lab, r.pc, 
        r.reservation_date, r.time_in,
        s.First_Name, s.Last_Name, s.Course, s.Year_lvl
    FROM reservation r
    JOIN students s ON r.IDNO = s.IDNO
    WHERE r.status = 'pending'
    ORDER BY r.reservation_date ASC, r.time_in ASC";

    $result = $conn->query($sql);

    if (!$result) {
        throw new Exception("Query failed: " . $conn->error);
    }

    $notifications = [];
    
    while ($row = $result->fetch_assoc()) {
        // Format time from integer (e.g., 1430) to readable time
        $time = str_pad($row['time_in'], 4, '0', STR_PAD_LEFT);
        $hour = substr($time, 0, 2);
        $minute = substr($time, 2, 2);
        $formatted_time = date('g:i A', strtotime("$hour:$minute"));

        $notifications[] = [
            'type' => 'reservation',
            'reservation_id' => $row['reservation_id'],
            'IDNO' => $row['IDNO'],
            'First_Name' => $row['First_Name'],
            'Last_Name' => $row['Last_Name'],
            'Course' => $row['Course'],
            'Year_lvl' => $row['Year_lvl'],
            'lab' => $row['lab'],
            'pc' => $row['pc'],
            'purpose' => $row['purpose'],
            'reservation_date' => date('M d, Y', strtotime($row['reservation_date'])),
            'formatted_time' => $formatted_time
        ];
    }

    echo json_encode($notifications);

} catch (Exception $e) {
    error_log("Error in fetch_notifications.php: " . $e->getMessage());
    echo json_encode([
        'error' => 'An error occurred while fetching notifications',
        'details' => $e->getMessage()
    ]);
}

$conn->close();
?>
