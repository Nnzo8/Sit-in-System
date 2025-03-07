<?php
session_start();

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
    <style>
        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }
        
        /* Header styles */
        .header {
            background-color: #000080;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header-title {
            font-size: 18px;
            font-weight: bold;
        }
        
        .nav-links {
            display: flex;
        }
        
        .nav-links a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
        }
        
        .nav-links a:hover {
            text-decoration: underline;
        }
        
        /* Main content styles */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .page-title {
            text-align: center;
            font-size: 24px;
            margin-bottom: 30px;
            font-weight: bold;
        }
        
        /* Charts section */
        .charts-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 40px;
            margin-bottom: 40px;
        }
        
        .chart-wrapper {
            flex: 1;
            min-width: 300px;
            max-width: 500px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .chart-legend {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin-bottom: 10px;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            margin: 0 5px 5px 5px;
        }
        
        .legend-color {
            width: 15px;
            height: 15px;
            margin-right: 5px;
        }
        
        .empty-chart {
            width: 300px;
            height: 300px;
            border-radius: 50%;
            border: 4px solid #ddd;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }
        
        .no-data-message {
            color: #999;
            font-size: 14px;
        }
        
        .chart-scale {
            margin-top: 15px;
            font-size: 12px;
            color: #666;
            display: flex;
            justify-content: center;
            width: 100%;
        }
        
        .scale-number {
            padding: 0 4px;
        }
        
        /* Table controls */
        .table-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .entries-control select {
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .search-control input {
            padding: 5px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 200px;
        }
        
        /* Table styles */
        .records-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: white;
        }
        
        .records-table th, .records-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        
        .records-table th {
            background-color: #f2f2f2;
            cursor: pointer;
        }
        
        .records-table th:hover {
            background-color: #e6e6e6;
        }
        
        .empty-message {
            text-align: center;
            padding: 20px;
            color: #999;
        }
        
        /* Pagination */
        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .page-info {
            font-size: 14px;
            color: #666;
        }
        
        .pagination {
            display: flex;
        }
        
        .pagination a {
            padding: 5px 10px;
            border: 1px solid #ddd;
            text-decoration: none;
            color: #666;
            background-color: #f2f2f2;
        }
        
        .pagination a.active {
            background-color: #ddd;
            color: #333;
        }
        
        .pagination a:first-child {
            border-top-left-radius: 4px;
            border-bottom-left-radius: 4px;
        }
        
        .pagination a:last-child {
            border-top-right-radius: 4px;
            border-bottom-right-radius: 4px;
        }
        
        /* Media queries */
        @media (max-width: 768px) {
            .charts-container {
                flex-direction: column;
                align-items: center;
            }
            
            .table-controls {
                flex-direction: column;
                gap: 10px;
            }
            
            .pagination-container {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<!-- Navigation -->
<nav class="bg-primary shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center">
                <span class="text-white text-xl font-bold py-4">Sit-in</span>
                <div class="flex space-x-4">
                    <div class="hidden md:flex items-center space-x-4">
                        <a href="dashboard.php" class="nav-link text-white hover:text-gray-200">Dashboard</a>
                        <a href="search.php" class="nav-link text-white hover:text-gray-200">Search</a>
                        <a href="students.php" class="nav-link text-white hover:text-gray-200">Students</a>
                        <a href="sitin.php" class="nav-link text-white hover:text-gray-200">Sit-in</a>
                        <a href="sit_in_records.php" class="nav-link text-white hover:text-gray-200">View Sit-in Records</a>
                        <a href="sit_in_reports.php" class="nav-link text-white hover:text-gray-200">Sit-in Reports</a>
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

<!-- Main Content -->
<div class="container">
        <h1 class="page-title">Current Sit-in Records</h1>
        
        <!-- Charts Section -->
        <div class="charts-container">
            <!-- Programming Languages Chart -->
            <div class="chart-wrapper">
                <div class="chart-legend">
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #42a5f5;"></div>
                        <span>C#</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #f06292;"></div>
                        <span>C</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #ff9800;"></div>
                        <span>Java</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #ffca28;"></div>
                        <span>ASP.Net</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #26a69a;"></div>
                        <span>Php</span>
                    </div>
                </div>
                
                <div class="empty-chart">
                    <div class="no-data-message">No data available</div>
                </div>
                
                <div class="chart-scale">
                    <?php for ($i = 0; $i <= 1; $i += 0.1): ?>
                        <div class="scale-number"><?php echo number_format($i, 1); ?></div>
                    <?php endfor; ?>
                </div>
            </div>
            
            <!-- Labs Chart -->
            <div class="chart-wrapper">
                <div class="chart-legend">
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #f8bbd0;"></div>
                        <span>524</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #ffe0b2;"></div>
                        <span>526</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #fff9c4;"></div>
                        <span>528</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #b2dfdb;"></div>
                        <span>530</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #bbdefb;"></div>
                        <span>542</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #e1bee7;"></div>
                        <span>Mac</span>
                    </div>
                </div>
                
                <div class="empty-chart">
                    <div class="no-data-message">No data available</div>
                </div>
                
                <div class="chart-scale">
                    <?php for ($i = 0; $i <= 1; $i += 0.1): ?>
                        <div class="scale-number"><?php echo number_format($i, 1); ?></div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
        
        <!-- Table Controls -->
        <div class="table-controls">
            <div class="entries-control">
                <select>
                    <option>10</option>
                    <option>25</option>
                    <option>50</option>
                    <option>100</option>
                </select>
                <span>entries per page</span>
            </div>
            
            <div class="search-control">
                <label for="search">Search:</label>
                <input type="text" id="search">
            </div>
        </div>
        
        <!-- Records Table -->
        <table class="records-table">
            <thead>
                <tr>
                    <th>Sit-in Number</th>
                    <th>ID Number</th>
                    <th>Name</th>
                    <th>Purpose</th>
                    <th>Lab</th>
                    <th>Login</th>
                    <th>Logout</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="8" class="empty-message">No records available</td>
                </tr>
            </tbody>
        </table>
        
        <!-- Pagination -->
        <div class="pagination-container">
            <div class="page-info">Showing 0 to 0 of 0 entries</div>
            <div class="pagination">
                <a href="#">&laquo;</a>
                <a href="#">&lsaquo;</a>
                <a href="#" class="active">1</a>
                <a href="#">&rsaquo;</a>
                <a href="#">&raquo;</a>
            </div>
        </div>
    </div>
    
    <script>
        // Toggle mobile menu function
        function toggleNav() {
            const navbarNav = document.getElementById('navbarNav');
            if (navbarNav) {
                navbarNav.classList.toggle('hidden');
            }
        }
    </script>
</body>
</html>