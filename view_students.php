<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include "config/database.php";

$limit = 5;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$start = ($page - 1) * $limit;

/* GET STUDENTS */
$query = mysqli_query(
    $conn,
    "SELECT * FROM students ORDER BY id DESC LIMIT $start, $limit"
);

/* COUNT STUDENTS */
$total_query = mysqli_query(
    $conn,
    "SELECT COUNT(id) AS total FROM students"
);

$total_result = mysqli_fetch_assoc($total_query);
$total_records = $total_result['total'];

$total_pages = ($total_records > 0)
    ? ceil($total_records / $limit)
    : 1;
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>View Students - Student Record Management System</title>

    <link rel="stylesheet" href="assets/css/style.css">

    <style>

        .students-page {
            width: 100%;
        }

        .top-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            margin-bottom: 25px;
        }

        .search-form {
            display: flex;
            gap: 10px;
            flex: 1;
            max-width: 500px;
        }

        .search-form input {
            flex: 1;
            padding: 13px 16px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            outline: none;
        }

        .search-form input:focus {
            border-color: #006400;
        }

        .search-form button {
            padding: 13px 22px;
            border: none;
            border-radius: 8px;
            background: #006400;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        .search-form button:hover {
            background: #008000;
        }

        .create-btn {
            display: inline-block;
            padding: 13px 20px;
            background: #006400;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            white-space: nowrap;
        }

        .create-btn:hover {
            background: #008000;
        }

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        .students-table {
            width: 100%;
            min-width: 1200px;
            border-collapse: collapse;
        }

        .students-table th {
            background: #006400;
            color: white;
            padding: 18px 12px;
            text-align: left;
            font-size: 15px;
            white-space: nowrap;
        }

        .students-table th:first-child {
            border-radius: 15px 0 0 0;
        }

        .students-table th:last-child {
            border-radius: 0 15px 0 0;
        }

        .students-table td {
            padding: 15px 12px;
            border-bottom: 1px solid #eeeeee;
            color: #333;
            vertical-align: middle;
        }

        .students-table tbody tr:hover {
            background: #f8fff8;
        }

        .passport-image {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e8f5e9;
            display: block;
        }

        .student-name {
            color: #006400;
            font-weight: 600;
            text-decoration: none;
        }

        .student-name:hover {
            text-decoration: underline;
        }

        .action-buttons {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .action-btn {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 17px;
            transition: 0.2s;
        }

        .view-btn {
            background: #eee;
            color: #006400;
        }

        .view-btn:hover {
            background: #d9f0dc;
        }

        .edit-btn {
            background: #20a849;
            color: white;
        }

        .edit-btn:hover {
            background: #178b3b;
        }

        .delete-btn {
            background: #dc3545;
            color: white;
        }

        .delete-btn:hover {
            background: #b92332;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 25px;
            margin-bottom: 20px;
        }

        .pagination a {
            min-width: 40px;
            height: 40px;
            padding: 0 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #006400;
            border-radius: 8px;
            color: #006400;
            text-decoration: none;
            font-weight: bold;
        }

        .pagination a:hover,
        .pagination a.active {
            background: #006400;
            color: white;
        }

        .no-students {
            text-align: center;
            padding: 40px;
            color: #777;
        }

    </style>
</head>
<body>
<div class="container">
    <!-- SIDEBAR -->
    <div class="sidebar">
        <h2>Management System</h2>
        <ul>
            <li>
                <a href="dashboard.php">Dashboard </a>
            </li>

            <li>
                <a href="add_student.php">Add Student</a>
            </li>

            <li>
                <a href="view_students.php" class="active"> View Students</a>
            </li>

            <li>
                <a href="search_student.php"> Search Student  </a>
            </li>

            <li>
                <a href="logout.php">  Logout</a>
            </li>
        </ul>
    </div>
    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="students-page">
            <!-- HEADER -->
            <div class="header">
                <div>
                    <h1>Student Records</h1>
                    <p> View and manage registered students. </p>
                </div>
            </div>
            <!-- SEARCH + CREATE -->
            <div class="top-actions">
                <form
                    action="search_student.php"
                    method="GET"
                    class="search-form"
                >
                    <input
                        type="text"
                        name="search"
                        placeholder="Search by name, matric number or email"
                    >
                    <button type="submit"> Search </button>
                </form>
                <a href="add_student.php" class="create-btn"> + Create Student </a>
            </div>
            <!-- TABLE -->
            <div class="table-wrapper">
                <table class="students-table">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Passport</th>
                            <th>Matric No</th>
                            <th>Name</th>
                            <th>Gender</th>
                            <th>Department</th>
                            <th>Level</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (mysqli_num_rows($query) > 0): ?>
                        <?php
                        $sn = $start + 1;
                        while ($row = mysqli_fetch_assoc($query)):
                        ?>
                        <tr>
                            <!-- S/N -->
                            <td>
                                <?php echo $sn++; ?>
                            </td>
                            <!-- PASSPORT -->
                            <td>

                                <?php if (!empty($row['passport'])): ?>
                                    <img
                                        src="assets/upload/students/<?php echo htmlspecialchars($row['passport']); ?>"
                                        class="passport-image" alt="Student Passport"
                                        onerror="this.onerror=null;this.src='assets/image/default.png';">
                                <?php else: ?>
                                    <img
                                        src="assets/image/default.png"class="passport-image"
                                        alt="No Passport" >
                                <?php endif; ?>
                            </td>
                            <!-- MATRIC NUMBER -->
                            <td>
                                <?php
                                echo htmlspecialchars($row['matric_no'] ); ?>
                            </td>
                            <!-- NAME -->
                            <td>
                                <a
                                    href="student_profile.php?id=<?php echo $row['id']; ?>"
                                    style="color:#000000; text-decoration:none;">
                                    <?php
                                    echo htmlspecialchars($row['firstname'] . " " . $row['lastname']);
                                    ?>
                               </a>

                                </a>
                            </td>
                            <!-- GENDER -->
                            <td>
                                <?php echo htmlspecialchars($row['gender']);?>
                            </td>
                            <!-- DEPARTMENT -->
                            <td>
                                <?php echo htmlspecialchars($row['department']); ?>
                            </td>
                            <!-- LEVEL -->
                            <td>
                                <?php echo htmlspecialchars($row['level']);?>
                            </td>
                            <!-- PHONE -->
                            <td>
                                <?php echo htmlspecialchars($row['phone']);?>
                            </td>
                            <!-- EMAIL -->
                            <td>
                                <?php echo htmlspecialchars($row['email']);?>
                            </td>
                            <!-- ACTIONS -->
                            <td>
                                <div class="action-buttons">

                                    <!-- VIEW -->
                                    <a
                                        href="student_profile.php?id=<?php echo (int)$row['id']; ?>"
                                        class="action-btn view-btn"title="View Student">👁
                                    </a>
                                    <!-- EDIT -->
                                    <a
                                        href="edit_student.php?id=<?php echo (int)$row['id']; ?>"
                                        class="action-btn edit-btn"title="Edit Student"> ✏
                                    </a>
                                    <!-- DELETE -->
                                    <a
                                        href="delete_student.php?id=<?php echo (int)$row['id']; ?>"
                                        class="action-btn delete-btn"title="Delete Student"
                                        onclick="return confirm('Are you sure you want to delete this student?')">🗑
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="no-students">
                                No student records found.
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>

                </table>

            </div>
            <!-- PAGINATION -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a
                            href="view_students.php?page=<?php echo $i; ?>"
                            class="<?php echo ($page == $i) ? 'active' : ''; ?>"
                        >
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>