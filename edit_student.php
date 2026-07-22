<?php
session_start();

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}
include "config/database.php";

$id = $_GET['id'];

$query = mysqli_query($conn, "SELECT * FROM students WHERE id='$id'");
$student = mysqli_fetch_assoc($query);

if(isset($_POST['update_student'])){

    $matric_no = $_POST['matric_no'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $gender = $_POST['gender'];
    $department = $_POST['department'];
    $level = $_POST['level'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];


    $update = mysqli_query($conn, "UPDATE students SET
        matric_no='$matric_no',
        firstname='$firstname',
        lastname='$lastname',
        gender='$gender',
        department='$department',
        level='$level',
        phone='$phone',
        email='$email'
        WHERE id='$id'
    ");

    if($update){
        header("Location: view_students.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Student</title>
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
            <h1>Edit Student</h1>
        </div>

        <form method="POST" class="student-form">

            <input type="text" name="matric_no"
                value="<?php echo $student['matric_no']; ?>" required>

            <input type="text" name="firstname"
                value="<?php echo $student['firstname']; ?>" required>

            <input type="text" name="lastname"
                value="<?php echo $student['lastname']; ?>" required>

            <select name="gender" required>
                <option><?php echo $student['gender']; ?></option>
                <option>Male</option>
                <option>Female</option>
            </select>

            <input type="text" name="department"
                value="<?php echo $student['department']; ?>" required>

            <input type="text" name="level"
                value="<?php echo $student['level']; ?>" required>

            <input type="text" name="phone"
                value="<?php echo $student['phone']; ?>">

            <input type="text" name="email"
                value="<?php echo $student['email']; ?>">

            <button type="submit" name="update_student">
                Update Student
            </button>

        </form>

    </div>

</div>

</body>
</html>