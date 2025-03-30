<?php
session_start();
include '../header.php';

// Check if user is admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../login.php");
    exit();
}

// Add database connection
$conn = mysqli_connect("localhost", "root", "", "users");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Query to combine records from both tables
$query = "SELECT 
    'direct' as source,
    d.id,
    d.IDNO as student_id,
    CONCAT(s1.First_Name, ' ', s1.Last_Name) as name,
    s1.Course as course,
    DATE(d.time_in) as date,
    TIME(d.time_in) as time_in,
    TIME(d.time_out) as time_out,
    d.purpose as reason,
    d.status
FROM direct_sitin d
LEFT JOIN students s1 ON d.IDNO = s1.IDNO
UNION ALL
SELECT 
    'records' as source,
    r.id,
    r.IDNO as student_id,
    CONCAT(s2.First_Name, ' ', s2.Last_Name) as name,
    s2.Course as course,
    DATE(r.time_in) as date,
    TIME(r.time_in) as time_in,
    TIME(r.time_out) as time_out,
    r.purpose as reason,
    r.status
FROM sit_in_records r
LEFT JOIN students s2 ON r.IDNO = s2.IDNO
ORDER BY date DESC, time_in DESC";

$result = mysqli_query($conn, $query);
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
    <!-- Add these lines after the font-awesome import -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.29/jspdf.plugin.autotable.min.js"></script>
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

        function exportToCSV() {
            const table = document.getElementById('sit-in-table');
            let csv = [];
            const rows = table.querySelectorAll('tr');
            
            for (const row of rows) {
                const cols = row.querySelectorAll('td,th');
                const rowArray = Array.from(cols).map(col => '"' + (col.innerText || '').replace(/"/g, '""') + '"');
                csv.push(rowArray.join(','));
            }

            const csvContent = csv.join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.setAttribute('download', 'sit_in_reports.csv');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function exportToExcel() {
            const table = document.getElementById('sit-in-table');
            const html = table.outerHTML;
            const url = 'data:application/vnd.ms-excel;charset=utf-8,' + encodeURIComponent(html);
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', 'sit_in_reports.xls');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function exportToPDF() {
            // Get table data
            const table = document.getElementById('sit-in-table');
            const rows = Array.from(table.querySelectorAll('tr'));
            
            // Extract headers
            const headers = Array.from(rows[0].querySelectorAll('th')).map(header => header.textContent);
            
            // Extract data
            const data = rows.slice(1).map(row => {
                return Array.from(row.querySelectorAll('td')).map(cell => cell.textContent);
            });

            // Initialize jsPDF
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            // Add title
            doc.setFontSize(16);
            doc.text('Sit-in Reports', 14, 15);

            // Create table
            doc.autoTable({
                head: [headers],
                body: data,
                startY: 25,
                theme: 'grid',
                headStyles: {
                    fillColor: [0, 0, 128],
                    textColor: [255, 255, 255],
                    fontSize: 8
                },
                bodyStyles: {
                    fontSize: 8
                },
                columnStyles: {
                    0: { cellWidth: 15 }, // ID
                    1: { cellWidth: 20 }, // Student ID
                    2: { cellWidth: 30 }, // Name
                    3: { cellWidth: 25 }, // Course
                    4: { cellWidth: 20 }, // Date
                    5: { cellWidth: 20 }, // Time In
                    6: { cellWidth: 20 }, // Time Out
                    7: { cellWidth: 25 }, // Reason
                    8: { cellWidth: 20 }  // Status
                },
                margin: { top: 25 }
            });

            // Save the PDF
            doc.save('sit_in_reports.pdf');
        }
    </script>
</body>
</html>