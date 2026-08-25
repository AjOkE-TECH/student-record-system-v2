<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include "config/database.php";


/* =========================================================
   TOTAL STUDENTS
========================================================= */

$total_query = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM students"
);

$total_result = mysqli_fetch_assoc($total_query);
$total_students = $total_result['total'];


/* =========================================================
   MALE STUDENTS
========================================================= */

$male_query = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM students
     WHERE gender = 'Male'"
);

$male_result = mysqli_fetch_assoc($male_query);
$male_students = $male_result['total'];


/* =========================================================
   FEMALE STUDENTS
========================================================= */

$female_query = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM students
     WHERE gender = 'Female'"
);

$female_result = mysqli_fetch_assoc($female_query);
$female_students = $female_result['total'];


/* =========================================================
   NUMBER OF DEPARTMENTS
========================================================= */

$department_count_query = mysqli_query(
    $conn,
    "SELECT COUNT(DISTINCT department) AS total
     FROM students
     WHERE department IS NOT NULL
     AND department != ''"
);

$department_count_result =
    mysqli_fetch_assoc($department_count_query);

$total_departments =
    $department_count_result['total'];


/* =========================================================
   STUDENTS BY DEPARTMENT
========================================================= */

$department_query = mysqli_query(
    $conn,
    "SELECT
        department,
        COUNT(*) AS total
     FROM students
     WHERE department IS NOT NULL
     AND department != ''
     GROUP BY department
     ORDER BY total DESC"
);


/* =========================================================
   STUDENTS BY LEVEL
========================================================= */

$level_query = mysqli_query(
    $conn,
    "SELECT
        level,
        COUNT(*) AS total
     FROM students
     WHERE level IS NOT NULL
     AND level != ''
     GROUP BY level
     ORDER BY total DESC"
);


/* =========================================================
   STUDENTS BY GENDER
========================================================= */

$gender_query = mysqli_query(
    $conn,
    "SELECT
        gender,
        COUNT(*) AS total
     FROM students
     WHERE gender IS NOT NULL
     AND gender != ''
     GROUP BY gender"
);


/* =========================================================
   MONTHLY REGISTRATION
========================================================= */

$monthly_query = mysqli_query(
    $conn,
    "SELECT
        DATE_FORMAT(created_at, '%M %Y') AS month_name,
        DATE_FORMAT(created_at, '%Y-%m') AS month_sort,
        COUNT(*) AS total
     FROM students
     GROUP BY month_sort, month_name
     ORDER BY month_sort DESC
     LIMIT 6"
);


/* =========================================================
   RECENT STUDENTS
========================================================= */

$recent_query = mysqli_query(
    $conn,
    "SELECT
        id,
        firstname,
        lastname,
        matric_no,
        department,
        level,
        created_at
     FROM students
     ORDER BY id DESC
     LIMIT 5"
);


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
        Dashboard - Student Record Management System
    </title>

    <link
        rel="stylesheet"
        href="assets/css/admin.css"
    >

</head>


<body>


