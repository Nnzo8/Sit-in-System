<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

$conn = new mysqli("localhost", "root", "", "users");

if ($conn->connect_error) {
    echo json_encode(['error' => 'Connection failed']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'add' || $action === 'edit') {
        $resource_name = $_POST['resource_name'];
        $description = $_POST['description'];
        $website_url = $_POST['website_url'];
        $resource_id = isset($_POST['resource_id']) ? $_POST['resource_id'] : null;

        // Handle image upload
        $image_path = null;
        if (isset($_FILES['resource_image']) && $_FILES['resource_image']['error'] == 0) {
            $target_dir = "../uploads/resources/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $file_extension = strtolower(pathinfo($_FILES["resource_image"]["name"], PATHINFO_EXTENSION));
            $file_name = uniqid() . '.' . $file_extension;
            $target_file = $target_dir . $file_name;
            
            if (move_uploaded_file($_FILES["resource_image"]["tmp_name"], $target_file)) {
                $image_path = 'uploads/resources/' . $file_name;
            }
        }

        if ($action === 'add') {
            $sql = "INSERT INTO lab_resources (resource_name, description, website_url, image_path) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $resource_name, $description, $website_url, $image_path);
        } else {
            if ($image_path) {
                $sql = "UPDATE lab_resources SET resource_name=?, description=?, website_url=?, image_path=? WHERE id=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssi", $resource_name, $description, $website_url, $image_path, $resource_id);
            } else {
                $sql = "UPDATE lab_resources SET resource_name=?, description=?, website_url=? WHERE id=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssi", $resource_name, $description, $website_url, $resource_id);
            }
        }

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => $stmt->error]);
        }
    } elseif ($action === 'delete') {
        $resource_id = $_POST['resource_id'];
        
        $sql = "DELETE FROM lab_resources WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $resource_id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => $stmt->error]);
        }
    }
}

$conn->close();
?>
