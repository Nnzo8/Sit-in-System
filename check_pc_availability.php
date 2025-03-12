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
    $time_in = $date . ' ' . $time . ':00';
    
    $pcs = getAvailablePCs($conn, $lab_room, $time_in);
    
    header('Content-Type: application/json');
    echo json_encode($pcs);
}
