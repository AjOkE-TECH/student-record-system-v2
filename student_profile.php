<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include "config/database.php";


/* =========================================================
   CHECK STUDENT ID
========================================================= */

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: view_students.php");
    exit();
}

$id = (int) $_GET['id'];


/* =========================================================
   GET STUDENT
========================================================= */

$query = mysqli_query(
    $conn,
    "SELECT * FROM students WHERE id = $id"
);

if (!$query || mysqli_num_rows($query) == 0) {
    header("Location: view_students.php");
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
        remark,
        created_at
     FROM results
     WHERE student_id = $id
     ORDER BY session DESC, semester ASC, id DESC"
);


/* =========================================================
   PASSPORT
========================================================= */

if (!empty($student['passport'])) {
    $passport = "assets/upload/students/" . $student['passport'];
} else {
    $passport = "assets/image/default.png";
}


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
            $student['firstname'] . " " . $student['lastname']
        );
        ?>
        - Student Profile
    </title>

    <!-- MAIN ADMIN CSS -->

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

                    <a href="dashboard.php">

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

                    <a href="add_student.php">

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

                <li>

    <a href="results.php">

        <span class="nav-icon">

            <svg viewBox="0 0 24 24"
                 width="18"
                 height="18"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2"
                 stroke-linecap="round"
                 stroke-linejoin="round">

                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>

                <polyline points="14 2 14 8 20 8"/>

                <line x1="8" y1="13" x2="16" y2="13"/>

                <line x1="8" y1="17" x2="13" y2="17"/>

                <circle cx="17" cy="17" r="2"/>

            </svg>

        </span>

        <span>
            Results
        </span>

    </a>

