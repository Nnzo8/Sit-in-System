<?php include 'header.php'; ?>

<div class="min-h-screen flex items-center justify-center py-12 px-4 slide-in-top">
    <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
        <div class="flex justify-center space-x-4 mb-6">
            <img src="imgs/uc.png" alt="UC Logo" class="h-20">
            <img src="imgs/ccs.png" alt="CCS Logo" class="h-20">
        </div>

        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Registration</h2>

        <?php
            $servername = "localhost";
            $username = "root";
            $password = "";
            $database = "users";
            
            $conn = new mysqli($servername, $username, $password, $database);
            
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $idno = $_POST['idno'];
                $lastname = $_POST['lastname'];
                $firstname = $_POST['firstname'];
                $midname = $_POST['midname'];
                $course = $_POST['course'];
                $yearlvl = $_POST['yearlvl'];
                $username = $_POST['username'];
                
                // Password validation
                $password = $_POST['password'];
                if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
                    echo "<div class='alert alert-danger'>Password must be at least 8 characters long and contain uppercase, lowercase, number, and special character.</div>";
                    exit;
                }
                
                $password = password_hash($password, PASSWORD_DEFAULT);
        
                $sql = "INSERT INTO students (IDNO, Last_Name, First_Name, Mid_Name, Course, Year_lvl, Username, Password) VALUES ('$idno', '$lastname', '$firstname', '$midname', '$course', '$yearlvl', '$username', '$password')";
        
                if ($conn->query($sql) === TRUE) {
                    echo "<div class='alert alert-success'>Student Added Successfully!</div>";
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            }
            
            $conn->close();
        ?>
        <title>Registration</title>
        <form action="registration.php" method="post" class="space-y-4">
            <input type="number" name="idno" placeholder="IDNO" 
                class="form-control" required min="0">
            <input type="text" name="lastname" placeholder="Last Name" 
                class="form-control" required oninput="validateText(event)">
            <input type="text" name="firstname" placeholder="First Name" 
                class="form-control" required oninput="validateText(event)">
            <input type="text" name="midname" placeholder="Middle Name" 
                class="form-control" oninput="validateText(event)">
            
            <select name="course" class="form-control" required>
                <option value="" disabled selected>Course</option>
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

            <select name="yearlvl" class="form-control" required>
                <option value="" disabled selected>Year Level</option>
                <option value="1">1st Year</option>
                <option value="2">2nd Year</option>
                <option value="3">3rd Year</option>
                <option value="4">4th Year</option>
            </select>

            <input type="text" name="username" placeholder="Username" 
                class="form-control" required>
            <div class="space-y-2">
                <input type="password" name="password" id="password" placeholder="Password" 
                    class="form-control" required pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}"
                    oninput="validatePassword(this)">
                <p class="text-sm text-gray-600">Password must be at least 8 characters long and contain uppercase, lowercase, number, and special character.</p>
                <p id="password-error" class="text-sm text-red-500 hidden"></p>
            </div>

            <div class="flex flex-col items-center space-y-4">
                <button type="submit" name="submit" 
                    class="btn-primary w-full">Register</button>
                <a href="login.php" 
                    class="text-primary hover:text-blue-700">Login</a>
            </div>
        </form>
    </div>
</div>

<script>
function validatePassword(input) {
    const password = input.value;
    const errorElement = document.getElementById('password-error');
    const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
    
    if (!regex.test(password)) {
        errorElement.textContent = 'Password requirements not met';
        errorElement.classList.remove('hidden');
        input.setCustomValidity('Password requirements not met');
    } else {
        errorElement.classList.add('hidden');
        input.setCustomValidity('');
    }
}
</script>
</body>
</html>