<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$database = "users";

$conn = new mysqli($servername, $username, $password, $database);
$search = isset($_GET['search']) ? $_GET['search'] : '';

if ($search) {
    $sql = "SELECT * FROM students WHERE IDNO LIKE ? OR First_Name LIKE ? OR Last_Name LIKE ? ORDER BY Last_Name";
    $stmt = $conn->prepare($sql);
    $searchTerm = "%$search%";
    $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT * FROM students ORDER BY Last_Name";
    $result = $conn->query($sql);
}
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sit-in Records</title>
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
                <span class="text-white text-xl font-bold py-4">Students</span>
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
<body class="bg-gray-100">
  
    
    <div class="max-w-7xl mx-auto py-6 px-4">
        <div class="mb-6">
            <form method="GET" class="flex gap-2">
                <input type="text" name="search" placeholder="Search by ID or Name" 
                       value="<?= htmlspecialchars($search) ?>"
                       class="flex-1 border p-2 rounded">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                    Search
                </button>
            </form>
        </div>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <?php if (isset($_GET['reset']) && $_GET['reset'] === 'success'): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">All student sessions have been reset to 30.</span>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Student List</h2>
                <div class="space-x-2">
                    <form action="reset_sessions.php" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to reset all student sessions to 30?');">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Reset All Sessions
                        </button>
                    </form>
                    <button onclick="openAddStudentModal()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Add Student
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID Number</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Course</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Year Level</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<tr class='hover:bg-gray-50'>";
                                echo "<td class='px-6 py-4'>" . htmlspecialchars($row['IDNO']) . "</td>";
                                echo "<td class='px-6 py-4'>" . htmlspecialchars($row['Last_Name'] . ", " . $row['First_Name']) . "</td>";
                                echo "<td class='px-6 py-4'>" . htmlspecialchars($row['Course']) . "</td>";
                                echo "<td class='px-6 py-4'>" . htmlspecialchars($row['Year_lvl']) . "</td>";
                                echo "<td class='px-6 py-4'>
                                        <button onclick='deleteStudent(" . $row['IDNO'] . ")' 
                                                class='bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded'>
                                            Delete
                                        </button>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='px-6 py-4 text-center'>No students found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Student Modal -->
    <div id="addStudentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-xl w-96">
            <h2 class="text-xl font-bold mb-4">Add New Student</h2>
            <form action="add_student.php" method="POST">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="idno">ID Number</label>
                    <input type="text" id="idno" name="idno" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="firstname">First Name</label>
                    <input type="text" id="firstname" name="firstname" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="lastname">Last Name</label>
                    <input type="text" id="lastname" name="lastname" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="course">Course</label>
                    <select id="course" name="course" required class="shadow border rounded w-full py-2 px-3 text-gray-700">
                        <option value="" disabled selected>Select Course</option>
                        <option value="BSIT">BSIT</option>
                        <option value="BSCS">BSCS</option>
                        <option value="BSHM">BSHM</option>
                        <option value="BSBA">BSBA</option>
                        <option value="College of Customs Administration">College of Customs Administration</option>
                        <option value="College of Education">College of Education</option>
                        <option value="College of Engineering">College of Engineering</option>
                        <option value="College of Arts and Sciences">College of Arts and Sciences</option>
                        <option value="College of Nursing">College of Nursing</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="yearlevel">Year Level</label>
                    <select id="yearlevel" name="yearlevel" required class="shadow border rounded w-full py-2 px-3 text-gray-700">
                        <option value="1">1st Year</option>
                        <option value="2">2nd Year</option>
                        <option value="3">3rd Year</option>
                        <option value="4">4th Year</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeAddStudentModal()" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Cancel
                    </button>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Add Student
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function openAddStudentModal() {
        document.getElementById('addStudentModal').classList.remove('hidden');
        document.getElementById('addStudentModal').classList.add('flex');
    }

    function closeAddStudentModal() {
        document.getElementById('addStudentModal').classList.add('hidden');
        document.getElementById('addStudentModal').classList.remove('flex');
    }

    function deleteStudent(idno) {
        if(confirm('Are you sure you want to delete this student?')) {
            window.location.href = `delete_student.php?idno=${idno}`;
        }
    }
    </script>
</body>
</html>
