<?php
session_start();

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

include "config/database.php";

/* Count all students */
$totalStudents = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM students")
);

/* Count male students */
$totalMale = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM students WHERE gender='Male'")
);

/* Count female students */
$totalFemale = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM students WHERE gender='Female'")
);

/* Count departments */
$totalDepartment = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(DISTINCT department) AS total FROM students")
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="container">

    <div class="sidebar">

        <h2>Management System</h2>

        <ul>
            <li><a href="dashboard.php" class="active">Dashboard</a></li>
            <li><a href="add_student.php">Add Student</a></li>
            <li><a href="view_students.php">View Students</a></li>
            <li><a href="search_student.php">Search Student</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>

    </div>

    <div class="main-content">

        <div class="header">
            <div>
                <h1>Dashboard</h1>
                <p>Welcome, <?php echo $_SESSION['admin']; ?></p>
            </div>
        </div>

        <div class="cards">

            <a href="view_students.php" class="card-link">
                <div class="card">
                    <div class="card-icon">🎓</div>
                    <h3>Total Students</h3>
                    <p><?php echo $totalStudents['total']; ?></p>
                </div>
            </a>

            <a href="view_students.php?gender=Male" class="card-link">
                <div class="card">
                    <div class="card-icon">👨</div>
                    <h3>Male Students</h3>
                    <p><?php echo $totalMale['total']; ?></p>
                </div>
            </a>

            <a href="view_students.php?gender=Female" class="card-link">
                <div class="card">
                    <div class="card-icon">👩</div>
                    <h3>Female Students</h3>
                    <p><?php echo $totalFemale['total']; ?></p>
                </div>
            </a>

            <div class="card">
                <div class="card-icon">🏫</div>
                <h3>Departments</h3>
                <p><?php echo $totalDepartment['total']; ?></p>
            </div>

        </div>

    </div>

</div>

</body>
</html>