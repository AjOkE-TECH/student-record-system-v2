<?php
session_start();

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

include "config/database.php";

if(!isset($_GET['id'])){
    header("Location: view_students.php");
    exit();
}

$id = (int)$_GET['id'];

$query = mysqli_query($conn, "SELECT * FROM students WHERE id='$id'");

if(mysqli_num_rows($query) == 0){
    echo "Student not found.";
    exit();
}

$row = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Profile</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="container">

    <div class="sidebar">

        <h2>Management System</h2>

        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="add_student.php">Add Student</a></li>
            <li><a href="view_students.php" class="active">View Students</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>

    </div>

    <div class="main-content">

        <div class="header">
            <h1>Student Profile</h1>
        </div>

        <div class="profile-card">

            <div class="profile-left">

               <?php
                    if(!empty($row['passport'])){
                    ?>
                        <img src="assets/upload/students/<?php echo $row['passport']; ?>"
                            width="150"
                            height="150"
                            style="border-radius:50%; object-fit:cover;">
                    <?php
                    }else{
                    ?>
                        <img src="assets/image/default.png"
                            width="150"
                            height="150"
                            style="border-radius:50%; object-fit:cover;">
                    <?php
                    }
                ?>

            </div>

            <div class="profile-right">

                <h2>
                    <?php echo $row['firstname']." ".$row['lastname']; ?>
                </h2>

                <table class="profile-table">

                    <tr>
                        <th>Matric Number</th>
                        <td><?php echo $row['matric_no']; ?></td>
                    </tr>

                    <tr>
                        <th>Gender</th>
                        <td><?php echo $row['gender']; ?></td>
                    </tr>

                    <tr>
                        <th>Department</th>
                        <td><?php echo $row['department']; ?></td>
                    </tr>

                    <tr>
                        <th>Level</th>
                        <td><?php echo $row['level']; ?></td>
                    </tr>

                    <tr>
                        <th>Phone</th>
                        <td><?php echo $row['phone']; ?></td>
                    </tr>

                    <tr>
                        <th>Email</th>
                        <td><?php echo $row['email']; ?></td>
                    </tr>

                </table>

                <br>

                <a href="edit_student.php?id=<?php echo $row['id']; ?>" class="create-btn">
                    Edit Student
                </a>

                <a href="#" onclick="window.print()" class="create-btn">
                    Print Profile
                </a>

            </div>

        </div>

    </div>

</div>

</body>
</html>