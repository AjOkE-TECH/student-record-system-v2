<?php
session_start();

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

include "config/database.php";

$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

$query = mysqli_query($conn,
    "SELECT * FROM students ORDER BY id DESC LIMIT $start, $limit");

$total_query = mysqli_query($conn,
    "SELECT COUNT(id) AS total FROM students");

$total_result = mysqli_fetch_assoc($total_query);
$total_records = $total_result['total'];
$total_pages = ceil($total_records / $limit);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Students</title>
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
            <h1>Student Records</h1>
        </div>

        <div class="top-actions">

            <form action="search_student.php" method="GET" class="search-form">
                <input type="text" name="search"
                    placeholder="Search by name">
                <button type="submit">Search</button>
            </form>

            <a href="add_student.php" class="create-btn">+ Create Student</a>

        </div>

        <table>
            <tr>
                <th>S/N</th>
                <th>Matric No</th>
                <th>Name</th>
                <th>Gender</th>
                <th>Department</th>
                <th>Level</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>

            <?php $sn = $start + 1; ?>
            <?php while($row = mysqli_fetch_assoc($query)){ ?>

                 <tr>
                    <td><?php echo $sn++; ?></td>
                    <td><?php echo $row['matric_no']; ?></td>
                    <td><?php echo $row['firstname'] . " " . $row['lastname']; ?></td>
                    <td><?php echo $row['gender']; ?></td>
                    <td><?php echo $row['department']; ?></td>
                    <td><?php echo $row['level']; ?></td>
                    <td><?php echo $row['phone']; ?></td>
                    <td><?php echo $row['email']; ?></td>

                  <td>
                    <div class="action-buttons">
                      <a href="edit_student.php?id=<?php echo $row['id']; ?>" class="edit-btn">✏</a>

                      <a href="delete_student.php?id=<?php echo $row['id']; ?>"
                      class="delete-btn"
                      onclick="return confirm('Are you sure you want to delete this student?')">🗑</a>
                   </div>
                 </td>
               </tr>
            <?php } ?>
       </table>

        <div class="pagination">
            <?php for($i = 1; $i <= $total_pages; $i++){ ?>
                <a href="view_students.php?page=<?php echo $i; ?>">
                    <?php echo $i; ?>
                </a>
            <?php } ?>
        </div>

    </div>

</div>

</body>
</html>