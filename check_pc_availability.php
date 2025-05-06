<?php
require_once 'includes/sitin_functions.php';

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "users";

$conn = new mysqli($servername, $username, $password, $database);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lab_room = $_POST['lab_room'];
    $date = $_POST['date'];
    $time = $_POST['time_in'];
    
    // Convert time to integer format (HHMM)
    $time_int = (int)str_replace(':', '', $time);
    
    // Get all PCs (assuming 1-30 for each lab)
    $pcs = array();
    for ($i = 1; $i <= 30; $i++) {
        $pcs[] = array(
            'number' => $i,
            'available' => true
        );
    }

$query = "SELECT pc FROM reservation 
          WHERE lab = ? 
          AND reservation_date = ? 
          AND status != 'declined'
          AND time_in <= ? 
          AND time_out >= ?
          UNION
          SELECT pc_number 
          FROM pc_status 
          WHERE lab_room = ? 
          AND is_disabled = 1";

$stmt = $conn->prepare($query);
$stmt->bind_param("sssss", $lab_room, $date, $time_int, $time_int, $lab_room);
    // Check reservations
    $query = "SELECT pc FROM reservation 
              WHERE lab = ? 
              AND reservation_date = ? 
              AND status != 'declined'
              AND time_in <= ? 
              AND time_out >= ?";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssii", $lab_room, $date, $time_int, $time_int);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Mark reserved PCs as unavailable
    while ($row = $result->fetch_assoc()) {
        $pc_number = (int)$row['pc'] - 1; // Array is 0-based
        if (isset($pcs[$pc_number])) {
            $pcs[$pc_number]['available'] = false;
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode(array_values($pcs));
}
