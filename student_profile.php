<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login");
    exit();
}

include "config/database.php";


/* =========================================================
   CHECK STUDENT ID
========================================================= */

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: view_students");
    exit();
}

$id = (int) $_GET['id'];


/* =========================================================
   GET STUDENT
========================================================= */

$query = mysqli_query(
    $conn,
    "SELECT * FROM students
     WHERE id = $id
     AND archived_at IS NULL"
);

if (!$query || mysqli_num_rows($query) == 0) {
    header("Location: view_students");
    exit();
}

$student = mysqli_fetch_assoc($query);


/* =========================================================
   GET STUDENT RESULTS
========================================================= */

$results_query = mysqli_query(
    $conn,
    "SELECT
        id,
        course_code,
        course_title,
        semester,
        session,
        score,
        grade,
        remark
     FROM results
     WHERE student_id = $id
     ORDER BY id DESC"
);


/* =========================================================
   PASSPORT
========================================================= */

if (!empty($student['passport'])) {

    $passport =
        "assets/upload/students/" .
        $student['passport'];

} else {

    $passport =
        "assets/image/default.png";
}


/* =========================================================
   ADMIN NAME
========================================================= */

$admin_name = "Administrator";

if (
    isset($_SESSION['admin_name']) &&
    !empty($_SESSION['admin_name'])
) {

    $admin_name =
        $_SESSION['admin_name'];
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

        <?php

        echo htmlspecialchars(
            $student['firstname']
            . " "
            . $student['lastname']
        );

        ?>

        - Student Profile

    </title>


    <!-- ADMIN CSS -->

    <link
        rel="stylesheet"
        href="assets/css/admin.css"
    >


    <!-- MAIN STYLE -->

    <link
        rel="stylesheet"
        href="assets/css/style.css"
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

                    <a href="dashboard">

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

                    <a href="add_student">

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

                    <a href="archive_students">

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

                    <a href="result">

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

                    <a href="courses">

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


                <li>

                    <a href="sessions">

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

                    <a href="search_student">

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



        <!-- SIDEBAR FOOTER -->

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
         MAIN CONTENT
    ====================================================== -->

    <main class="admin-main">


        <!-- TOPBAR -->

        <header class="admin-topbar">

            <div>

                <p class="topbar-label">
                    STUDENT RECORDS
                </p>

                <h1>
                    Student Profile
                </h1>

            </div>



            <!-- ADMIN -->

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

                        echo htmlspecialchars(
                            $admin_name
                        );
                        ?>
                    </strong>
                    <span>Administrator</span>
                </div>
            </div>
        </header>

        <!--PAGE CONTENT-->
        <section class="student-profile-page">
            <!-- PAGE INTRO -->
            <div class="student-profile-page-header">
                <div>
                    <span class="student-profile-eyebrow"> STUDENT RECORD </span>
                    <h2>Student Profile </h2>
                    <p> View student information and academic performance.</p>
                </div>
                <!-- ACTIONS -->
                <div class="student-profile-header-actions">
                    <a
                        href="edit_student?id=<?php echo (int)$student['id']; ?>"
                        class="profile-header-btn profile-edit-btn"
                    >

                        <svg
                            viewBox="0 0 24 24"
                            width="16"
                            height="16"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >

                            <path
                                d="M12 20h9"
                            />

                            <path
                                d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z"
                            />

                        </svg>
                        Edit Student
                    </a>


                    <button
                        type="button"
                        onclick="window.print()"
                        class="profile-header-btn profile-print-btn"
                    >

                        <svg
                            viewBox="0 0 24 24"
                            width="16"
                            height="16"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >

                            <polyline
                                points="6 9 6 2 18 2 18 9"
                            />

                            <path
                                d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"
                            />

                            <rect
                                x="6"
                                y="14"
                                width="12"
                                height="8"
                            />

                        </svg>
                        Print Profile
                    </button>
                </div>
            </div>

            <!-- COMPACT STUDENT INFORMATION CARD -->
            <section class="compact-profile-card">
                <!-- LEFT: PASSPORT -->
                <div class="compact-profile-passport">
                    <div class="passport-frame">
                        <img
                            src="<?php echo htmlspecialchars($passport); ?>"
                            alt="Student Passport"
                            onerror="this.onerror=null;this.src='assets/image/default.png';"
                        >
                    </div>
                    <div class="passport-status">
                        <span class="passport-status-dot"></span>
                        <?php
                        if (!empty($student['passport'])) {
                            echo "Passport Uploaded";
                        } else {
                            echo "No Passport";
                        }
                        ?>
                    </div>
                    <div class="passport-student-name">
                        <h3>
                            <?php

                            echo htmlspecialchars(
                                $student['firstname']
                                . " "
                                . $student['lastname']
                            );
                            ?>
                        </h3>
                        <p>
                            <?php
                            echo htmlspecialchars(
                                $student['matric_no']
                            );
                            ?>
                        </p>
                    </div>
                </div>
                <!-- RIGHT: DETAILS -->
                <div class="compact-profile-details">
                    <div class="compact-details-heading">
                        <div class="details-heading-icon">
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
                                    cy="8"
                                    r="4"
                                />

                                <path
                                    d="M4 21a8 8 0 0 1 16 0"
                                />
                            </svg>
                        </div>
                        <div>
                            <span> STUDENT INFORMATION</span>
                            <h3> Personal Details </h3>
                        </div>
                    </div>
                    <div class="compact-details-grid">
                        <!-- MATRIC -->
                        <div class="compact-detail">
                            <span> Matric Number </span>
                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $student['matric_no']
                                );
                                ?>
                            </strong>
                        </div>

                        <!-- FULL NAME -->

                        <div class="compact-detail">

                            <span>
                                Full Name
                            </span>

                            <strong>

                                <?php

                                echo htmlspecialchars(
                                    $student['firstname']
                                    . " "
                                    . $student['lastname']
                                );

                                ?>

                            </strong>

                        </div>



                        <!-- GENDER -->

                        <div class="compact-detail">

                            <span>
                                Gender
                            </span>

                            <strong>

                                <?php

                                echo htmlspecialchars(
                                    $student['gender']
                                );

                                ?>

                            </strong>

                        </div>



                        <!-- DEPARTMENT -->

                        <div class="compact-detail">

                            <span>
                                Department
                            </span>

                            <strong>

                                <?php

                                echo htmlspecialchars(
                                    $student['department']
                                );

                                ?>

                            </strong>

                        </div>



                        <!-- LEVEL -->

                        <div class="compact-detail">

                            <span>
                                Level
                            </span>

                            <strong>

                                <?php

                                echo htmlspecialchars(
                                    $student['level']
                                );

                                ?>

                            </strong>

                        </div>



                        <!-- ADMISSION YEAR -->

                        <div class="compact-detail">

                            <span>
                                Admission Year
                            </span>

                            <strong>

                                <?php

                                if (
                                    ($student['admission_year'] ?? null) !== null
                                    && $student['admission_year'] !== ''
                                ) {

                                    echo (int) $student['admission_year'];

                                } else {

                                    echo "N/A";

                                }

                                ?>

                            </strong>

                        </div>



                        <!-- PHONE -->

                        <div class="compact-detail">

                            <span>
                                Phone Number
                            </span>

                            <strong>

                                <?php

                                echo htmlspecialchars(
                                    $student['phone']
                                );

                                ?>

                            </strong>

                        </div>



                        <!-- EMAIL -->

                        <div class="compact-detail">

                            <span>
                                Email Address
                            </span>

                            <strong class="email-value">

                                <?php

                                echo htmlspecialchars(
                                    $student['email']
                                );

                                ?>

                            </strong>

                        </div>



                        <!-- PASSPORT -->

                        <div class="compact-detail">

                            <span>
                                Passport
                            </span>

                            <strong>

                                <?php

                                if (!empty($student['passport'])) {

                                    echo "Uploaded";

                                } else {

                                    echo "Not Uploaded";

                                }

                                ?>

                            </strong>

                        </div>


                    </div>


                </div>


            </section>



            <!-- =====================================================
                 ACADEMIC RESULTS
            ====================================================== -->

            <section
                class="compact-results-section"
                id="results"
            >


                <!-- RESULTS HEADER -->

                <div class="compact-results-header">


                    <div class="compact-results-title">


                        <div class="results-heading-icon">

                            <svg
                                viewBox="0 0 24 24"
                                width="20"
                                height="20"
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

                        </div>


                        <div>

                            <span>
                                ACADEMIC PERFORMANCE
                            </span>

                            <h2>
                                Student Results
                            </h2>

                            <p>
                                Academic results recorded for this student.
                            </p>

                        </div>


                    </div>



                    <a
                        href="add_result?student_id=<?php echo (int)$student['id']; ?>"
                        class="compact-add-result-btn"
                    >

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

                            <line
                                x1="12"
                                y1="5"
                                x2="12"
                                y2="19"
                            />

                            <line
                                x1="5"
                                y1="12"
                                x2="19"
                                y2="12"
                            />

                        </svg>

                        Add Result

                    </a>


                </div>



                <!-- RESULTS TABLE -->

                <div class="compact-results-card">


                    <?php

                    if (
                        $results_query &&
                        mysqli_num_rows($results_query) > 0
                    ) {

                    ?>


                        <div class="compact-results-table-wrapper">


                            <table class="compact-results-table">


                                <thead>

                                    <tr>

                                        <th>
                                            #
                                        </th>

                                        <th>
                                            Course
                                        </th>

                                        <th>
                                            Semester
                                        </th>

                                        <th>
                                            Session
                                        </th>

                                        <th>
                                            Score
                                        </th>

                                        <th>
                                            Grade
                                        </th>

                                        <th>
                                            Remark
                                        </th>

                                    </tr>

                                </thead>


                                <tbody>


                                <?php

                                $result_number = 1;

                                while (
                                    $result =
                                    mysqli_fetch_assoc(
                                        $results_query
                                    )
                                ) {

                                ?>


                                    <tr>


                                        <!-- NUMBER -->

                                        <td>

                                            <span class="result-number">

                                                <?php

                                                echo $result_number;

                                                ?>

                                            </span>

                                        </td>



                                        <!-- COURSE -->

                                        <td>

                                            <div class="result-course">

                                                <strong>

                                                    <?php

                                                    echo htmlspecialchars(
                                                        $result['course_code']
                                                    );

                                                    ?>

                                                </strong>


                                                <span>

                                                    <?php

                                                    echo htmlspecialchars(
                                                        $result['course_title']
                                                    );

                                                    ?>

                                                </span>

                                            </div>

                                        </td>



                                        <!-- SEMESTER -->

                                        <td>

                                            <?php

                                            echo htmlspecialchars(
                                                $result['semester']
                                            );

                                            ?>

                                        </td>



                                        <!-- SESSION -->

                                        <td>

                                            <?php

                                            echo htmlspecialchars(
                                                $result['session']
                                            );

                                            ?>

                                        </td>



                                        <!-- SCORE -->

                                        <td>

                                            <strong class="result-score">

                                                <?php

                                                echo htmlspecialchars(
                                                    $result['score']
                                                );

                                                ?>

                                            </strong>

                                            <span class="score-total">
                                                / 100
                                            </span>

                                        </td>



                                        <!-- GRADE -->

                                        <td>

                                            <span
                                                class="result-grade-badge grade-<?php echo strtolower(htmlspecialchars($result['grade'])); ?>"
                                            >

                                                <?php

                                                echo htmlspecialchars(
                                                    $result['grade']
                                                );

                                                ?>

                                            </span>

                                        </td>



                                        <!-- REMARK -->

                                        <td>

                                            <span class="result-remark">

                                                <?php

                                                echo htmlspecialchars(
                                                    $result['remark']
                                                );

                                                ?>

                                            </span>

                                        </td>


                                    </tr>


                                <?php

                                    $result_number++;

                                }

                                ?>


                                </tbody>


                            </table>


                        </div>


                    <?php

                    } else {

                    ?>


                        <div class="compact-no-results">


                            <div class="no-results-icon">

                                <svg
                                    viewBox="0 0 24 24"
                                    width="30"
                                    height="30"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.7"
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

                            </div>


                            <h3>
                                No Results Yet
                            </h3>


                            <p>
                                No academic results have been recorded for this student.
                            </p>


                            <a
                                href="add_result?student_id=<?php echo (int)$student['id']; ?>"
                                class="compact-add-result-btn"
                            >

                                Add First Result

                            </a>


                        </div>


                    <?php

                    }

                    ?>


                </div>


            </section>



            <!-- =====================================================
                 BOTTOM ACTIONS
            ====================================================== -->

            <div class="student-profile-bottom-actions">


                <a
                    href="view_students"
                    class="profile-bottom-btn back-btn-new"
                >

                    <svg
                        viewBox="0 0 24 24"
                        width="16"
                        height="16"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >

                        <line
                            x1="19"
                            y1="12"
                            x2="5"
                            y2="12"
                        />

                        <polyline
                            points="12 19 5 12 12 5"
                        />

                    </svg>

                    Back to Students

                </a>


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

</body>

</html>