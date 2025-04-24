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
    <link rel="stylesheet" href="../css/admin-dark-mode.css">
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
                        
                        <!-- New Lab Dropdown -->
                        <div class="relative group">
                            <button class="nav-link text-white hover:text-gray-200 flex items-center">
                                Lab
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-800 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                <div class="py-1 rounded-md bg-white dark:bg-gray-800 shadow-xs">
                                    <a href="lab_resources.php" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Lab Resources</a>
                                    <a href="lab_schedule.php" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Set Lab Schedule</a>
                                    <a href="lab_points.php" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Lab Usage Points</a>
                                </div>
                            </div>
                        </div>

                        <a href="sit_in_records.php" class="nav-link text-white hover:text-gray-200">View Sit-in Records</a>
                        <a href="sit_in_reports.php" class="nav-link text-white hover:text-gray-200">Sit-in Reports</a>
                        <a href="feedback.php" class="nav-link text-white hover:text-gray-200">View Feedbacks</a>
                        <a href="../logout.php" class="nav-link text-white hover:text-gray-200">Logout</a>
                        <button id="darkModeToggle" class="p-2 rounded-lg text-white hover:text-gray-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 block dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </button>
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
                <h2 class="text-2xl font-bold text-center w-full">CCS Sit-in Reports</h2>
            </div>
            <div class="space-x-2 text-right">
                <button onclick="exportToCSV()" class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded">
                    <i class="fas fa-file-csv mr-2"></i>Export CSV
                </button>
                <button onclick="exportToExcel()" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded">
                    <i class="fas fa-file-excel mr-2"></i>Export Excel
                </button>
                <button onclick="exportToPDF()" class="bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded">
                    <i class="fas fa-file-pdf mr-2"></i>Export PDF
                </button>
                <button onclick="printTable()" class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded">
                    <i class="fas fa-print mr-2"></i>Print
                </button>
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
            
            // Add title and university name
            csv.push('"CCS Lab Sit-in Reports"');
            csv.push('"University of Cebu Main Campus"');
            csv.push(''); // Empty line for spacing
            
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
            const title = '<tr><td colspan="9" style="text-align:center;font-size:16px;font-weight:bold;">CCS Lab Sit-in Reports</td></tr>';
            const university = '<tr><td colspan="9" style="text-align:center;font-size:14px;">University of Cebu Main Campus</td></tr>';
            const spacing = '<tr><td colspan="9"></td></tr>';
            
            const table = document.getElementById('sit-in-table');
            const html = title + university + spacing + table.outerHTML;
            const url = 'data:application/vnd.ms-excel;charset=utf-8,' + encodeURIComponent(html);
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', 'sit_in_reports.xls');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function exportToPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            // Add UC logo
            const ucImg = new Image();
            ucImg.src = '../imgs/uc.png';
            
            // Add CCS logo
            const ccsImg = new Image();
            ccsImg.src = '../imgs/ccs.png';

            // Wait for both images to load
            Promise.all([
                new Promise(resolve => {
                    ucImg.onload = resolve;
                    ucImg.onerror = resolve;
                }),
                new Promise(resolve => {
                    ccsImg.onload = resolve;
                    ccsImg.onerror = resolve;
                })
            ]).then(() => {
                // Add logos
                doc.addImage(ucImg, 'PNG', 20, 10, 25, 25); // Left side
                doc.addImage(ccsImg, 'PNG', 165, 10, 25, 25); // Right side

                // Add title and university name (adjusted Y positions to accommodate logos)
                doc.setFontSize(16);
                doc.text('CCS Lab Sit-in Reports', doc.internal.pageSize.width/2, 25, { align: 'center' });
                doc.setFontSize(12);
                doc.text('University of Cebu Main Campus', doc.internal.pageSize.width/2, 32, { align: 'center' });

                // Get table data
                const table = document.getElementById('sit-in-table');
                const rows = Array.from(table.querySelectorAll('tr'));
                const headers = Array.from(rows[0].querySelectorAll('th')).map(header => header.textContent);
                const data = rows.slice(1).map(row => {
                    return Array.from(row.querySelectorAll('td')).map(cell => cell.textContent);
                });

                // Create table (adjusted startY to accommodate logos and headers)
                doc.autoTable({
                    head: [headers],
                    body: data,
                    startY: 40,
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
                        0: { cellWidth: 15 },
                        1: { cellWidth: 20 },
                        2: { cellWidth: 30 },
                        3: { cellWidth: 25 },
                        4: { cellWidth: 20 },
                        5: { cellWidth: 20 },
                        6: { cellWidth: 20 },
                        7: { cellWidth: 25 },
                        8: { cellWidth: 20 }
                    },
                    margin: { top: 40 }
                });

                doc.save('sit_in_reports.pdf');
            });
        }

        function printTable() {
            const table = document.getElementById('sit-in-table').outerHTML;
            const newWindow = window.open('', '_blank');
            newWindow.document.write(`
                <html>
                    <head>
                        <title>CCS Lab Sit-in Reports</title>
                        <style>
                            table { width: 100%; border-collapse: collapse; }
                            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                            th { background-color: #f4f4f4; }
                            h1, h2 { text-align: center; margin-bottom: 10px; }
                            .subtitle { font-size: 18px; margin-bottom: 20px; }
                            .header-container {
                                display: flex;
                                justify-content: space-between;
                                align-items: center;
                                margin-bottom: 20px;
                                padding: 0 20px;
                            }
                            .logo { width: 80px; height: auto; }
                            .title-container {
                                text-align: center;
                                flex-grow: 1;
                                padding: 0 20px;
                            }
                        </style>
                    </head>
                    <body>
                        <div class="header-container">
                            <img src="../imgs/uc.png" class="logo" />
                            <div class="title-container">
                                <h1>CCS Lab Sit-in Reports</h1>
                                <h2 class="subtitle">University of Cebu Main Campus</h2>
                            </div>
                            <img src="../imgs/ccs.png" class="logo" />
                        </div>
                        ${table}
                    </body>
                </html>
            `);
            newWindow.document.close();
            newWindow.print();
        }

         // Dark mode toggle functionality
 document.addEventListener('DOMContentLoaded', function() {
            const darkModeToggle = document.getElementById('darkModeToggle');
            const html = document.documentElement;
            
            // Check for saved dark mode preference
            const darkMode = localStorage.getItem('adminDarkMode');
            if (darkMode === 'enabled') {
                html.classList.add('dark');
            }
            
            // Toggle dark mode
            darkModeToggle.addEventListener('click', function() {
                html.classList.toggle('dark');
                
                // Save preference
                if (html.classList.contains('dark')) {
                    localStorage.setItem('adminDarkMode', 'enabled');
                } else {
                    localStorage.setItem('adminDarkMode', null);
                }
            });
        });
    </script>
    <style>
    .group:hover .group-hover\:opacity-100 {
        opacity: 1;
    }
    .group:hover .group-hover\:visible {
        visibility: visible;
    }
    .nav-link {
        position: relative;
        padding: 0.5rem;
    }
    .nav-link:after {
        content: '';
        position: absolute;
        width: 0;
        height: 2px;
        bottom: 0;
        left: 0;
        background-color: white;
        transition: width 0.3s ease;
    }
    .nav-link:hover:after {
        width: 100%;
    }
</style>
</body>
</html>