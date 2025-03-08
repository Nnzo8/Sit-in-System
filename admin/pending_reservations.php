<?php
session_start();
require_once '../includes/sitin_functions.php';

// Check if user is admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "users");

// Handle approval/decline
if(isset($_POST['action']) && isset($_POST['record_id'])) {
    $record_id = $_POST['record_id'];
    $status = $_POST['action'] === 'approve' ? 'active' : 'declined';
    
    if(updateSitInStatus($conn, $record_id, $status)) {
        $message = $status === 'active' ? 'Reservation approved!' : 'Reservation declined!';
        $success = true;
    } else {
        $message = 'Error updating reservation.';
        $success = false;
    }
}

// Fetch pending reservations
$sql = "SELECT sir.*, s.First_Name, s.Last_Name, s.Course 
        FROM sit_in_records sir 
        JOIN students s ON sir.id_number = s.IDNO 
        WHERE sir.status = 'pending' 
        ORDER BY sir.time_in DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Reservations</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <!-- Add your navigation here -->
    
    <div class="max-w-7xl mx-auto py-6 px-4">
        <h2 class="text-2xl font-bold mb-4">Pending Reservations</h2>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lab Room</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date & Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Purpose</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td class="px-6 py-4">
                            <?= htmlspecialchars($row['First_Name'] . ' ' . $row['Last_Name']) ?><br>
                            <span class="text-sm text-gray-500"><?= htmlspecialchars($row['Course']) ?></span>
                        </td>
                        <td class="px-6 py-4"><?= htmlspecialchars($row['lab_room']) ?></td>
                        <td class="px-6 py-4">
                            <?= date('M d, Y g:i A', strtotime($row['time_in'])) ?>
                        </td>
                        <td class="px-6 py-4"><?= htmlspecialchars($row['purpose']) ?></td>
                        <td class="px-6 py-4">
                            <form method="POST" class="inline">
                                <input type="hidden" name="record_id" value="<?= $row['id'] ?>">
                                <button type="submit" name="action" value="approve" 
                                        class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">
                                    Approve
                                </button>
                                <button type="submit" name="action" value="decline" 
                                        class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 ml-2">
                                    Decline
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if(isset($message)): ?>
    <script>
        Swal.fire({
            title: '<?= $success ? 'Success!' : 'Error!' ?>',
            text: '<?= $message ?>',
            icon: '<?= $success ? 'success' : 'error' ?>',
            confirmButtonColor: '#000080'
        });
    </script>
    <?php endif; ?>
</body>
</html>
