<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login");
    exit();
}

include "config/database.php";

$current_page = basename($_SERVER['PHP_SELF']);


/* =========================================================
   ADMIN NAME
========================================================= */

$admin_name = "Administrator";

if (
    isset($_SESSION['admin_name']) &&
    !empty($_SESSION['admin_name'])
) {
    $admin_name = $_SESSION['admin_name'];
}


/* =========================================================
   SEARCH + FILTER + PAGINATION
========================================================= */

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page);

$limit = 10;
$start = ($page - 1) * $limit;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$session_filter = isset($_GET['session']) ? (int)$_GET['session'] : 0;

$view = (isset($_GET['view']) && $_GET['view'] === 'archived') ? 'archived' : 'active';

if ($view === 'archived') {
    $where = "WHERE courses.archived_at IS NOT NULL";
} else {
    $where = "WHERE courses.archived_at IS NULL";
}

if ($search != '') {
    $search_escaped = $conn->real_escape_string($search);
    $where .= " AND (courses.course_code LIKE '%$search_escaped%'
                   OR courses.course_title LIKE '%$search_escaped%')";
}

if ($session_filter > 0) {
    $where .= " AND courses.session_id = $session_filter";
}

$total_query = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM courses
     $where"
);
$total_courses = 0;

if ($total_query && mysqli_num_rows($total_query) > 0) {
    $total_courses = (int) mysqli_fetch_assoc($total_query)['total'];
}

$total_pages = max(1, (int) ceil($total_courses / $limit));

if ($page > $total_pages) {
    $page = $total_pages;
    $start = ($page - 1) * $limit;
}

$query = mysqli_query(
    $conn,
    "SELECT
        courses.id,
        courses.course_code,
        courses.course_title,
        courses.archived_at,
        courses.created_at,
        sessions.id AS session_id,
        sessions.session_name
     FROM courses
     INNER JOIN sessions
        ON courses.session_id = sessions.id
     $where
     ORDER BY courses.id DESC
     LIMIT $limit OFFSET $start"
);

