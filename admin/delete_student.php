<?php
session_start();

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['idno'])) {
    $conn = new mysqli("localhost", "root", "", "users");
    
    $idno = $_GET['idno'];
    
    // Delete from student_session first (foreign key constraint)
    $sql1 = "DELETE FROM student_session WHERE id_number = ?";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("i", $idno);
    $stmt1->execute();
    
    // Delete from students table
    $sql2 = "DELETE FROM students WHERE IDNO = ?";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("i", $idno);
    
    if ($stmt2->execute()) {
        header("Location: students.php?delete=success");
    } else {
        header("Location: students.php?delete=error");
    }
    
    $conn->close();
}
?>
