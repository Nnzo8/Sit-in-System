<?php
function createSitInRecord($conn, $student_id, $lab_room, $pc_number, $purpose, $time_in) {
    try {
        $sql = "INSERT INTO sit_in_records (id_number, lab_room, pc_number, purpose, time_in, status) 
                VALUES (?, ?, ?, ?, ?, 'pending')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssiss", $student_id, $lab_room, $pc_number, $purpose, $time_in);
        return $stmt->execute();
    } catch (Exception $e) {
        error_log("Error creating sit-in record: " . $e->getMessage());
        return false;
    }
}

function updateSitInRecord($conn, $record_id, $time_out) {
    $sql = "UPDATE sit_in_records SET time_out = ?, status = 'completed' 
            WHERE id = ? AND status = 'active'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $time_out, $record_id);
    return $stmt->execute();
}

function updateSitInStatus($conn, $record_id, $status) {
    $sql = "UPDATE sit_in_records SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $record_id);
    return $stmt->execute();
}

function getActiveSitIn($conn, $student_id) {
    $sql = "SELECT * FROM sit_in_records 
            WHERE id_number = ? AND status = 'active'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getSitInHistory($conn, $student_id, $limit = 10) {
    $sql = "SELECT * FROM sit_in_records 
            WHERE IDNO = ? 
            ORDER BY created_at DESC LIMIT ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $student_id, $limit);
    $stmt->execute();
    return $stmt->get_result();
}
