<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    die(json_encode(['status' => 'error', 'message' => 'Unauthorized access']));
}

$conn = new mysqli("localhost", "root", "", "users");
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $record_id = (int)$_POST['record_id'];
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        if ($action === 'approve') {
            // Get the reservation details
            $select_sql = "SELECT * FROM reservation WHERE reservation_id = ?";
            $stmt = $conn->prepare($select_sql);
            $stmt->bind_param('i', $record_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $reservation = $result->fetch_assoc();
            
            if ($reservation) {
                // Convert time_in from HHMM to HH:MM format
                $time_in = substr($reservation['time_in'], 0, 2) . ':' . substr($reservation['time_in'], 2, 2) . ':00';
                
                // Insert into sit_in_records with current timestamp
                $insert_sql = "INSERT INTO sit_in_records (IDNO, lab_room, time_in, purpose, status, pc_number) 
                              VALUES (?, ?, NOW(), ?, 'active', ?)";
                $stmt = $conn->prepare($insert_sql);
                $stmt->bind_param('ssss', 
                    $reservation['IDNO'],
                    $reservation['lab'],
                    $reservation['purpose'],
                    $reservation['pc']
                );
                $stmt->execute();
                
                // Delete from reservation table
                $delete_sql = "DELETE FROM reservation WHERE reservation_id = ?";
                $stmt = $conn->prepare($delete_sql);
                $stmt->bind_param('i', $record_id);
                $stmt->execute();
            }
        } else if ($action === 'decline') {
            // Simply delete the reservation
            $sql = "DELETE FROM reservation WHERE reservation_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $record_id);
            $stmt->execute();
        }
        
        // Commit transaction
        $conn->commit();
        echo json_encode(['status' => 'success']);
        
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

$conn->close();
?>
