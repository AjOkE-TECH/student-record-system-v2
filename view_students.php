<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include "config/database.php";


/* =========================================================
   PAGINATION
========================================================= */

$limit = 8;

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$start = ($page - 1) * $limit;


/* =========================================================
   SEARCH
========================================================= */

$search = "";

if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

$where = "";

if ($search !== "") {

    $safe_search = mysqli_real_escape_string($conn, $search);

    $where = "WHERE matric_no LIKE '%$safe_search%'
              OR firstname LIKE '%$safe_search%'
              OR lastname LIKE '%$safe_search%'
              OR department LIKE '%$safe_search%'
              OR email LIKE '%$safe_search%'";
}


/* =========================================================
   COUNT STUDENTS
========================================================= */

$count_query = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM students
     $where"
);

$count_row = mysqli_fetch_assoc($count_query);

$total_students = (int) $count_row['total'];

$total_pages = $total_students > 0
    ? ceil($total_students / $limit)
    : 1;


/* =========================================================
   GET STUDENTS
========================================================= */

$query = mysqli_query(
    $conn,
    "SELECT *
     FROM students
     $where
     ORDER BY id DESC
     LIMIT $start, $limit"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>View Students - SRMS</title>
<link rel="stylesheet" href="assets/css/admin.css">
<link rel="stylesheet" href="assets/css/students.css">
</head>


<body>


<div class="container">
   <!-- sidebar -->
    <aside class="admin-sidebar">
        <div class="admin-brand">

            <div class="brand-mark">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
            </div>

            <div class="brand-text">

                <h2>
                    Student Record
                </h2>

                <span>
                    Management System
                </span>

            </div>

        </div>


        <nav class="admin-navigation">

            <div class="navigation-title">
                MAIN MENU
            </div>

            <ul>


                <li>

                    <a
                        href="dashboard.php"
                        class="active"
                    >

                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                        </span>

                        <span>
                            Dashboard
                        </span>

                    </a>

                </li>


                <li>

                    <a href="add_student.php">

                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="7" r="4"/><path d="M3 21v-2a6 6 0 0 1 12 0v2"/><path d="M15 3h6"/><path d="M18 0v6"/></svg>
                        </span>

                        <span>
                            Add Student
                        </span>

                    </a>

                </li>


                <li>

                    <a href="view_students.php">

                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="7" r="4"/><path d="M3 21v-2a6 6 0 0 1 12 0v2"/><circle cx="17" cy="9" r="3"/><path d="M21 21v-2a4 4 0 0 0-7.5-1.5"/></svg>
                        </span>

                        <span>
                            View Students
                        </span>

                    </a>

                </li>


                <li>

                    <a href="search_student.php">

                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
                        </span>

                        <span>
                            Search Student
                        </span>

                    </a>

                </li>


            </ul>


            <div class="navigation-title navigation-bottom-title">
                ACCOUNT
            </div>


            <ul>

                <li>

                    <a href="logout.php" class="logout-link">

                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        </span>

                        <span>
                            Logout
                        </span>

                    </a>

                </li>

            </ul>

        </nav>


        <div class="sidebar-footer">

            <strong>
                Student Record Management System
            </strong>

            <span>
                Administration Panel
            </span>

        </div>


    </aside>

    <!--main contentet -->

    <main class="main-content">


        <!-- TOP HEADER -->

        <header class="header">

            <div class="header-left">

                <div class="breadcrumb">
                    Student Management
                    <span>/</span>
                    Students
                </div>

                <h1>
                    Students
                </h1>

                <p>
                    Manage and view all registered students.
                </p>

            </div>


            <div class="header-right">

                <div class="admin-profile">

                    <div class="admin-avatar">
                        A
                    </div>

                    <div class="admin-info">

                        <strong>
                            Administrator
                        </strong>

                        <span>
                            System Admin
                        </span>

                    </div>

                </div>

            </div>

        </header>



        <!-- PAGE CONTENT -->

        <section class="students-page">


            <!-- PAGE TOP -->

            <div class="students-page-top">

                <div>

                    <h2>
                        Student Records
                    </h2>

                    <p>
                        View, search, edit and manage student information.
                    </p>

                </div>


                <a
                    href="add_student.php"
                    class="primary-button"
                >

                    <span class="button-plus">
                        +
                    </span>

                    Add Student

                </a>

            </div>



            <!-- STAT CARDS -->

            <div class="student-stat-grid">

                <div class="student-stat-card">

                    <div class="stat-icon stat-icon-green">
                        S
                    </div>

                    <div>

                        <span class="stat-label">
                            Total Students
                        </span>

                        <strong class="stat-value">
                            <?php echo number_format($total_students); ?>
                        </strong>

                    </div>

                </div>


                <div class="student-stat-card">

                    <div class="stat-icon stat-icon-blue">
                        R
                    </div>

                    <div>

                        <span class="stat-label">
                            Records Displayed
                        </span>

                        <strong class="stat-value">

                            <?php

                            if ($query) {
                                echo number_format(mysqli_num_rows($query));
                            } else {
                                echo "0";
                            }

                            ?>

                        </strong>

                    </div>

                </div>


                <div class="student-stat-card">

                    <div class="stat-icon stat-icon-orange">
                        F
                    </div>

                    <div>

                        <span class="stat-label">
                            Current Page
                        </span>

                        <strong class="stat-value">
                            <?php echo $page; ?>
                        </strong>

                    </div>

                </div>

            </div>



            <!-- SEARCH CARD -->

            <div class="student-search-card">

                <div class="search-card-title">

                    <div>

                        <h3>
                            Find a Student
                        </h3>

                        <p>
                            Search by name, matric number, department or email.
                        </p>

                    </div>

                </div>


                <form
                    method="GET"
                    action="view_students.php"
                    class="student-search-form"
                >

                    <div class="search-input-wrapper">

                        <span class="search-icon">
                            Q
                        </span>

                        <input
                            type="text"
                            name="search"
                            class="student-search-input"
                            placeholder="Search students..."
                            value="<?php echo htmlspecialchars($search); ?>"
                        >

                    </div>


                    <button
                        type="submit"
                        class="student-search-button"
                    >
                        Search
                    </button>


                    <?php if ($search !== ""): ?>

                        <a
                            href="view_students.php"
                            class="clear-search"
                        >
                            Clear
                        </a>

                    <?php endif; ?>

                </form>

            </div>



            <!-- TABLE CARD -->

            <div class="students-table-card">


                <div class="table-card-header">

                    <div>

                        <h3>
                            Registered Students
                        </h3>

                        <p>
                            <?php

                            if ($search !== "") {

                                echo "Showing results for \""
                                    . htmlspecialchars($search)
                                    . "\"";

                            } else {

                                echo "All registered student records";

                            }

                            ?>
                        </p>

                    </div>


                    <div class="table-count">

                        <strong>
                            <?php echo number_format($total_students); ?>
                        </strong>

                        <span>
                            student<?php echo $total_students == 1 ? '' : 's'; ?>
                        </span>

                    </div>

                </div>



                <?php if ($query && mysqli_num_rows($query) > 0): ?>


                    <div class="students-table-wrapper">

                        <table class="students-table">

                            <thead>

                                <tr>

                                    <th>
                                        #
                                    </th>

                                    <th>
                                        Student
                                    </th>

                                    <th>
                                        Matric Number
                                    </th>

                                    <th>
                                        Department
                                    </th>

                                    <th>
                                        Level
                                    </th>

                                    <th>
                                        Phone
                                    </th>

                                    <th>
                                        Actions
                                    </th>

                                </tr>

                            </thead>


                            <tbody>


                            <?php

                            $sn = $start + 1;

                            while ($row = mysqli_fetch_assoc($query)):

                                if (!empty($row['passport'])) {

                                    $student_passport =
                                        "assets/upload/students/"
                                        . $row['passport'];

                                } else {

                                    $student_passport =
                                        "assets/image/default.png";

                                }

                                $full_name =
                                    $row['firstname']
                                    . " "
                                    . $row['lastname'];

                            ?>

                                <tr>


                                    <!-- NUMBER -->

                                    <td class="serial-number">

                                        <?php echo $sn++; ?>

                                    </td>



                                    <!-- STUDENT -->

                                    <td>

                                        <div class="student-cell">

                                            <img
                                                src="<?php echo htmlspecialchars($student_passport); ?>"
                                                class="student-passport"
                                                alt="Student Passport"
                                                onerror="this.onerror=null;this.src='assets/image/default.png';"
                                            >


                                            <div class="student-details">

                                                <strong>
                                                    <?php
                                                    echo htmlspecialchars($full_name);
                                                    ?>
                                                </strong>

                                                <span>
                                                    <?php
                                                    echo htmlspecialchars(
                                                        $row['email'] ?? 'No email'
                                                    );
                                                    ?>
                                                </span>

                                            </div>

                                        </div>

                                    </td>



                                    <!-- MATRIC NUMBER -->

                                    <td>

                                        <span class="matric-badge">

                                            <?php
                                            echo htmlspecialchars(
                                                $row['matric_no']
                                            );
                                            ?>

                                        </span>

                                    </td>



                                    <!-- DEPARTMENT -->

                                    <td>

                                        <span class="department-text">

                                            <?php
                                            echo htmlspecialchars(
                                                $row['department']
                                            );
                                            ?>

                                        </span>

                                    </td>



                                    <!-- LEVEL -->

                                    <td>

                                        <span class="level-badge">

                                            <?php
                                            echo htmlspecialchars(
                                                $row['level']
                                            );
                                            ?>

                                        </span>

                                    </td>



                                    <!-- PHONE -->

                                    <td>

                                        <span class="phone-text">

                                            <?php
                                            echo htmlspecialchars(
                                                $row['phone']
                                            );
                                            ?>

                                        </span>

                                    </td>



                                    <!-- ACTIONS -->

                                    <td>

                                        <div class="student-actions">


                                            <a
                                                href="student_profile.php?id=<?php echo (int) $row['id']; ?>"
                                                class="table-action view-action"
                                                title="View Student"
                                            >
                                                View
                                            </a>


                                            <a
                                                href="edit_student.php?id=<?php echo (int) $row['id']; ?>"
                                                class="table-action edit-action"
                                                title="Edit Student"
                                            >
                                                Edit
                                            </a>


                                            <a
                                                href="delete_student.php?id=<?php echo (int) $row['id']; ?>"
                                                class="table-action delete-action"
                                                title="Delete Student"
                                                onclick="return confirm('Are you sure you want to delete this student?');"
                                            >
                                                Delete
                                            </a>


                                        </div>

                                    </td>


                                </tr>


                            <?php endwhile; ?>


                            </tbody>

                        </table>

                    </div>



                    <!-- PAGINATION -->

                    <?php if ($total_pages > 1): ?>

                        <div class="pagination-area">


                            <div class="pagination-info">

                                Showing

                                <strong>
                                    <?php echo $start + 1; ?>
                                </strong>

                                to

                                <strong>
                                    <?php echo min($start + $limit, $total_students); ?>
                                </strong>

                                of

                                <strong>
                                    <?php echo $total_students; ?>
                                </strong>

                            </div>


                            <div class="pagination">


                                <?php if ($page > 1): ?>

                                    <a
                                        href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>"
                                        class="page-arrow"
                                    >
                                        Previous
                                    </a>

                                <?php endif; ?>


                                <?php

                                $page_start = max(1, $page - 2);
                                $page_end = min($total_pages, $page + 2);

                                for ($i = $page_start; $i <= $page_end; $i++):

                                ?>

                                    <?php if ($i == $page): ?>

                                        <span class="active">
                                            <?php echo $i; ?>
                                        </span>

                                    <?php else: ?>

                                        <a
                                            href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"
                                        >
                                            <?php echo $i; ?>
                                        </a>

                                    <?php endif; ?>

                                <?php endfor; ?>


                                <?php if ($page < $total_pages): ?>

                                    <a
                                        href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>"
                                        class="page-arrow"
                                    >
                                        Next
                                    </a>

                                <?php endif; ?>


                            </div>

                        </div>

                    <?php endif; ?>


                <?php else: ?>


                    <!-- EMPTY STATE -->

                    <div class="no-students">

                        <div class="empty-icon">
                            S
                        </div>

                        <h3>
                            No students found
                        </h3>

                        <p>

                            <?php

                            if ($search !== "") {

                                echo "No student matches your search. Try a different name, matric number or department.";

                            } else {

                                echo "There are currently no registered students.";

                            }

                            ?>

                        </p>


                        <?php if ($search !== ""): ?>

                            <a
                                href="view_students.php"
                                class="empty-button"
                            >
                                View All Students
                            </a>

                        <?php else: ?>

                            <a
                                href="add_student.php"
                                class="empty-button"
                            >
                                Add First Student
                            </a>

                        <?php endif; ?>

                    </div>


                <?php endif; ?>


            </div>


        </section>


    </main>


</div>


</body>

</html>