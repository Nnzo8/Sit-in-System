<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../login.php");
    exit();
}

// Handle point addition before any output
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_point'])) {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "users";
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $student_id = $_POST['student_id'];
    
    // Check if student exists in student_points table
    $check_sql = "SELECT points FROM student_points WHERE IDNO = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $student_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        $update_sql = "UPDATE student_points SET points = points + 1 WHERE IDNO = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("i", $student_id);
    } else {
        $insert_sql = "INSERT INTO student_points (IDNO, points) VALUES (?, 1)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("i", $student_id);
    }
    
    if ($stmt->execute()) {
        $name_sql = "SELECT First_Name, Last_Name FROM students WHERE IDNO = ?";
        $name_stmt = $conn->prepare($name_sql);
        $name_stmt->bind_param("i", $student_id);
        $name_stmt->execute();
        $student = $name_stmt->get_result()->fetch_assoc();
        
        $_SESSION['success_message'] = "Successfully added 1 point to " . $student['First_Name'] . " " . $student['Last_Name'];
    } else {
        $_SESSION['error_message'] = "Error adding point to student.";
    }
    
    $conn->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Add this after the point handling code but before including header.php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_session'])) {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "users";
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $student_id = $_POST['student_id'];
    
    // Update sessions in the database
    $update_sql = "UPDATE student_session SET remaining_sessions = remaining_sessions + 1 WHERE id_number = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("i", $student_id);
    
    if ($stmt->execute()) {
        $name_sql = "SELECT First_Name, Last_Name FROM students WHERE IDNO = ?";
        $name_stmt = $conn->prepare($name_sql);
        $name_stmt->bind_param("i", $student_id);
        $name_stmt->execute();
        $student = $name_stmt->get_result()->fetch_assoc();
        
        $_SESSION['success_message'] = "Successfully added 1 session to " . $student['First_Name'] . " " . $student['Last_Name'];
    } else {
        $_SESSION['error_message'] = "Error adding session to student.";
    }
    
    $conn->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

