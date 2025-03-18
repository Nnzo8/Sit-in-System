<?php
session_start();

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("localhost", "root", "", "users");
    
    $idno = $_POST['idno'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $course = $_POST['course'];
    $yearlevel = $_POST['yearlevel'];
    
    // Insert into students table
    $sql = "INSERT INTO students (IDNO, First_Name, Last_Name, Course, Year_lvl) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssi", $idno, $firstname, $lastname, $course, $yearlevel);
    
    if ($stmt->execute()) {
        // Also add entry in student_session table
        $sql2 = "INSERT INTO student_session (id_number, remaining_sessions) VALUES (?, 30)";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("i", $idno);
        $stmt2->execute();
        
        header("Location: students.php?add=success");
    } else {
        header("Location: students.php?add=error");
    }
    
    $conn->close();
}
?>
