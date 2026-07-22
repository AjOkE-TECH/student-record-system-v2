<?php
session_start();

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

include "config/database.php";

if(isset($_POST['add_student'])){

    $matric_no = $_POST['matric_no'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $gender = $_POST['gender'];
    $department = $_POST['department'];
    $level = $_POST['level'];
    $phone = $_POST['phone'];
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $sql = "INSERT INTO students
            (matric_no, firstname, lastname, gender, department, level, phone, email)
            VALUES
            ('$matric_no','$firstname','$lastname','$gender','$department','$level','$phone','$email')";

  if(mysqli_query($conn, $sql)){

    // Include PHPMailer function
    require "send_email.php";
    // Send email to the student
    sendStudentEmail(
        $email,
        $firstname . " " . $lastname
    );
    $success = "Student added successfully! Email notification sent.";
}else{
    $error = "Failed to add student!";
}
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Student</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="container">

    <div class="sidebar">
        <h2>Management System</h2>

        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="add_student.php">Add Student</a></li>
            <li><a href="view_students.php">View Students</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">

        <div class="header">
            <h1>Add Student</h1>
        </div>

        <?php if(isset($success)){ ?>
            <div class="success"><?php echo $success; ?></div>
        <?php } ?>

        <?php if(isset($error)){ ?>
            <div class="error"><?php echo $error; ?></div>
        <?php } ?>

        <form method="POST" class="student-form">

            <input type="text" name="matric_no" placeholder="e.g NCSF/23/0025" required>

            <input type="text" name="firstname" placeholder="e.g Sekinat" required>

            <input type="text" name="lastname" placeholder="e.g Mutolib" required>

            <select name="gender" required>
            <option value="">Select Gender</option>
            <option>Male</option>
            <option>Female</option>
            </select>

            <input type="text" name="department" placeholder="e.g Computer Science" required>

            <input type="text" name="level" placeholder="e.g ND II" required>

            <input type="text" name="phone" placeholder="e.g 07051716653">

            <input type="email" name="email" placeholder="e.g. sekinat@gmail.com" required>

            <button type="submit" name="add_student">
                Add Student
            </button>

        </form>

    </div>

</div>

</body>
</html>