include '../header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Points</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../css/admin-dark-mode.css">
    <script>
        tailwind.config = {
            darkMode: 'class',
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
        /* Add transition styles */
        * {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin-dark-mode.css">
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@4/dark.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 transition-all duration-300">
    <!-- Navigation -->
    <nav class="bg-primary shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center">
                <span class="text-white text-xl font-bold py-4">Lab Points</span>
                <div class="flex space-x-4">
                    <div class="hidden md:flex items-center space-x-4">
                        <a href="dashboard.php" class="nav-link text-white hover:text-gray-200">Dashboard</a>
                        <a href="search.php" class="nav-link text-white hover:text-gray-200">Search</a>
                        <a href="students.php" class="nav-link text-white hover:text-gray-200">Students</a>
                        <!-- Replace the sitin link with this dropdown -->
                        <div class="relative group">
                            <button class="nav-link text-white hover:text-gray-200 flex items-center">
                                Sit-in
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-800 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                <div class="py-1 rounded-md bg-white dark:bg-gray-800 shadow-xs">
                                    <a href="sitin.php" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Lab Sit-ins</a>
                                    <a href="sitin_logs.php" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Sit-in Logs</a>
                                </div>
                            </div>
                        </div>
                        
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
    <body class="bg-gray-100">
    <div class="max-w-7xl mx-auto py-6 px-4">
        <!-- Dashboard Header -->
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Lab Points</h1>

        <!-- Top 5 Students -->
        <div class="relative max-w-6xl mx-auto mb-8">
            <button id="prevBtn" class="absolute left-0 top-1/2 transform -translate-y-1/2 bg-primary/80 hover:bg-primary text-white rounded-full p-3 z-20 transition-all duration-300 disabled:opacity-50">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button id="nextBtn" class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-primary/80 hover:bg-primary text-white rounded-full p-3 z-20 transition-all duration-300 disabled:opacity-50">
                <i class="fas fa-chevron-right"></i>
            </button>
            
            <div class="overflow-hidden px-12">
                <div id="leaderboard" class="relative flex justify-center items-center min-h-[400px]">
                    <div class="main-cards flex justify-center items-center gap-8 transition-all duration-500">
                        <?php
                        $servername = "localhost";
                        $username = "root";
                        $password = "";
                        $dbname = "users"; // Changed from sit_in_db to users
                        
                        // Create connection
                        $conn = new mysqli($servername, $username, $password, $dbname);
                        
                        // Check connection
                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }

                        // Get top 5 students ordered by points
                        $sql = "SELECT 
                                s.IDNO,
                                s.First_Name,
                                s.Last_Name,
                                s.Course,
                                s.profile_image,
                                COALESCE(sp.points, 0) as points,
                                ss.remaining_sessions
                            FROM students s
                            LEFT JOIN student_points sp ON s.IDNO = sp.IDNO
                            LEFT JOIN student_session ss ON s.IDNO = ss.id_number
                            ORDER BY COALESCE(sp.points, 0) DESC, s.Last_Name ASC
                            LIMIT 5";

                        $result = $conn->query($sql);
                        $students = [];
                        while($row = $result->fetch_assoc()) {
                            $students[] = $row;
                        }

                        // Determine number of cards to show
                        $totalStudents = count($students);
                        $displayOrder = [];

                        if ($totalStudents >= 3) {
                            $displayOrder[] = ['student' => $students[1], 'rank' => 2]; // 2nd place
                            $displayOrder[] = ['student' => $students[0], 'rank' => 1]; // 1st place
                            $displayOrder[] = ['student' => $students[2], 'rank' => 3]; // 3rd place
                        }

                        // Store 4th and 5th places for the next slide
                        $extraCards = [];
                        if (isset($students[3]) || isset($students[4])) {
                            $extraCards[] = [
                                'students' => array_filter([
                                    isset($students[3]) ? ['student' => $students[3], 'rank' => 4] : null,
                                    isset($students[4]) ? ['student' => $students[4], 'rank' => 5] : null
                                ])
                            ];
                        }

                        // Display top 3 in the center
                        foreach($displayOrder as $item) {
                            $row = $item['student'];
                            $rank = $item['rank'];
                            $image = $row['profile_image'] ? '../' . $row['profile_image'] : '../images/default-avatar.png';
                            $trophy_color = $rank == 1 ? 'bg-yellow-500' : ($rank == 2 ? 'bg-gray-400' : 'bg-orange-500');
                            $card_class = $rank == 1 ? 'w-80 transform scale-110 z-10' : 'w-72';
                        ?>
                            <div class="leaderboard-card flex-none <?php echo $card_class; ?> transition-all duration-500" data-rank="<?php echo $rank; ?>">
                                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 text-center relative">
                                    <div class="absolute top-2 left-2 <?php echo $trophy_color; ?> text-white rounded-full px-3 py-1 text-sm font-bold">
                                        Top <?php echo $rank; ?>
                                    </div>
                                    <div class="relative">
                                        <img src="<?php echo $image; ?>" alt="Profile" class="w-24 h-24 rounded-full mx-auto mb-4 object-cover border-4 border-white dark:border-gray-700">
                                        <div class="absolute -top-2 -right-2 <?php echo $trophy_color; ?> rounded-full p-2">
                                            <i class="fas fa-trophy text-white"></i>
                                        </div>
                                    </div>
                                    <h3 class="font-semibold text-gray-800 dark:text-white mb-2">
                                        <?php echo htmlspecialchars($row['First_Name'] . ' ' . $row['Last_Name']); ?>
                                    </h3>
                                    <p class="text-gray-600 dark:text-gray-300 text-sm mb-2">
                                        <i class="fas fa-graduation-cap mr-2"></i><?php echo htmlspecialchars($row['Course']); ?>
                                    </p>
                                    <p class="text-lg font-bold text-primary dark:text-blue-400">
                                        <i class="fas fa-star mr-2"></i><?php echo $row['points']; ?> Points
                                    </p>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    
                    <!-- Separate container for extra cards -->
                    <div class="extra-cards-container absolute top-0 left-0 w-full h-full">
                        <?php foreach($extraCards as $group): ?>
                            <div class="leaderboard-card extra-card absolute top-1/2 left-1/2 w-full flex justify-center gap-8" style="transform: translate(-50%, -50%)">
                                <?php foreach($group['students'] as $item):
                                    $row = $item['student'];
                                    $rank = $item['rank'];
                                    $image = $row['profile_image'] ? '../' . $row['profile_image'] : '../images/default-avatar.png';
                                    $trophy_color = 'bg-blue-500';
                                ?>
                                    <div class="w-72">
                                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 text-center relative">
                                            <div class="absolute top-2 left-2 <?php echo $trophy_color; ?> text-white rounded-full px-3 py-1 text-sm font-bold">
                                                Top <?php echo $rank; ?>
                                            </div>
                                            <div class="relative">
                                                <img src="<?php echo $image; ?>" alt="Profile" class="w-24 h-24 rounded-full mx-auto mb-4 object-cover border-4 border-white dark:border-gray-700">
                                                <div class="absolute -top-2 -right-2 <?php echo $trophy_color; ?> rounded-full p-2">
                                                    <i class="fas fa-trophy text-white"></i>
                                                </div>
                                            </div>
                                            <h3 class="font-semibold text-gray-800 dark:text-white mb-2">
                                                <?php echo htmlspecialchars($row['First_Name'] . ' ' . $row['Last_Name']); ?>
                                            </h3>
                                            <p class="text-gray-600 dark:text-gray-300 text-sm mb-2">
                                                <i class="fas fa-graduation-cap mr-2"></i><?php echo htmlspecialchars($row['Course']); ?>
                                            </p>
                                            <p class="text-lg font-bold text-primary dark:text-blue-400">
                                                <i class="fas fa-star mr-2"></i><?php echo $row['points']; ?> Points
                                            </p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

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

            .leaderboard-card.active {
                transform: scale(1.1);
                z-index: 10;
            }
            
            .leaderboard-card.inactive {
                transform: scale(0.9);
                opacity: 0.6;
            }
            
            #leaderboard {
                scroll-behavior: smooth;
                position: relative;
                min-height: 400px;
            }

            .extra-card {
                position: absolute;
                left: 50%;
                top: 50%;
                transform: translate(-50%, -50%) scale(0.9);
                opacity: 0;
                pointer-events: none;
                transition: all 0.5s ease;
            }

            .extra-card.visible {
                opacity: 1;
                pointer-events: auto;
                transform: translate(-50%, -50%) scale(1.1);
            }

            .main-cards {
                transition: all 0.5s ease;
            }

            .main-cards.hidden {
                opacity: 0;
                transform: translateX(-100%);
            }
        </style>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const leaderboard = document.getElementById('leaderboard');
                const mainCardsContainer = document.querySelector('.main-cards');
                const extraCards = Array.from(document.querySelectorAll('.extra-card'));
                const prevBtn = document.getElementById('prevBtn');
                const nextBtn = document.getElementById('nextBtn');
                
                // Hide navigation buttons if no extra cards
                if (extraCards.length === 0) {
                    prevBtn.style.display = 'none';
                    nextBtn.style.display = 'none';
                }

                let currentView = 0; // 0 = default view, 1 = top 4, 2 = top 5

                function updateView() {
                    // Hide all extra cards first
                    extraCards.forEach(card => {
                        card.classList.remove('visible');
                    });

                    if (currentView === 0) {
                        // Show main cards
                        mainCardsContainer.classList.remove('hidden');
                    } else {
                        // Hide main cards
                        mainCardsContainer.classList.add('hidden');
                        // Show current extra card
                        const activeCard = extraCards[currentView - 1];
                        if (activeCard) {
                            activeCard.classList.add('visible');
                        }
                    }

                    // Update button states
                    prevBtn.disabled = currentView === 0;
                    nextBtn.disabled = currentView === extraCards.length;
                }

                // Initialize
                updateView();

                prevBtn.addEventListener('click', () => {
                    if (currentView > 0) {
                        currentView--;
                        updateView();
                    }
                });

                nextBtn.addEventListener('click', () => {
                    if (currentView < extraCards.length) {
                        currentView++;
                        updateView();
                    }
                });

                // Add keyboard navigation
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'ArrowLeft' && !prevBtn.disabled) prevBtn.click();
                    if (e.key === 'ArrowRight' && !nextBtn.disabled) nextBtn.click();
                });
            });
        </script>

        <!-- All Students Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-700 dark:text-white">All Students</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Course</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Sessions Left</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Points</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                        <?php
                        // First, ensure all students have session records
                        $init_sql = "INSERT IGNORE INTO student_session (id_number, remaining_sessions)
                                   SELECT IDNO, 30 FROM students 
                                   WHERE IDNO NOT IN (SELECT id_number FROM student_session)";
                        $conn->query($init_sql);

                        // Now fetch student data with their actual session counts
                        $sql = "SELECT 
                                s.IDNO,
                                s.First_Name,
                                s.Last_Name,
                                s.Course,
                                COALESCE(sp.points, 0) as points,
                                CASE 
                                    WHEN ss.remaining_sessions IS NULL THEN 30
                                    ELSE ss.remaining_sessions 
                                END as remaining_sessions
                            FROM students s
                            LEFT JOIN student_points sp ON s.IDNO = sp.IDNO
                            LEFT JOIN student_session ss ON s.IDNO = ss.id_number
                            ORDER BY COALESCE(sp.points, 0) DESC, s.Last_Name ASC";  // Changed ORDER BY clause

                        $result = $conn->query($sql);
                        while($row = $result->fetch_assoc()) {
                        ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 text-gray-800 dark:text-gray-200">
                                    <?php echo htmlspecialchars($row['First_Name'] . ' ' . $row['Last_Name']); ?>
                                </td>
                                <td class="px-6 py-4 text-gray-800 dark:text-gray-200">
                                    <?php echo htmlspecialchars($row['Course']); ?>
                                </td>
                                <td class="px-6 py-4 text-gray-800 dark:text-gray-200">
                                    <?php echo $row['remaining_sessions']; ?>
                                </td>
                                <td class="px-6 py-4 text-gray-800 dark:text-gray-200">
                                    <?php echo $row['points']; ?>
                                </td>
                                <td class="px-6 py-4 space-x-2">
                                    <form method="POST" class="inline">
                                        <input type="hidden" name="student_id" value="<?php echo $row['IDNO']; ?>">
                                        <button type="button" onclick="confirmAddPoint(this.form, '<?php echo htmlspecialchars($row['First_Name'] . ' ' . $row['Last_Name']); ?>')" 
                                            class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm">
                                            <i class="fas fa-plus-circle mr-1"></i> Point
                                        </button>
                                        <button type="button" onclick="confirmAddSession(this.form, '<?php echo htmlspecialchars($row['First_Name'] . ' ' . $row['Last_Name']); ?>')" 
                                            class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">
                                            <i class="fas fa-clock mr-1"></i> Add Session
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add this right after the header navigation -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div id="successAlert" class="fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded z-50">
            <span class="block sm:inline"><?php echo $_SESSION['success_message']; ?></span>
            <button onclick="this.parentElement.style.display='none'" class="absolute top-0 right-0 px-4 py-3">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div id="errorAlert" class="fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded z-50">
            <span class="block sm:inline"><?php echo $_SESSION['error_message']; ?></span>
            <button onclick="this.parentElement.style.display='none'" class="absolute top-0 right-0 px-4 py-3">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <script>
        async function confirmAddPoint(form, studentName) {
            const result = await Swal.fire({
                title: 'Add Point',
                text: `Are you sure you want to add a point to ${studentName}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, add point',
                cancelButtonText: 'Cancel'
            });

            if (result.isConfirmed) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'add_point';
                input.value = '1';
                form.appendChild(input);
                form.submit();
            }
        }

        async function confirmAddSession(form, studentName) {
            const result = await Swal.fire({
                title: 'Add Session',
                text: `Are you sure you want to add a session for ${studentName}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, add session',
                cancelButtonText: 'Cancel'
            });

            if (result.isConfirmed) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'add_session';
                input.value = '1';
                form.appendChild(input);
                form.submit();
            }
        }

        // Mobile navigation toggle function
        function toggleNav() {
            const navbarNav = document.getElementById('navbarNav');
            navbarNav.classList.toggle('hidden');
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
    
        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const successAlert = document.getElementById('successAlert');
                const errorAlert = document.getElementById('errorAlert');
                if (successAlert) successAlert.style.display = 'none';
                if (errorAlert) errorAlert.style.display = 'none';
            }, 5000);
        });

        function confirmAction(form, studentName) {
            if (form.add_point) {
                return Swal.fire({
                    title: 'Confirm Point Addition',
                    text: `Are you sure you want to add a point to ${studentName}?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, add point'
                }).then((result) => {
                    return result.isConfirmed;
                });
            }
            return true; // For other form submissions
        }
    </script>
</body>
</html>