/* Get all sessions for the filter dropdown */
$sessions_query = mysqli_query(
    $conn,
    "SELECT * FROM sessions ORDER BY session_name DESC"
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

    <title>
        Courses - Student Record Management System
    </title>

    <link
        rel="stylesheet"
        href="assets/css/admin.css"
    >

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

    <link
        rel="stylesheet"
        href="assets/css/courses.css"
    >

</head>


<body>


<div class="admin-layout">


    <!-- =====================================================
         SIDEBAR
    ====================================================== -->

    <aside class="admin-sidebar">


        <!-- BRAND -->

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


        <!-- NAVIGATION -->
        <nav class="admin-navigation">


            <div class="navigation-title">
                MAIN MENU
            </div>


            <ul>


                <!-- DASHBOARD -->

                <li>

                    <a
                        href="dashboard"
                        class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>"
                    >

                        <span class="nav-icon">

                            <svg
                                viewBox="0 0 24 24"
                                width="18"
                                height="18"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >

                                <rect
                                    x="3"
                                    y="3"
                                    width="7"
                                    height="7"
                                    rx="1"
                                />

                                <rect
                                    x="14"
                                    y="3"
                                    width="7"
                                    height="7"
                                    rx="1"
                                />

                                <rect
                                    x="3"
                                    y="14"
                                    width="7"
                                    height="7"
                                    rx="1"
                                />

                                <rect
                                    x="14"
                                    y="14"
                                    width="7"
                                    height="7"
                                    rx="1"
                                />

                            </svg>

                        </span>


                        <span>
                            Dashboard
                        </span>

                    </a>

                </li>



                <!-- ADD STUDENT -->

                <li>

                    <a
                        href="add_student"
                        class="<?php echo ($current_page == 'add_student.php') ? 'active' : ''; ?>"
                    >

                        <span class="nav-icon">

                            <svg
                                viewBox="0 0 24 24"
                                width="18"
                                height="18"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >

                                <circle
                                    cx="9"
                                    cy="7"
                                    r="4"
                                />

                                <path
                                    d="M3 21v-2a6 6 0 0 1 12 0v2"
                                />

                                <path
                                    d="M15 3h6"
                                />

                                <path
                                    d="M18 0v6"
                                />

                            </svg>

                        </span>


                        <span>
                            Add Student
                        </span>

                    </a>

                </li>



                <!-- VIEW STUDENTS -->

                <li>

                    <a
                        href="view_students"
                        class="<?php echo ($current_page == 'view_students.php' || $current_page == 'student_profile.php' || $current_page == 'edit_student.php') ? 'active' : ''; ?>"
                    >

                        <span class="nav-icon">

                            <svg
                                viewBox="0 0 24 24"
                                width="18"
                                height="18"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >

                                <circle
                                    cx="9"
                                    cy="7"
                                    r="4"
                                />

                                <path
                                    d="M3 21v-2a6 6 0 0 1 12 0v2"
                                />

                                <circle
                                    cx="17"
                                    cy="9"
                                    r="3"
                                />

                                <path
                                    d="M21 21v-2a4 4 0 0 0-7.5-1.5"
                                />

                            </svg>

                        </span>


                        <span>
                            View Students
                        </span>

                    </a>

                </li>



                <!-- ARCHIVE -->

                <li>

                    <a
                        href="archive_students"
                        class="<?php echo ($current_page == 'archive_students.php') ? 'active' : ''; ?>"
                    >

                        <span class="nav-icon">

                            <svg
                                viewBox="0 0 24 24"
                                width="18"
                                height="18"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >

                                <rect
                                    x="2"
                                    y="3"
                                    width="20"
                                    height="5"
                                    rx="1"
                                />

                                <path
                                    d="M4 8v12a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V8"
                                />

                                <path
                                    d="M10 12h4"
                                />

                            </svg>

                        </span>


                        <span>
                            Archive
                        </span>

                    </a>

                </li>



                <!-- RESULTS -->

                <li>

                    <a
                        href="result"
                        class="<?php echo ($current_page == 'result.php') ? 'active' : ''; ?>"
                    >

                        <span class="nav-icon">

                            <svg
                                viewBox="0 0 24 24"
                                width="18"
                                height="18"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >

                                <path
                                    d="M4 4h16v16H4z"
                                />

                                <path
                                    d="M8 16v-4"
                                />

                                <path
                                    d="M12 16V8"
                                />

                                <path
                                    d="M16 16v-7"
                                />

                            </svg>

                        </span>


                        <span>
                            Results
                        </span>

                    </a>

                </li>



                <!-- COURSES -->

                <li>

                    <a
                        href="courses"
                        class="<?php echo ($current_page == 'courses.php' || $current_page == 'add_course.php' || $current_page == 'edit_course.php') ? 'active' : ''; ?>"
                    >

                        <span class="nav-icon">

                            <svg
                                viewBox="0 0 24 24"
                                width="18"
                                height="18"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >

                                <path
                                    d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"
                                />

                                <path
                                    d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"
                                />

                            </svg>

                        </span>


                        <span>
                            Courses
                        </span>

                    </a>

                </li>



                <!-- SESSIONS -->

                <li>

                    <a
                        href="sessions"
                        class="<?php echo ($current_page == 'sessions.php') ? 'active' : ''; ?>"
                    >

                        <span class="nav-icon">

                            <svg
                                viewBox="0 0 24 24"
                                width="18"
                                height="18"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >

                                <circle
                                    cx="12"
                                    cy="12"
                                    r="10"
                                />

                                <polyline
                                    points="12 6 12 12 16 14"
                                />

                            </svg>

                        </span>


                        <span>
                            Sessions
                        </span>

                    </a>

                </li>



                <!-- SEARCH -->

                <li>

                    <a
                        href="search_student"
                        class="<?php echo ($current_page == 'search_student.php') ? 'active' : ''; ?>"
                    >

                        <span class="nav-icon">

                            <svg
                                viewBox="0 0 24 24"
                                width="18"
                                height="18"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >

                                <circle
                                    cx="11"
                                    cy="11"
                                    r="7"
                                />

                                <path
                                    d="m20 20-4-4"
                                />

                            </svg>

                        </span>


                        <span>
                            Search Student
                        </span>

                    </a>

                </li>


            </ul>



            <!-- ACCOUNT -->

            <div class="navigation-title navigation-bottom-title">
                ACCOUNT
            </div>


            <ul>

                <li>

                    <a
                        href="logout"
                        class="logout-link"
                    >

                        <span class="nav-icon">

                            <svg
                                viewBox="0 0 24 24"
                                width="18"
                                height="18"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >

                                <path
                                    d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"
                                />

                                <polyline
                                    points="16 17 21 12 16 7"
                                />

                                <line
                                    x1="21"
                                    y1="12"
                                    x2="9"
                                    y2="12"
                                />

                            </svg>

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



    <!-- =====================================================
         MAIN
    ====================================================== -->

    <main class="admin-main">


        <!-- TOPBAR -->

        <header class="admin-topbar">

            <div>

                <p class="topbar-label">
                    ACADEMIC RECORDS
                </p>

                <h1>
                    Courses
                </h1>

            </div>


            <div class="admin-profile">

                <div class="profile-avatar">

                    <?php
                    echo strtoupper(
                        substr($admin_name, 0, 1)
                    );
                    ?>

                </div>


                <div class="profile-details">

                    <strong>
                        <?php
                        echo htmlspecialchars($admin_name);
                        ?>
                    </strong>

                    <span>
                        Administrator
                    </span>

                </div>

            </div>

        </header>



        <!-- PAGE CONTENT -->

        <section class="courses-page">


            <!-- PAGE TOP -->

            <div class="courses-page-header">

                <div>

                    <span class="welcome-label">
                        COURSE DIRECTORY
                    </span>

                    <h2>
                        Registered Courses
                    </h2>

                    <p>
                        Manage course offerings across all academic sessions.
                    </p>

                </div>


                <a
                    href="add_course"
                    class="courses-add-btn"
                >

                    <span class="courses-add-btn-plus">
                        +
                    </span>

                    Add Course

                </a>


                <?php if ($view === 'archived'): ?>

                    <a
                        href="courses"
                        class="courses-view-toggle-btn"
                    >
                        View Active Courses
                    </a>

                <?php else: ?>

                    <a
                        href="courses?view=archived"
                        class="courses-view-toggle-btn"
                    >
                        View Archived
                    </a>

                <?php endif; ?>

            </div>



            <!-- FILTER BAR -->

            <div class="courses-filter-bar">

                <form
                    method="get"
                    action="courses"
                    class="courses-filter-form"
                >

                    <div class="courses-search-box">

                        <svg
                            viewBox="0 0 24 24"
                            width="17"
                            height="17"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >

                            <circle
                                cx="11"
                                cy="11"
                                r="7"
                            />

                            <path
                                d="m20 20-4-4"
                            />

                        </svg>

                        <input
                            type="text"
                            name="search"
                            value="<?php echo htmlspecialchars($search); ?>"
                            placeholder="Search by course code or title..."
                        >

                    </div>


                    <select
                        name="session"
                        class="courses-session-filter"
                    >

                        <option value="0">
                            All Sessions
                        </option>

                        <?php
                        while (
                            $session_row =
                            mysqli_fetch_assoc($sessions_query)
                        ) {
                        ?>

                            <option
                                value="<?php echo (int)$session_row['id']; ?>"
                                <?php echo ($session_filter == (int)$session_row['id']) ? 'selected' : ''; ?>
                            >
                                <?php
                                echo htmlspecialchars(
                                    $session_row['session_name']
                                );
                                ?>
                            </option>

                        <?php
                        }
                        ?>

                    </select>


                    <button
                        type="submit"
                        class="courses-filter-btn"
                    >
                        Filter
                    </button>


                    <?php if ($view === 'archived'): ?>

                        <input
                            type="hidden"
                            name="view"
                            value="archived"
                        >

                    <?php endif; ?>


                    <?php if ($search != '' || $session_filter > 0): ?>

                        <a
                            href="<?php echo ($view === 'archived') ? 'courses?view=archived' : 'courses'; ?>"
                            class="courses-clear-btn"
                        >
                            Clear
                        </a>

                    <?php endif; ?>

                </form>

            </div>



            <!-- COURSES CARD -->

            <div class="courses-table-card">


                <div class="courses-card-header">

                    <div>

                        <h3>
                            <?php echo ($view === 'archived') ? 'Archived Courses' : 'All Courses'; ?>
                        </h3>

                        <p>
                            <?php echo ($view === 'archived') ? 'Previously archived course records.' : 'Active course records in the system.'; ?>
                        </p>

                    </div>


                    <div class="courses-table-count">

                        <strong>
                            <?php echo number_format($total_courses); ?>
                        </strong>

                        <span>
                            course<?php echo $total_courses == 1 ? '' : 's'; ?>
                        </span>

                    </div>

                </div>



                <?php if ($query && mysqli_num_rows($query) > 0): ?>


                    <div class="courses-table-wrapper">

                        <table class="courses-table">

                            <thead>

                                <tr>

                                    <th>
                                        #
                                    </th>

                                    <th>
                                        Course Code
                                    </th>

                                    <th>
                                        Course Title
                                    </th>

                                    <th>
                                        Session
                                    </th>

                                    <th>
                                        <?php echo ($view === 'archived') ? 'Date Archived' : 'Date Added'; ?>
                                    </th>

                                    <th>
                                        Actions
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                            <?php

                            $sn = $start + 1;
                            $count = 0;

                            while (
                                $row = mysqli_fetch_assoc($query)
                            ):

                                $course_result_count = 0;

                                $count_query = mysqli_query(
                                    $conn,
                                    "SELECT COUNT(*) AS total
                                     FROM results
                                     WHERE course_id = " . (int)$row['id']
                                );

                                if (
                                    $count_query &&
                                    mysqli_num_rows($count_query) > 0
                                ) {
                                    $course_result_count = (int) mysqli_fetch_assoc($count_query)['total'];
                                }

                            ?>

                                <tr>


                                    <!-- NUMBER -->

                                    <td class="courses-serial">

                                        <?php echo $sn++; ?>

                                    </td>



                                    <!-- COURSE CODE -->

                                    <td>

                                        <span class="courses-code-badge">

                                            <?php
                                            echo htmlspecialchars(
                                                $row['course_code']
                                            );
                                            ?>

                                        </span>

                                    </td>



                                    <!-- COURSE TITLE -->

                                    <td>

                                        <div class="courses-title-cell">

                                            <strong>
                                                <?php
                                                echo htmlspecialchars(
                                                    $row['course_title']
                                                );
                                                ?>
                                            </strong>

                                            <?php if ($course_result_count > 0): ?>

                                                <span>
                                                    <?php
                                                    echo number_format($course_result_count);
                                                    ?> result<?php echo $course_result_count == 1 ? '' : 's'; ?>
                                                </span>

                                            <?php endif; ?>

                                        </div>

                                    </td>



                                    <!-- SESSION -->

                                    <td>

                                        <span class="courses-session-badge">

                                            <?php
                                            echo htmlspecialchars(
                                                $row['session_name']
                                            );
                                            ?>

                                        </span>

                                    </td>



                                    <!-- DATE ADDED / DATE ARCHIVED -->

                                    <td>

                                        <span class="courses-date-text">

                                            <?php
                                            if ($view === 'archived') {
                                                echo $row['archived_at']
                                                    ? date("M j, Y", strtotime($row['archived_at']))
                                                    : '-';
                                            } else {
                                                echo date(
                                                    "M j, Y",
                                                    strtotime($row['created_at'])
                                                );
                                            }
                                            ?>

                                        </span>

                                    </td>



                                    <!-- ACTIONS -->

                                    <td>

                                        <div class="courses-actions">

                                            <?php if ($view === 'archived'): ?>

                                                <a
                                                    href="restore_course?id=<?php echo (int)$row['id']; ?>"
                                                    class="courses-action courses-restore-action"
                                                    title="Restore Course"
                                                >
                                                    Restore
                                                </a>

                                            <?php else: ?>

                                                <a
                                                    href="edit_course?id=<?php echo (int)$row['id']; ?>"
                                                    class="courses-action courses-edit-action"
                                                    title="Edit Course"
                                                >
                                                    Edit
                                                </a>

                                                <a
                                                    href="archive_course?id=<?php echo (int)$row['id']; ?>"
                                                    class="courses-action courses-archive-action"
                                                    title="Archive Course"
                                                >
                                                    Archive
                                                </a>

                                            <?php endif; ?>

                                        </div>

                                    </td>


                                </tr>

                            <?php endwhile; ?>

                            </tbody>

                        </table>

                    </div>



                    <!-- PAGINATION -->

                    <?php if ($total_pages > 1): ?>

                        <div class="courses-pagination-area">

                            <div class="courses-pagination-info">

                                Showing

                                <strong>
                                    <?php echo $start + 1; ?>
                                </strong>

                                to

                                <strong>
                                    <?php echo min($start + $limit, $total_courses); ?>
                                </strong>

                                of

                                <strong>
                                    <?php echo $total_courses; ?>
                                </strong>

                            </div>


                            <div class="courses-pagination">

                                <?php if ($page > 1): ?>

                                    <a
                                        href="?page=<?php echo $page - 1; ?>&amp;search=<?php echo urlencode($search); ?>&amp;session=<?php echo $session_filter; ?><?php echo ($view === 'archived') ? '&amp;view=archived' : ''; ?>"
                                        class="courses-page-arrow"
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
                                            href="?page=<?php echo $i; ?>&amp;search=<?php echo urlencode($search); ?>&amp;session=<?php echo $session_filter; ?><?php echo ($view === 'archived') ? '&amp;view=archived' : ''; ?>"
                                        >
                                            <?php echo $i; ?>
                                        </a>

                                    <?php endif; ?>

                                <?php endfor; ?>


                                <?php if ($page < $total_pages): ?>

                                    <a
                                        href="?page=<?php echo $page + 1; ?>&amp;search=<?php echo urlencode($search); ?>&amp;session=<?php echo $session_filter; ?><?php echo ($view === 'archived') ? '&amp;view=archived' : ''; ?>"
                                        class="courses-page-arrow"
                                    >
                                        Next
                                    </a>

                                <?php endif; ?>

                            </div>

                        </div>

                    <?php endif; ?>


                <?php else: ?>


                    <!-- EMPTY STATE -->

                    <div class="courses-empty">

                        <div class="courses-empty-icon">
                            C
                        </div>

                        <h3>
                            No courses found
                        </h3>

                        <p>

                            <?php if ($view === 'archived'): ?>

                                There are no archived courses at the moment.

                            <?php elseif ($search != '' || $session_filter > 0): ?>

                                No courses match your current filters.

                            <?php else: ?>

                                There are currently no courses registered.

                            <?php endif; ?>

                        </p>


                        <?php if ($view === 'archived'): ?>

                            <a
                                href="courses"
                                class="courses-empty-btn"
                            >
                                View Active Courses
                            </a>

                        <?php else: ?>

                            <a
                                href="add_course"
                                class="courses-empty-btn"
                            >
                                Add First Course
                            </a>

                        <?php endif; ?>

                    </div>


                <?php endif; ?>


            </div>


        </section>



        <!-- FOOTER -->

        <footer class="admin-footer">

            <span>
                2026 Student Record Management System
            </span>

            <span>
                |
            </span>

            <span>
                Designed by Sekinat Mutolib
            </span>

        </footer>


    </main>


</div>


<script src="assets/js/sidebar.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.querySelectorAll('.courses-archive-action').forEach(function (link) {
        link.addEventListener('click', function (event) {
            event.preventDefault();

            var href = this.getAttribute('href');

            Swal.fire({
                title: 'Archive Course?',
                text: 'Are you sure you want to archive this course? It will be hidden from the active list.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#006400',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, archive it',
                cancelButtonText: 'Cancel'
            }).then(function (result) {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            });
        });
    });

    document.querySelectorAll('.courses-restore-action').forEach(function (link) {
        link.addEventListener('click', function (event) {
            event.preventDefault();

            var href = this.getAttribute('href');

            Swal.fire({
                title: 'Restore Course?',
                text: 'Are you sure you want to restore this course? It will return to the active list.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#006400',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, restore it',
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