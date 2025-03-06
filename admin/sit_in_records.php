<?php
session_start();
include '../config/db.php';

$sql = "SELECT r.*, s.First_Name, s.Last_Name 
        FROM sit_in_records r 
        JOIN students s ON r.student_id = s.IDNO 
        ORDER BY r.time_in DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sit-in Records</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Include navigation -->
    
    <div class="max-w-7xl mx-auto py-6 px-4">
        <h1 class="text-3xl font-bold mb-6">Sit-in Records</h1>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lab Room</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">PC</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time In</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time Out</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="px-6 py-4">
                                <?= date('Y-m-d', strtotime($row['time_in'])) ?>
                            </td>
                            <td class="px-6 py-4">
                                <?= htmlspecialchars($row['Last_Name'] . ', ' . $row['First_Name']) ?>
                            </td>
                            <td class="px-6 py-4"><?= htmlspecialchars($row['lab_room']) ?></td>
                            <td class="px-6 py-4"><?= htmlspecialchars($row['pc_number']) ?></td>
                            <td class="px-6 py-4">
                                <?= date('H:i', strtotime($row['time_in'])) ?>
                            </td>
                            <td class="px-6 py-4">
                                <?= $row['time_out'] ? date('H:i', strtotime($row['time_out'])) : '-' ?>
                            </td>
                            <td class="px-6 py-4">
                                <span class="<?= $row['status'] === 'active' ? 'text-green-500' : 'text-gray-500' ?>">
                                    <?= ucfirst($row['status']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
