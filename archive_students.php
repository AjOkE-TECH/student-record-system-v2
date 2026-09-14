<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login");
    exit();
}

include "config/database.php";


/*pagination */

$limit = 8;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$start = ($page - 1) * $limit;


/* admin name */

$admin_name = "Administrator";

if (
    isset($_SESSION['admin_name']) &&
    !empty($_SESSION['admin_name'])
) {
    $admin_name = $_SESSION['admin_name'];
}


/* handle restore action */

if (isset($_GET['restore'])) {

    $restore_id = (int) $_GET['restore'];

    mysqli_query(
        $conn,
        "UPDATE students
         SET archived_at = NULL
         WHERE id = $restore_id
         AND archived_at IS NOT NULL"
    );

    header("Location: archive_students");
    exit();
}


/* count archived students */

$count_query = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM students
     WHERE archived_at IS NOT NULL"
);

$count_row = mysqli_fetch_assoc($count_query);
$total_students = (int) $count_row['total'];
$total_pages = $total_students > 0 ? ceil($total_students / $limit) : 1;


/* get archived students */

$query = mysqli_query(
    $conn,
    "SELECT *
     FROM students
     WHERE archived_at IS NOT NULL
     ORDER BY archived_at DESC
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

    <title>Archived Students - SRMS</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="assets/css/students.css">
</head>


<body>


<div class="admin-layout">
   <!-- sidebar -->
    <aside class="admin-sidebar">
        <div class="admin-brand">

            <button
                class="sidebar-toggle"
                id="sidebarToggle"
                type="button"
                aria-label="Toggle sidebar"
                aria-expanded="true"
            >
                <span></span>
                <span></span>
                <span></span>
            </button>

            <img
                class="logo-image"
                src="assets/image/record_logo.png"
                alt="Student Record Management System Logo"
            >

        </div>


        <nav class="admin-navigation">

            <div class="navigation-title">
                MAIN MENU
            </div>

            <ul>


                <li>

                    <a href="dashboard">

                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                        </span>

                        <span>
                            Dashboard
                        </span>

                    </a>

                </li>


                <li>

                    <a href="add_student">

                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="7" r="4"/><path d="M3 21v-2a6 6 0 0 1 12 0v2"/><path d="M15 3h6"/><path d="M18 0v6"/></svg>
                        </span>

                        <span>
                            Add Student
                        </span>

                    </a>

                </li>


                <li>

                    <a href="view_students">

                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="7" r="4"/><path d="M3 21v-2a6 6 0 0 1 12 0v2"/><circle cx="17" cy="9" r="3"/><path d="M21 21v-2a4 4 0 0 0-7.5-1.5"/></svg>
                        </span>

                        <span>
                            View Students
                        </span>

                    </a>

                </li>


                <li>

                    <a href="archive_students" class="active">

                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="5" rx="1"/><path d="M4 8v12a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V8"/><path d="M10 12h4"/></svg>
                        </span>

                        <span> Archive </span>

                    </a>

                </li>


                <li>

                    <a href="result">

                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16v16H4z"/><path d="M8 16v-4"/><path d="M12 16V8"/><path d="M16 16v-7"/></svg>
                        </span>

                        <span>Results</span>

                    </a>

                </li>


                <li>

                    <a href="courses">

                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                        </span>

                        <span>
                            Courses
                        </span>

                    </a>

                </li>


                <li>

                    <a href="sessions">

                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </span>

                        <span>
                            Sessions
                        </span>

                    </a>

                </li>


                <li>

                    <a href="search_student">

                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
                        </span>

                        <span> Search Student</span>

                    </a>

                </li>


            </ul>


            <div class="navigation-title navigation-bottom-title">ACCOUNT</div>

            <ul>

                <li>

                    <a href="logout" class="logout-link">

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

    <!--main content -->

    <main class="admin-main">


        <!-- TOP HEADER -->

        <header class="admin-topbar">

            <div>

                <p class="topbar-label">
                    STUDENT RECORDS
                </p>

                <h1>
                    Archived Students
                </h1>

            </div>

        </header>



        <!-- PAGE CONTENT -->

        <section class="students-page">


            <!-- PAGE TOP -->

            <div class="students-page-top">

                <div>

                    <h2>
                        Archived Student Records
                    </h2>

                    <p>
                        Students that have been archived from the system. 
                        You can restore them at any time.
                    </p>

                </div>


                <a
                    href="view_students"
                    class="primary-button"
                >
                    Back to Active Students
                </a>

            </div>



            <!-- TABLE CARD -->

            <div class="students-table-card">


                <div class="table-card-header">

                    <div>

                        <h3>
                            Archived Students
                        </h3>

                        <p>
                            <?php
                            echo number_format($total_students) . " archived student"
                                . ($total_students == 1 ? '' : 's');
                            ?>
                        </p>

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
                                        Archived On
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

                                $full_name =
                                    $row['firstname']
                                    . " "
                                    . $row['lastname'];

                            ?>

                                <tr>


                                    <td class="serial-number">

                                        <?php echo $sn++; ?>

                                    </td>


                                    <td>

                                        <div class="student-cell">

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


                                    <td>

                                        <span class="matric-badge">

                                            <?php
                                            echo htmlspecialchars(
                                                $row['matric_no']
                                            );
                                            ?>

                                        </span>

                                    </td>


                                    <td>

                                        <span class="department-text">

                                            <?php
                                            echo htmlspecialchars(
                                                $row['department']
                                            );
                                            ?>

                                        </span>

                                    </td>


                                    <td>

                                        <span class="level-badge">

                                            <?php
                                            echo htmlspecialchars(
                                                $row['level']
                                            );
                                            ?>

                                        </span>

                                    </td>


                                    <td>

                                        <?php
                                        echo date(
                                            'd M Y, H:i',
                                            strtotime(
                                                $row['archived_at']
                                            )
                                        );
                                        ?>

                                    </td>


                                    <td>

                                        <div class="student-actions">

                                            <a
                                                href="archive_students?restore=<?php echo (int) $row['id']; ?>"
                                                class="table-action edit-action restore-action"
                                                title="Restore Student"
                                            >
                                                Restore
                                            </a>

                                        </div>

                                    </td>


                                </tr>


                            <?php endwhile; ?>


                            </tbody>

                        </table>

                    </div>



                    <?php if ($total_pages > 1): ?>

                        <div class="pagination-area">

                            <div class="pagination-info">

                                Showing
                                <strong><?php echo $start + 1; ?></strong>
                                to
                                <strong><?php echo min($start + $limit, $total_students); ?></strong>
                                of
                                <strong><?php echo $total_students; ?></strong>

                            </div>


                            <div class="pagination">

                                <?php if ($page > 1): ?>

                                    <a
                                        href="?page=<?php echo $page - 1; ?>"
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

                                        <a href="?page=<?php echo $i; ?>">
                                            <?php echo $i; ?>
                                        </a>

                                    <?php endif; ?>

                                <?php endfor; ?>

                                <?php if ($page < $total_pages): ?>

                                    <a
                                        href="?page=<?php echo $page + 1; ?>"
                                        class="page-arrow"
                                    >
                                        Next
                                    </a>

                                <?php endif; ?>

                            </div>

                        </div>

                    <?php endif; ?>


                <?php else: ?>

                    <div class="no-students">

                        <div class="empty-icon">
                            A
                        </div>

                        <h3>
                            No Archived Students
                        </h3>

                        <p>
                            There are currently no archived student records.
                        </p>

                        <a href="view_students" class="empty-button">
                            Back to Active Students
                        </a>

                    </div>

                <?php endif; ?>

            </div>

        </section>


    </main>


</div>


<script src="assets/js/sidebar.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.querySelectorAll('.restore-action').forEach(function (link) {
        link.addEventListener('click', function (event) {
            event.preventDefault();

            var href = this.getAttribute('href');

            Swal.fire({
                title: 'Restore Student?',
                text: 'Restore this student to the active list?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#006400',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, restore',
                cancelButtonText: 'Cancel'
            }).then(function (result) {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            });
        });
    });
</script>

</body>

</html>