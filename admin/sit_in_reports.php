<?php
session_start();
include '../header.php';

// Check if user is admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sit-in Reports</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#000080',
                        secondary: '#1e293b'
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<!-- Navigation -->
<nav class="bg-primary shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center">
                <span class="text-white text-xl font-bold py-4">Sit-in Reports</span>
                <div class="flex space-x-4">
                    <div class="hidden md:flex items-center space-x-4">
                        <a href="dashboard.php" class="nav-link text-white hover:text-gray-200">Dashboard</a>
                        <a href="search.php" class="nav-link text-white hover:text-gray-200">Search</a>
                        <a href="students.php" class="nav-link text-white hover:text-gray-200">Students</a>
                        <a href="sitin.php" class="nav-link text-white hover:text-gray-200">Sit-in</a>
                        <a href="sit_in_records.php" class="nav-link text-white hover:text-gray-200">View Sit-in Records</a>
                        <a href="sit_in_reports.php" class="nav-link text-white hover:text-gray-200">Sit-in Reports</a>
                        <a href="feedback.php" class="nav-link text-white hover:text-gray-200">View Feedbacks</a>
                        <a href="../logout.php" class="nav-link text-white hover:text-gray-200">Logout</a>
                    </div>
                </div>
                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button class="mobile-menu-button" onclick="toggleNav()">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <!-- Mobile menu -->
        <div class="hidden md:hidden" id="navbarNav">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="dashboard.php" class="block text-white py-2 px-4 hover:bg-blue-900">Dashboard</a>
                <a href="students.php" class="block text-white py-2 px-4 hover:bg-blue-900">Students</a>
                <a href="sit_in_records.php" class="block text-white py-2 px-4 hover:bg-blue-900">Records</a>
                <a href="announcements.php" class="block text-white py-2 px-4 hover:bg-blue-900">Announcements</a>
                <a href="../logout.php" class="block text-white py-2 px-4 hover:bg-blue-900">Logout</a>
            </div>
        </div>
    </nav>


    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Sit-in Reports</h2>
                <div class="space-x-2">
                    <button onclick="exportToCSV()" class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded">
                        <i class="fas fa-file-csv mr-2"></i>Export CSV
                    </button>
                    <button onclick="exportToExcel()" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded">
                        <i class="fas fa-file-excel mr-2"></i>Export Excel
                    </button>
                    <button onclick="exportToPDF()" class="bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded">
                        <i class="fas fa-file-pdf mr-2"></i>Export PDF
                    </button>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <table id="sit-in-table" class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3">ID</th>
                                <th scope="col" class="px-6 py-3">Student ID</th>
                                <th scope="col" class="px-6 py-3">Name</th>
                                <th scope="col" class="px-6 py-3">Course</th>
                                <th scope="col" class="px-6 py-3">Date</th>
                                <th scope="col" class="px-6 py-3">Time In</th>
                                <th scope="col" class="px-6 py-3">Time Out</th>
                                <th scope="col" class="px-6 py-3">Reason</th>
                                <th scope="col" class="px-6 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($row['id']); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($row['student_id']); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($row['course']); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($row['date']); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($row['time_in']); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($row['time_out']); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($row['reason']); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($row['status']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="p-6 text-center text-gray-500">
                        <i class="fas fa-clipboard-list text-blue-500 mb-2 text-2xl"></i>
                        <p>No sit-in records found.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Mobile navigation toggle function
        function toggleNav() {
            const navbarNav = document.getElementById('navbarNav');
            navbarNav.classList.toggle('hidden');
        }
    </script>
</body>
</html>