</li>

                <!-- SEARCH -->

                <li>

                    <a href="search_student.php">

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


        <!-- =================================================
             TOP HEADER
        ================================================== -->

        <header class="admin-topbar">


            <div>

                <p class="topbar-label">
                    STUDENT RECORDS
                </p>


                <h1>
                    Student Profile
                </h1>

            </div>



            <!-- ADMIN PROFILE -->

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


                    <span>
                        Administrator
                    </span>

                </div>


            </div>


        </header>



        <!-- =================================================
             PAGE HEADER
        ================================================== -->

        <section class="admin-page-header">


            <h1>
                Student Profile
            </h1>


            <p>
                View complete student information.
            </p>


        </section>



        <!-- =================================================
             STUDENT PROFILE
        ================================================== -->

        <div class="student-profile-card">


            <!-- PROFILE HEADER -->

            <div class="student-profile-header">


                <!-- PASSPORT -->

                <img
                    src="<?php echo htmlspecialchars($passport); ?>"
                    alt="Student Passport"
                    class="student-profile-image"
                    onerror="this.onerror=null;this.src='assets/image/default.png';"
                >



                <!-- STUDENT NAME -->

                <div class="student-profile-name">


                    <h2>

                        <?php

                        echo htmlspecialchars(
                            $student['firstname']
                            . " "
                            . $student['lastname']
                        );

                        ?>

                    </h2>


                    <p>

                        Matric No:

                        <?php

                        echo htmlspecialchars(
                            $student['matric_no']
                        );

                        ?>

                    </p>


                </div>


            </div>



            <!-- =================================================
                 STUDENT DETAILS
            ================================================== -->

            <div class="student-details-grid">


                <!-- MATRIC NUMBER -->

                <div class="student-detail-item">

                    <div class="student-detail-label">
                        Matric Number
                    </div>


                    <div class="student-detail-value">

                        <?php

                        echo htmlspecialchars(
                            $student['matric_no']
                        );

                        ?>

                    </div>

                </div>



                <!-- FULL NAME -->

                <div class="student-detail-item">

                    <div class="student-detail-label">
                        Full Name
                    </div>


                    <div class="student-detail-value">

                        <?php

                        echo htmlspecialchars(
                            $student['firstname']
                            . " "
                            . $student['lastname']
                        );

                        ?>

                    </div>

                </div>



                <!-- GENDER -->

                <div class="student-detail-item">

                    <div class="student-detail-label">
                        Gender
                    </div>


                    <div class="student-detail-value">

                        <?php

                        echo htmlspecialchars(
                            $student['gender']
                        );

                        ?>

                    </div>

                </div>



                <!-- DEPARTMENT -->

                <div class="student-detail-item">

                    <div class="student-detail-label">
                        Department
                    </div>


                    <div class="student-detail-value">

                        <?php

                        echo htmlspecialchars(
                            $student['department']
                        );

                        ?>

                    </div>

                </div>



                <!-- LEVEL -->

                <div class="student-detail-item">

                    <div class="student-detail-label">
                        Level
                    </div>


                    <div class="student-detail-value">

                        <?php

                        echo htmlspecialchars(
                            $student['level']
                        );

                        ?>

                    </div>

                </div>



                <!-- PHONE -->

                <div class="student-detail-item">

                    <div class="student-detail-label">
                        Phone
                    </div>


                    <div class="student-detail-value">

                        <?php

                        echo htmlspecialchars(
                            $student['phone']
                        );

                        ?>

                    </div>

                </div>



                <!-- EMAIL -->

                <div class="student-detail-item">

                    <div class="student-detail-label">
                        Email
                    </div>


                    <div class="student-detail-value">

                        <?php

                        echo htmlspecialchars(
                            $student['email']
                        );

                        ?>

                    </div>

                </div>



                <!-- PASSPORT STATUS -->

                <div class="student-detail-item">

                    <div class="student-detail-label">
                        Passport
                    </div>


                    <div class="student-detail-value">


                        <?php

                        if (!empty($student['passport'])) {

                            echo "Passport uploaded";

                        } else {

                            echo "No passport uploaded";

                        }

                        ?>


                    </div>

                </div>


            </div>


                <!-- students result-->

                <section
                    class="student-results-section"
                    id="results"
                >


                    <div class="student-results-header">

                        <div>

                            <span>
                                ACADEMIC PERFORMANCE
                            </span>

                            <h2>
                                Student Results
                            </h2>

                            <p>
                                Academic results for this student.
                            </p>

                        </div>


                        <a
                            href="add_result.php?student_id=<?php echo (int)$student['id']; ?>"
                            class="student-result-add-btn"
                        >
                            Add Result
                        </a>

                    </div>



                    <?php

                    if (
                        $results_query &&
                        mysqli_num_rows($results_query) > 0
                    ) {

                    ?>


                        <div class="student-results-table-wrapper">


                            <table class="student-results-table">


                                <thead>

                                    <tr>

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

                                while (
                                    $result =
                                    mysqli_fetch_assoc(
                                        $results_query
                                    )
                                ) {

                                ?>

                                    <tr>


                                        <td>

                                            <div class="student-course">

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


                                        <td>

                                            <?php
                                            echo htmlspecialchars(
                                                $result['semester']
                                            );
                                            ?>

                                        </td>


                                        <td>

                                            <?php
                                            echo htmlspecialchars(
                                                $result['session']
                                            );
                                            ?>

                                        </td>


                                        <td>

                                            <strong class="student-score">

                                                <?php
                                                echo htmlspecialchars(
                                                    $result['score']
                                                );
                                                ?>

                                            </strong>

                                        </td>


                                        <td>

                                            <span
                                                class="student-grade grade-<?php
                                                echo strtolower(
                                                    $result['grade']
                                                );
                                                ?>"
                                            >

                                                <?php
                                                echo htmlspecialchars(
                                                    $result['grade']
                                                );
                                                ?>

                                            </span>

                                        </td>


                                        <td>

                                            <?php
                                            echo htmlspecialchars(
                                                $result['remark']
                                            );
                                            ?>

                                        </td>


                                    </tr>


                                <?php

                                }

                                ?>


                                </tbody>


                            </table>


                        </div>


                    <?php

                    } else {

                    ?>


                        <div class="student-results-empty">

                            <div class="student-results-empty-icon">
                                R
                            </div>

                            <h3>
                                No Results Yet
                            </h3>

                            <p>
                                No academic results have been added
                                for this student.
                            </p>

                            <a
                                href="add_result.php?student_id=<?php echo (int)$student['id']; ?>"
                                class="student-result-add-btn"
                            >
                                Add First Result
                            </a>

                        </div>


                    <?php

                    }

                    ?>


                </section>
            <!-- =================================================
                 ACTION BUTTONS
            ================================================== -->

            <div class="student-profile-actions">


                <!-- BACK -->

                <a
                    href="view_students.php"
                    class="student-profile-btn back-profile-btn"
                >
                    Back to Students
                </a>



                <!-- EDIT -->

                <a
                    href="edit_student.php?id=<?php echo (int)$student['id']; ?>"
                    class="student-profile-btn edit-profile-btn"
                >
                    Edit Student
                </a>



                <!-- PRINT -->

                <button
                    type="button"
                    class="student-profile-btn print-profile-btn"
                    onclick="window.print()"
                >
                    Print Profile
                </button>


            </div>


        </div>


    </main>


</div>


</body>

</html>