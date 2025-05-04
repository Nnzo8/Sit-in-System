<?php
session_start();
include 'db_connect.php';

header('Content-Type: application/json');

$sql = "SELECT * FROM courses ORDER BY course_name";
$result = $conn->query($sql);

$courses = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
}

echo json_encode($courses);
$conn->close();
?>
