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
   CHECK COURSE ID
========================================================= */

if (
    !isset($_GET['id']) ||
    empty($_GET['id'])
) {
    header("Location: courses");
    exit();
}

$course_id = (int) $_GET['id'];


/* =========================================================
   GET COURSE
========================================================= */

$course_query = mysqli_query(
    $conn,
    "SELECT
        courses.id,
        courses.course_code,
        courses.course_title,
        courses.session_id
     FROM courses
     WHERE courses.id = $course_id
       AND courses.archived_at IS NULL"
);


if (
    !$course_query ||
    mysqli_num_rows($course_query) == 0
) {
    header("Location: courses");
    exit();
}


$course = mysqli_fetch_assoc($course_query);


/* =========================================================
   GET ALL SESSIONS FOR THE DROPDOWN
========================================================= */

$sessions_query = mysqli_query(
    $conn,
    "SELECT * FROM sessions ORDER BY session_name DESC"
);


/* =========================================================
   FORM VARIABLES
========================================================= */

$success = "";
$error = "";

$course_code = $course['course_code'];
$course_title = $course['course_title'];
$session_id = (int) $course['session_id'];


/* =========================================================
   PROCESS FORM
========================================================= */

if (isset($_POST['update_course'])) {

    $course_code = trim($_POST['course_code'] ?? "");
    $course_title = trim($_POST['course_title'] ?? "");
    $session_id = (int) ($_POST['session_id'] ?? 0);

    /* Normalize course code to uppercase */
    $course_code = strtoupper($course_code);


    /* =====================================================
       VALIDATION
    ===================================================== */

    if ($course_code == "" || $course_title == "") {

        $error = "Course code and course title are required.";

    } elseif ($session_id <= 0) {

        $error = "Please select a valid academic session.";

    } elseif (!preg_match('/^[A-Z\/0-9\s.\-]{2,20}$/', $course_code)) {

        $error = "Course code must be 2-20 characters using only letters, numbers, dash or slash.";

    } else {

        /* Verify session exists */
        $session_check = mysqli_query(
            $conn,
            "SELECT id FROM sessions WHERE id = $session_id"
        );

        if (!$session_check || mysqli_num_rows($session_check) == 0) {

            $error = "Selected academic session does not exist.";

        } else {

            /* Duplicate check excluding current course */
            $code_escaped = $conn->real_escape_string($course_code);
            $title_escaped = $conn->real_escape_string($course_title);

            $dup_check = mysqli_query(
                $conn,
                "SELECT id FROM courses
                 WHERE course_code = '$code_escaped'
                   AND session_id = $session_id
                   AND id != $course_id
                   AND archived_at IS NULL"
            );

            if ($dup_check && mysqli_num_rows($dup_check) > 0) {

                $error = "Another course with this code already exists for the selected session.";

            } else {

                $update = mysqli_query(
                    $conn,
                    "UPDATE courses
                     SET course_code = '$code_escaped',
                         course_title = '$title_escaped',
                         session_id = $session_id
                     WHERE id = $course_id"
                );

                if ($update) {

                    $success = "Course updated successfully.";

                } else {

                    $error = "Unable to update course: " . mysqli_error($conn);

                }
            }
        }
    }
}

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
        Edit Course - Student Record Management System
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
        href="assets/css/students.css"
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

                                <rect x="3" y="3" width="7" height="7" rx="1"/>

                                <rect x="14" y="3" width="7" height="7" rx="1"/>

                                <rect x="3" y="14" width="7" height="7" rx="1"/>

                                <rect x="14" y="14" width="7" height="7" rx="1"/>

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

                                <circle cx="9" cy="7" r="4"/>

                                <path d="M3 21v-2a6 6 0 0 1 12 0v2"/>

                                <path d="M15 3h6"/>

                                <path d="M18 0v6"/>

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

                                <circle cx="9" cy="7" r="4"/>

                                <path d="M3 21v-2a6 6 0 0 1 12 0v2"/>

                                <circle cx="17" cy="9" r="3"/>

                                <path d="M21 21v-2a4 4 0 0 0-7.5-1.5"/>

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

                                <rect x="2" y="3" width="20" height="5" rx="1"/>

                                <path d="M4 8v12a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V8"/>

                                <path d="M10 12h4"/>

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

                                <path d="M4 4h16v16H4z"/>

                                <path d="M8 16v-4"/>

                                <path d="M12 16V8"/>

                                <path d="M16 16v-7"/>

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
                        class="active"
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

                                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>

                                <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>

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
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
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

                                <circle cx="11" cy="11" r="7"/>

                                <path d="m20 20-4-4"/>

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

                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>

                                <polyline points="16 17 21 12 16 7"/>

                                <line x1="21" y1="12" x2="9" y2="12"/>

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
                    COURSE DIRECTORY
                </p>

                <h1>
                    Edit Course
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


        <div class="page-content">

            <div class="course-page">


                <!-- PAGE HEADER -->

                <div class="course-page-header">

                    <div>

                        <span class="welcome-label">
                            UPDATE COURSE
                        </span>

                        <h2>
                            Edit Course Details
                        </h2>

                        <p>
                            Update the course information below.
                        </p>

                    </div>

                </div>



                <!-- SUCCESS -->

                <?php if (!empty($success)) { ?>

                    <div class="student-message success">

                        <?php
                        echo htmlspecialchars($success);
                        ?>

                    </div>

                <?php } ?>



                <!-- ERROR -->

                <?php if (!empty($error)) { ?>

                    <div class="student-message error">

                        <?php
                        echo htmlspecialchars($error);
                        ?>

                    </div>

                <?php } ?>



                <!-- FORM CARD -->

                <div class="student-form-card course-form-card">

                    <div class="student-form-header">

                        <h2>
                            Course Information
                        </h2>

                        <p>
                            Update the course details below.
                        </p>

                    </div>


                    <form
                        method="POST"
                        class="student-form"
                    >


                        <div class="student-form-body">

                            <div class="form-section-title">
                                Course Details
                            </div>

                            <div class="student-form-grid">

                                <!-- COURSE CODE -->

                                <div class="form-group">

                                    <label>
                                        Course Code
                                        <span class="required">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        name="course_code"
                                        value="<?php echo htmlspecialchars($course_code); ?>"
                                        placeholder="e.g. COM301"
                                        maxlength="20"
                                        required
                                    >

                                    <small class="field-hint">
                                        e.g. COM301, STA111. Letters and numbers only.
                                    </small>

                                </div>


                                <!-- COURSE TITLE -->

                                <div class="form-group">

                                    <label>
                                        Course Title
                                        <span class="required">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        name="course_title"
                                        value="<?php echo htmlspecialchars($course_title); ?>"
                                        placeholder="e.g. Introduction to Software Engineering"
                                        maxlength="150"
                                        required
                                    >

                                </div>


                                <!-- SESSION -->

                                <div class="form-group">

                                    <label>
                                        Academic Session
                                        <span class="required">*</span>
                                    </label>

                                    <select
                                        name="session_id"
                                        required
                                    >

                                        <option value="">
                                            Select Session
                                        </option>

                                        <?php
                                        if (
                                            $sessions_query &&
                                            mysqli_num_rows($sessions_query) > 0
                                        ):
                                            while (
                                                $session_row =
                                                mysqli_fetch_assoc($sessions_query)
                                            ):
                                        ?>

                                            <option
                                                value="<?php echo (int)$session_row['id']; ?>"
                                                <?php echo ($session_id == (int)$session_row['id']) ? 'selected' : ''; ?>
                                            >
                                                <?php
                                                echo htmlspecialchars(
                                                    $session_row['session_name']
                                                );
                                                ?>
                                            </option>

                                        <?php
                                            endwhile;
                                        endif;
                                        ?>

                                    </select>

                                    <small class="field-hint">
                                        The session this course is offered in.
                                    </small>

                                </div>

                            </div>

                        </div>


                        <!-- ACTIONS -->

                        <div class="student-form-actions">

                            <a
                                href="courses"
                                class="cancel-student"
                            >
                                Cancel
                            </a>


                            <button
                                type="submit"
                                name="update_course"
                                class="add-student-button"
                            >
                                Update Course
                            </button>

                        </div>


                    </form>

                </div>


            </div>

        </div>


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

</body>

</html>