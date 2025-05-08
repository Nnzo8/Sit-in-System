<?php
session_start();
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$database = "users";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Connection failed']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $record_id = $_POST['record_id'];
    $student_id = $_POST['student_id'];

    // Add this new condition in your action handling
    if ($_POST['action'] === 'mark_all_read') {
        try {
            $sql = "UPDATE reservation SET status = 'read' WHERE status = 'pending'";
            if ($conn->query($sql)) {
                echo json_encode(['status' => 'success']);
            } else {
                throw new Exception("Error updating records");
            }
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
        exit();
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Update reservation status
        $status = $action === 'approve' ? 'approved' : 'declined';
        $update_sql = "UPDATE reservation SET status = ? WHERE reservation_id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param('si', $status, $record_id);
        $stmt->execute();

        if ($action === 'approve') {
            // Get reservation details
            $fetch_sql = "SELECT * FROM reservation WHERE reservation_id = ?";
            $stmt = $conn->prepare($fetch_sql);
            $stmt->bind_param('i', $record_id);
            $stmt->execute();
            $reservation = $stmt->get_result()->fetch_assoc();

            // Insert into sit_in_records for approved reservations
            $insert_sql = "INSERT INTO sit_in_records (IDNO, lab_room, time_in, status, purpose, pc_number) 
                          VALUES (?, ?, NOW(), 'active', ?, ?)";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param('sssi', 
                $reservation['IDNO'],
                $reservation['lab'],
                $reservation['purpose'],
                $reservation['pc']
            );
            $stmt->execute();
        }

        $conn->commit();
        echo json_encode(['status' => 'success', 'message' => 'Reservation ' . $status . ' successfully']);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

$conn->close();
?>
