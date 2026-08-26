<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
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
   GET ALL RESULTS
========================================================= */

$results_query = mysqli_query(
    $conn,
    "SELECT
        results.id,
        results.student_id,
        results.course_code,
        results.course_title,
        results.semester,
        results.session,
        results.score,
        results.grade,
        results.remark,
        students.firstname,
        students.lastname,
        students.matric_no
     FROM results
     INNER JOIN students
        ON results.student_id = students.id
     ORDER BY results.id DESC"
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
        Results - Student Record Management System
    </title>

    <link
        rel="stylesheet"
        href="assets/css/admin.css"
    >

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

            <div class="brand-mark">

                <svg
                    viewBox="0 0 24 24"
                    width="24"
                    height="24"
                    fill="none"
                    stroke="white"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >

                    <path
                        d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"
                    />

                    <path
                        d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"
                    />

                </svg>

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



        <!-- NAVIGATION -->

        <nav class="admin-navigation">


            <div class="navigation-title">
                MAIN MENU
            </div>


            <ul>


                <!-- DASHBOARD -->

                <li>

                    <a
                        href="dashboard.php"
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
                        href="add_student.php"
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
                        href="view_students.php"
                        class="<?php echo ($current_page == 'view_students.php' || $current_page == 'student_profile.php') ? 'active' : ''; ?>"
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



                <!-- RESULTS -->

                <li>

                    <a
                        href="results.php"
                        class="<?php echo ($current_page == 'results.php') ? 'active' : ''; ?>"
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



                <!-- SEARCH -->

                <li>

                    <a
                        href="search_student.php"
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
                        href="logout.php"
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
                    Results
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

        <section class="results-page">


            <div class="results-page-header">

                <div>

                    <span class="welcome-label">
                        ACADEMIC PERFORMANCE
                    </span>

                    <h2>
                        Student Results
                    </h2>

                    <p>
                        View and manage academic results for all students.
                    </p>

                </div>

            </div>



            <!-- RESULTS TABLE -->

            <div class="results-table-card">


                <div class="results-table-wrapper">


                    <table class="results-table">


                        <thead>

                            <tr>

                                <th>
                                    Student
                                </th>

                                <th>
                                    Matric No
                                </th>

                                <th>
                                    Course Code
                                </th>

                                <th>
                                    Course Title
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

                                <th>
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody>


                        <?php

                        if (
                            $results_query &&
                            mysqli_num_rows($results_query) > 0
                        ) {

                            while (
                                $result =
                                mysqli_fetch_assoc($results_query)
                            ) {

                        ?>


                            <tr>


                                <!-- STUDENT -->

                                <td>

                                    <strong>

                                        <?php

                                        echo htmlspecialchars(
                                            $result['firstname']
                                            . " "
                                            . $result['lastname']
                                        );

                                        ?>

                                    </strong>

                                </td>



                                <!-- MATRIC -->

                                <td>

                                    <?php

                                    echo htmlspecialchars(
                                        $result['matric_no']
                                    );

                                    ?>

                                </td>



                                <!-- COURSE CODE -->

                                <td>

                                    <span class="course-code">

                                        <?php

                                        echo htmlspecialchars(
                                            $result['course_code']
                                        );

                                        ?>

                                    </span>

                                </td>



                                <!-- COURSE TITLE -->

                                <td>

                                    <?php

                                    echo htmlspecialchars(
                                        $result['course_title']
                                    );

                                    ?>

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

                                    <strong>

                                        <?php

                                        echo htmlspecialchars(
                                            $result['score']
                                        );

                                        ?>

                                    </strong>

                                </td>



                                <!-- GRADE -->

                                <td>

                                    <span
                                        class="result-grade grade-<?php echo strtolower($result['grade']); ?>"
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

                                    <?php

                                    echo htmlspecialchars(
                                        $result['remark']
                                    );

                                    ?>

                                </td>



                                <!-- ACTION -->

                                <td>

                                    <a
                                        href="student_profile.php?id=<?php echo (int)$result['student_id']; ?>"
                                        class="result-view-btn"
                                    >
                                        View Student
                                    </a>

                                </td>


                            </tr>


                        <?php

                            }

                        } else {

                        ?>


                            <tr>

                                <td
                                    colspan="10"
                                    class="empty-results"
                                >

                                    No student results have been added yet.

                                </td>

                            </tr>


                        <?php

                        }

                        ?>


                        </tbody>


                    </table>


                </div>


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


</body>

</html>