<div class="admin-layout">

    <!--sidebar-->

    <aside class="admin-sidebar">


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
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
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


                <li>

                    <a href="view_students.php">

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

    <!-- main content -->

    <main class="admin-main">


        <!-- =================================================
             TOP HEADER
        ================================================== -->

        <header class="admin-topbar">


            <div>

                <p class="topbar-label">
                    ADMINISTRATION
                </p>

                <h1>
                    Dashboard
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
             WELCOME SECTION
        ================================================== -->

        <section class="welcome-section">


            <div>

                <span class="welcome-label">
                    OVERVIEW
                </span>


                <h2>

                    Welcome back,

                    <?php

                    echo htmlspecialchars(
                        $admin_name
                    );

                    ?>

                </h2>


                <p>
                    Here's an overview of your student records
                    and current system activity.
                </p>

            </div>


            <a
                href="add_student.php"
                class="primary-action"
            >
                Add New Student
            </a>


        </section>



        <!-- =================================================
             STATISTICS
        ================================================== -->

        <section class="statistics-grid">


            <!-- TOTAL STUDENTS -->

            <div class="stat-card stat-green">


                <div class="stat-card-top">


                    <div class="stat-icon">

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
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M3 21v-2a6 6 0 0 1 12 0v2"/>
                            <circle cx="17" cy="9" r="3"/>
                            <path d="M21 21v-2a4 4 0 0 0-7.5-1.5"/>
                        </svg>

                    </div>


                    <span class="stat-label">
                        STUDENTS
                    </span>


                </div>


                <h3>

                    <?php
                    echo $total_students;
                    ?>

                </h3>


                <p>
                    Total registered students
                </p>


            </div>



            <!-- MALE -->

            <div class="stat-card stat-blue">


                <div class="stat-card-top">


                    <div class="stat-icon">

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
                            <circle cx="10" cy="7" r="4"/>
                            <path d="M4 21v-2a6 6 0 0 1 12 0v2"/>
                        </svg>

                    </div>


                    <span class="stat-label">
                        MALE
                    </span>


                </div>


                <h3>

                    <?php
                    echo $male_students;
                    ?>

                </h3>


                <p>
                    Male students
                </p>


            </div>



            <!-- FEMALE -->

            <div class="stat-card stat-teal">


                <div class="stat-card-top">


                    <div class="stat-icon">

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
                            <circle cx="10" cy="7" r="4"/>
                            <path d="M4 21v-2a6 6 0 0 1 12 0v2"/>
                            <path d="M17 3v4"/>
                            <path d="M15 5h4"/>
                        </svg>

                    </div>


                    <span class="stat-label">
                        FEMALE
                    </span>


                </div>


                <h3>

                    <?php
                    echo $female_students;
                    ?>

                </h3>


                <p>
                    Female students
                </p>


            </div>



            <!-- DEPARTMENTS -->

            <div class="stat-card stat-orange">


                <div class="stat-card-top">


                    <div class="stat-icon">

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
                            <path d="M3 21h18"/>
                            <path d="M5 21V6l7-3 7 3v15"/>
                            <path d="M9 9h2"/>
                            <path d="M13 9h2"/>
                            <path d="M9 13h2"/>
                            <path d="M13 13h2"/>
                        </svg>

                    </div>


                    <span class="stat-label">
                        DEPARTMENTS
                    </span>


                </div>


                <h3>

                    <?php
                    echo $total_departments;
                    ?>

                </h3>


                <p>
                    Active departments
                </p>


            </div>


        </section>



        <!-- =================================================
             QUICK ACTIONS
        ================================================== -->

        <section class="quick-actions-section">


            <div class="section-heading">


                <div>

                    <span>
                        ACTIONS
                    </span>

                    <h2>
                        Quick Actions
                    </h2>

                </div>


            </div>


            <div class="quick-actions-grid">


                <a
                    href="add_student.php"
                    class="quick-action green-action"
                >


                    <div class="quick-action-icon">

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

                    </div>


                    <div>

                        <strong>
                            Add Student
                        </strong>

                        <span>
                            Register a new student
                        </span>

                    </div>


                </a>



                <a
                    href="view_students.php"
                    class="quick-action blue-action"
                >


                    <div class="quick-action-icon">

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

                    </div>


                    <div>

                        <strong>
                            View Students
                        </strong>

                        <span>
                            Browse student records
                        </span>

                    </div>


                </a>



                <a
                    href="search_student.php"
                    class="quick-action teal-action"
                >


                    <div class="quick-action-icon">

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

                    </div>


                    <div>

                        <strong>
                            Search Students
                        </strong>

                        <span>
                            Find a student record
                        </span>

                    </div>


                </a>


            </div>


        </section>



        <!-- =================================================
             ANALYTICS
        ================================================== -->

        <section class="analytics-section">


            <div class="section-heading">


                <div>

                    <span>
                        ANALYTICS
                    </span>

                    <h2>
                        Student Analytics
                    </h2>

                </div>


            </div>


            <div class="analytics-grid">


                <!-- =================================================
                     DEPARTMENT
                ================================================== -->

                <div class="analytics-card">


                    <div class="analytics-card-header">


                        <div>

                            <h3>
                                Students by Department
                            </h3>

                            <p>
                                Distribution across departments
                            </p>

                        </div>


                        <div class="analytics-badge green-badge">
                            DEPARTMENT
                        </div>


                    </div>


                    <?php

                    if (
                        mysqli_num_rows(
                            $department_query
                        ) > 0
                    ) {

                        while (
                            $department =
                            mysqli_fetch_assoc(
                                $department_query
                            )
                        ) {

                            $department_total =
                                $department['total'];

                            $department_percentage = 0;

                            if ($total_students > 0) {

                                $department_percentage =
                                    (
                                        $department_total
                                        /
                                        $total_students
                                    ) * 100;

                            }

                    ?>

                        <div class="analytics-row">


                            <div class="analytics-info">


                                <strong>

                                    <?php

                                    echo htmlspecialchars(
                                        $department['department']
                                    );

                                    ?>

                                </strong>


                                <span>

                                    <?php

                                    echo $department_total;

                                    ?>

                                </span>


                            </div>


                            <div class="progress-container">


                                <div
                                    class="progress-bar progress-green"
                                    style="width: <?php echo $department_percentage; ?>%;"
                                ></div>


                            </div>


                        </div>


                    <?php

                        }

                    } else {

                    ?>

                        <div class="empty-data">
                            No department data available.
                        </div>

                    <?php

                    }

                    ?>


                </div>



                <!-- =================================================
                     LEVEL
                ================================================== -->

                <div class="analytics-card">


                    <div class="analytics-card-header">


                        <div>

                            <h3>
                                Students by Level
                            </h3>

                            <p>
                                Distribution across academic levels
                            </p>

                        </div>


                        <div class="analytics-badge blue-badge">
                            LEVEL
                        </div>


                    </div>


                    <?php

                    if (
                        mysqli_num_rows(
                            $level_query
                        ) > 0
                    ) {

                        while (
                            $level =
                            mysqli_fetch_assoc(
                                $level_query
                            )
                        ) {

                            $level_total =
                                $level['total'];

                            $level_percentage = 0;

                            if ($total_students > 0) {

                                $level_percentage =
                                    (
                                        $level_total
                                        /
                                        $total_students
                                    ) * 100;

                            }

                    ?>

                        <div class="analytics-row">


                            <div class="analytics-info">


                                <strong>

                                    <?php

                                    echo htmlspecialchars(
                                        $level['level']
                                    );

                                    ?>

                                </strong>


                                <span>

                                    <?php

                                    echo $level_total;

                                    ?>

                                </span>


                            </div>


                            <div class="progress-container">


                                <div
                                    class="progress-bar progress-blue"
                                    style="width: <?php echo $level_percentage; ?>%;"
                                ></div>


                            </div>


                        </div>


                    <?php

                        }

                    } else {

                    ?>

                        <div class="empty-data">
                            No level data available.
                        </div>

                    <?php

                    }

                    ?>


                </div>



                <!-- =================================================
                     GENDER
                ================================================== -->

                <div class="analytics-card">


                    <div class="analytics-card-header">


                        <div>

                            <h3>
                                Gender Distribution
                            </h3>

                            <p>
                                Student gender statistics
                            </p>

                        </div>


                        <div class="analytics-badge teal-badge">
                            GENDER
                        </div>


                    </div>


                    <?php

                    if (
                        mysqli_num_rows(
                            $gender_query
                        ) > 0
                    ) {

                        while (
                            $gender =
                            mysqli_fetch_assoc(
                                $gender_query
                            )
                        ) {

                            $gender_total =
                                $gender['total'];

                            $gender_percentage = 0;

                            if ($total_students > 0) {

                                $gender_percentage =
                                    (
                                        $gender_total
                                        /
                                        $total_students
                                    ) * 100;

                            }

                    ?>

                        <div class="analytics-row">


                            <div class="analytics-info">


                                <strong>

                                    <?php

                                    echo htmlspecialchars(
                                        $gender['gender']
                                    );

                                    ?>

                                </strong>


                                <span>

                                    <?php

                                    echo $gender_total;

                                    ?>

                                    (

                                    <?php

                                    echo round(
                                        $gender_percentage,
                                        1
                                    );

                                    ?>%

                                    )

                                </span>


                            </div>


                            <div class="progress-container">


                                <div
                                    class="progress-bar progress-teal"
                                    style="width: <?php echo $gender_percentage; ?>%;"
                                ></div>


                            </div>


                        </div>


                    <?php

                        }

                    } else {

                    ?>

                        <div class="empty-data">
                            No gender data available.
                        </div>

                    <?php

                    }

                    ?>


                </div>



                <!-- =================================================
                     MONTHLY REGISTRATION
                ================================================== -->

                <div class="analytics-card">


                    <div class="analytics-card-header">


                        <div>

                            <h3>
                                Recent Registrations
                            </h3>

                            <p>
                                Student registrations by month
                            </p>

                        </div>


                        <div class="analytics-badge orange-badge">
                            ACTIVITY
                        </div>


                    </div>


                    <?php

                    if (
                        mysqli_num_rows(
                            $monthly_query
                        ) > 0
                    ) {

                        $monthly_data = [];


                        while (
                            $month =
                            mysqli_fetch_assoc(
                                $monthly_query
                            )
                        ) {

                            $monthly_data[] = $month;

                        }


                        $highest_month_total =
                            max(
                                array_column(
                                    $monthly_data,
                                    'total'
                                )
                            );


                        foreach (
                            $monthly_data
                            as $month
                        ) {

                            $monthly_percentage = 0;


                            if (
                                $highest_month_total > 0
                            ) {

                                $monthly_percentage =
                                    (
                                        $month['total']
                                        /
                                        $highest_month_total
                                    ) * 100;

                            }

                    ?>

                        <div class="analytics-row">


                            <div class="analytics-info">


                                <strong>

                                    <?php

                                    echo htmlspecialchars(
                                        $month['month_name']
                                    );

                                    ?>

                                </strong>


                                <span>

                                    <?php

                                    echo $month['total'];

                                    ?>

                                </span>


                            </div>


                            <div class="progress-container">


                                <div
                                    class="progress-bar progress-orange"
                                    style="width: <?php echo $monthly_percentage; ?>%;"
                                ></div>


                            </div>


                        </div>


                    <?php

                        }

                    } else {

                    ?>

                        <div class="empty-data">
                            No registration data available.
                        </div>

                    <?php

                    }

                    ?>


                </div>


            </div>


        </section>



        <!-- =================================================
             RECENT STUDENTS
        ================================================== -->

        <section class="recent-card">


            <div class="recent-header">


                <div>

                    <span>
                        STUDENT RECORDS
                    </span>

                    <h2>
                        Recently Added Students
                    </h2>

                </div>


                <a
                    href="view_students.php"
                    class="view-all-btn"
                >
                    View All Students
                </a>


            </div>


            <div class="recent-table-wrapper">


                <table class="recent-table">


                    <thead>


                        <tr>

                            <th>
                                Name
                            </th>

                            <th>
                                Matric No
                            </th>

                            <th>
                                Department
                            </th>

                            <th>
                                Level
                            </th>

                            <th>
                                Date Added
                            </th>

                        </tr>


                    </thead>


                    <tbody>


                    <?php

                    if (
                        mysqli_num_rows(
                            $recent_query
                        ) > 0
                    ) {

                        while (
                            $recent =
                            mysqli_fetch_assoc(
                                $recent_query
                            )
                        ) {

                    ?>

                        <tr>


                            <td>

                                <strong class="student-name">

                                    <?php

                                    echo htmlspecialchars(
                                        $recent['firstname']
                                        . " "
                                        . $recent['lastname']
                                    );

                                    ?>

                                </strong>

                            </td>


                            <td>

                                <span class="matric-number">

                                    <?php

                                    echo htmlspecialchars(
                                        $recent['matric_no']
                                    );

                                    ?>

                                </span>

                            </td>


                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $recent['department']
                                );

                                ?>

                            </td>


                            <td>

                                <span class="level-badge">

                                    <?php

                                    echo htmlspecialchars(
                                        $recent['level']
                                    );

                                    ?>

                                </span>

                            </td>


                            <td>

                                <?php

                                echo date(
                                    'd M Y',
                                    strtotime(
                                        $recent['created_at']
                                    )
                                );

                                ?>

                            </td>


                        </tr>


                    <?php

                        }

                    } else {

                    ?>

                        <tr>

                            <td colspan="5">

                                <div class="empty-table">
                                    No students found.
                                </div>

                            </td>

                        </tr>

                    <?php

                    }

                    ?>


                    </tbody>


                </table>


            </div>


        </section>



        <!-- =================================================
             FOOTER
        ================================================== -->

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