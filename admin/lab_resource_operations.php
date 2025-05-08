<?php
session_start();

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    exit(json_encode(['success' => false, 'message' => 'Unauthorized']));
}

$conn = new mysqli("localhost", "root", "", "users");

if ($conn->connect_error) {
    exit(json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]));
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $image_path = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $target_dir = "../uploads/resources/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
            $file_name = uniqid() . '.' . $file_extension;
            $target_file = $target_dir . $file_name;
            
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_path = 'uploads/resources/' . $file_name;
            }
        }

        $sql = "INSERT INTO lab_resources (resource_name, resource_code, website_url, image_path) 
                VALUES (?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", 
            $_POST['resource_name'],
            $_POST['resource_code'],
            $_POST['website_url'],
            $image_path
        );

        $result = $stmt->execute();
        echo json_encode(['success' => $result]);
        
        $stmt->close();
    } else if ($_POST['action'] === 'edit') {
        $image_path = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $target_dir = "../uploads/resources/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
            $file_name = uniqid() . '.' . $file_extension;
            $target_file = $target_dir . $file_name;
            
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_path = 'uploads/resources/' . $file_name;
                
                // Delete old image if it exists
                $sql = "SELECT image_path FROM lab_resources WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $_POST['id']);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    if ($row['image_path'] && file_exists("../" . $row['image_path'])) {
                        unlink("../" . $row['image_path']);
                    }
                }
                $stmt->close();
            }
        }

        if ($image_path) {
            $sql = "UPDATE lab_resources SET resource_name=?, resource_code=?, website_url=?, image_path=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $_POST['resource_name'], $_POST['resource_code'], $_POST['website_url'], $image_path, $_POST['id']);
        } else {
            $sql = "UPDATE lab_resources SET resource_name=?, resource_code=?, website_url=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $_POST['resource_name'], $_POST['resource_code'], $_POST['website_url'], $_POST['id']);
        }

        $result = $stmt->execute();
        echo json_encode(['success' => $result]);
        $stmt->close();
    } else if ($_POST['action'] === 'delete') {
        // Delete the image file first
        $sql = "SELECT image_path FROM lab_resources WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $_POST['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            if ($row['image_path'] && file_exists("../" . $row['image_path'])) {
                unlink("../" . $row['image_path']);
            }
        }
        $stmt->close();

        // Delete the database record
        $sql = "DELETE FROM lab_resources WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $_POST['id']);
        $result = $stmt->execute();
        echo json_encode(['success' => $result]);
        $stmt->close();
    } else if ($_POST['action'] === 'get') {
        $sql = "SELECT * FROM lab_resources WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $_POST['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        echo json_encode($result->fetch_assoc());
        $stmt->close();
    }
}

$conn->close();
?>
