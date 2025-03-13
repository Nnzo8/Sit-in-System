<?php
function createSitInRecord($conn, $student_id, $lab_room, $pc_number, $purpose, $time_in) {
    try {
        $sql = "INSERT INTO sit_in_records (IDNO, lab_room, pc_number, purpose, time_in, status) 
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

function getActiveSitIn($conn, $student_id, $new_time_in = null) {
    // If no new time is provided, only check for currently active sessions
    if ($new_time_in === null) {
        $sql = "SELECT * FROM sit_in_records 
                WHERE IDNO = ? 
                AND status = 'active' 
                AND (time_out IS NULL OR time_out = '0000-00-00 00:00:00')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $student_id);
    } else {
        // Check for overlapping reservations
        $sql = "SELECT * FROM sit_in_records 
                WHERE IDNO = ? 
                AND (status = 'active' OR status = 'pending')
                AND time_in = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $student_id, $new_time_in);
    }
    
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getSitInHistory($conn, $student_id, $limit = 10) {
    try {
        $sql = "SELECT * FROM sit_in_records 
                WHERE IDNO = ? 
                ORDER BY time_in DESC 
                LIMIT ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $student_id, $limit);
        $stmt->execute();
        return $stmt->get_result();
    } catch (Exception $e) {
        error_log("Error getting sit-in history: " . $e->getMessage());
        return false;
    }
}

function getAvailablePCs($conn, $lab_room, $time_in) {
    // Total PCs per lab (you can adjust these numbers)
    $pc_counts = [
        'Lab 524' => 30,
        'Lab 526' => 30,
        'Lab 528' => 30,
        'Lab 530' => 30,
        'Lab 542' => 30
    ];

    // Get all occupied PCs for the given time
    $sql = "SELECT pc_number FROM sit_in_records 
            WHERE lab_room = ? 
            AND time_in <= ? 
            AND (time_out >= ? OR time_out IS NULL OR time_out = '0000-00-00 00:00:00')
            AND status IN ('active', 'pending')";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $lab_room, $time_in, $time_in);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $occupied_pcs = [];
    while($row = $result->fetch_assoc()) {
        $occupied_pcs[] = $row['pc_number'];
    }

    // Generate array of all PCs with availability status
    $all_pcs = [];
    $total_pcs = $pc_counts[$lab_room] ?? 30;
    
    for($i = 1; $i <= $total_pcs; $i++) {
        $all_pcs[] = [
            'number' => $i,
            'available' => !in_array($i, $occupied_pcs)
        ];
    }
    
    return $all_pcs;
}

function deleteSitInRecord($conn, $record_id) {
    $sql = "DELETE FROM sit_in_records WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $record_id);
    return $stmt->execute();
}