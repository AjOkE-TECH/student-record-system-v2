<?php
session_start();

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

include "config/database.php";

$search_result = null;

if(isset($_POST['search'])){

    $keyword = $_POST['keyword'];

    $search_query = mysqli_query($conn,
        "SELECT * FROM students
        WHERE matric_no LIKE '%$keyword%'
        OR firstname LIKE '%$keyword%'
        OR lastname LIKE '%$keyword%'"
    );

    $search_result = $search_query;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Student</title>
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
            <li><a href="search_student.php">Search Student</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">

        <div class="header">
            <h1>Search Student</h1>
        </div>

        <form method="POST" class="search-form">
            <input type="text" name="keyword"
                   placeholder="Enter Matric Number or Name" required>

            <button type="submit" name="search">Search</button>
        </form>

        <?php if($search_result){ ?>

        <table>
            <tr>
                <th>ID</th>
                <th>Matric No</th>
                <th>Name</th>
                <th>Gender</th>
                <th>Department</th>
                <th>Level</th>
                <th>Phone</th>
            </tr>

            <?php while($row = mysqli_fetch_assoc($search_result)){ ?>

            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['matric_no']; ?></td>
                <td><?php echo $row['firstname']." ".$row['lastname']; ?></td>
                <td><?php echo $row['gender']; ?></td>
                <td><?php echo $row['department']; ?></td>
                <td><?php echo $row['level']; ?></td>
                <td><?php echo $row['phone']; ?></td>
            </tr>

            <?php } ?>

        </table>

        <?php } ?>

    </div>

</div>

</body>
</html>