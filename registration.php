<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Registration Form</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link rel="stylesheet" href="style.css">
<script>
function validateText(event) {
    const textInput = event.target;
    textInput.value = textInput.value.replace(/[^a-zA-Z\s]/g, '');
}
</script>
<style>
body {
    background: linear-gradient(to bottom, #291c0e, #e1d4c2);
}
</style>
</head>
<body>
<div class="container">
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
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO students (IDNO, Last_Name, First_Name, Mid_Name, Course, Year_lvl, Username, Password) VALUES ('$idno', '$lastname', '$firstname', '$midname', '$course', '$yearlvl', '$username', '$password')";

        if ($conn->query($sql) === TRUE) {
            echo "<div class='alert alert-success'>Student Added Successfully!</div>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
    
    $conn->close();
    
?>
<form action="registration.php" method="post">
<div class="logo-container">
    <img src="imgs/uc.png" alt="Logo" class="logo uclogo">
    <img src="imgs/ccs.png" alt="Logo" class="logo">
</div>
<header class="text-center">Registration</header>
<div class="form-group">
<input type="number" class="form-control" name="idno" placeholder="IDNO: " required min="0">
</div>
<div class="form-group">
<input type="text" class="form-control" name="lastname" placeholder="Last Name: " required oninput="validateText(event)">
</div>
<div class="form-group">
<input type="text" class="form-control" name="firstname" placeholder="First Name: " required oninput="validateText(event)">
</div>
<div class="form-group">
<input type="text" class="form-control" name="midname" placeholder="Mid Name: " oninput="validateText(event)">
</div>
<div class="form-group">
<select class="form-control" name="course" required>
<option value="" disabled selected>Course</option>
<option value="1">BSIT</option>
<option value="2">BSCS</option>
<option value="3">BSCpe</option>
</select>
</div>
<div class="form-group">
<select class="form-control" name="yearlvl" required>
<option value="" disabled selected>Year Level</option>
<option value="1">1st Year</option>
<option value="2">2nd Year</option>
<option value="3">3rd Year</option>
<option value="4">4th Year</option>
</select>
</div>
<div class="form-group">
<input type="text" class="form-control" name="username" placeholder="Username: " required>
</div>
<div class="form-group">
<input type="password" class="form-control" name="password" placeholder="Password: " required>
</div>
<div class="btns">
<div class="form-btn">
<input type="submit" class="btn btn-primary" value="Register" name="submit">
</div>
<div class="text">
<a href="login.php" class="register-link">Login</a>
</div>
</div>
</form>
</div>
</body>
</html>