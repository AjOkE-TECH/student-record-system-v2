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


    <!-- =====================================================
         SIDEBAR
    ====================================================== -->

    <aside class="admin-sidebar">


        <div class="admin-sidebar-title">

            <h2>
                Management<br>
                System
            </h2>

        </div>


        <nav class="admin-navigation">

            <ul>


                <li>

                    <a
                        href="dashboard.php"
                        class="active"
                    >
                        Dashboard
                    </a>

                </li>


                <li>

                    <a href="add_student.php">
                        Add Student
                    </a>

                </li>


                <li>

                    <a href="view_students.php">
                        View Students
                    </a>

                </li>


                <li>

                    <a href="search_student.php">
                        Search Student
                    </a>

                </li>


                <li>

                    <a href="logout.php">
                        Logout
                    </a>

                </li>


            </ul>

        </nav>

    </aside>



    <!-- =====================================================
         MAIN
    ====================================================== -->

    <main class="admin-main">


        <!-- HEADER -->

        <section class="dashboard-header">

            <h1>
                Dashboard
            </h1>

            <p>
                Welcome,
                <?php
                echo htmlspecialchars($admin_name);
                ?>
            </p>

        </section>



        <!-- =================================================
             STATISTICS
        ================================================== -->

        <section class="statistics-grid">


            <div class="stat-card">

                <h3>
                    Total Students
                </h3>

                <p class="stat-number">
                    <?php
                    echo $total_students;
                    ?>
                </p>

            </div>


            <div class="stat-card">

                <h3>
                    Male Students
                </h3>

                <p class="stat-number">
                    <?php
                    echo $male_students;
                    ?>
                </p>

            </div>


            <div class="stat-card">

                <h3>
                    Female Students
                </h3>

                <p class="stat-number">
                    <?php
                    echo $female_students;
                    ?>
                </p>

            </div>


            <div class="stat-card">

                <h3>
                    Departments
                </h3>

                <p class="stat-number">
                    <?php
                    echo $total_departments;
                    ?>
                </p>

            </div>


        </section>



        <!-- =================================================
             ANALYTICS TITLE
        ================================================== -->

        <h2 class="analytics-title">
            Student Analytics
        </h2>



        <!-- =================================================
             ANALYTICS
        ================================================== -->

        <section class="analytics-grid">


            <!-- =============================================
                 DEPARTMENT
            ============================================== -->

            <div class="analytics-card">

                <h3>
                    Students by Department
                </h3>


                <?php

                if (
                    mysqli_num_rows($department_query) > 0
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


                        <progress
                            class="analytics-progress"
                            value="<?php echo $department_percentage; ?>"
                            max="100"
                        ></progress>


                    </div>

                <?php

                    }

                } else {

                ?>

                    <p>
                        No department data available.
                    </p>

                <?php

                }

                ?>

            </div>



            <!-- =============================================
                 LEVEL
            ============================================== -->

            <div class="analytics-card">

                <h3>
                    Students by Level
                </h3>


                <?php

                if (
                    mysqli_num_rows($level_query) > 0
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


                        <progress
                            class="analytics-progress"
                            value="<?php echo $level_percentage; ?>"
                            max="100"
                        ></progress>


                    </div>

                <?php

                    }

                } else {

                ?>

                    <p>
                        No level data available.
                    </p>

                <?php

                }

                ?>

            </div>



            <!-- =============================================
                 GENDER
            ============================================== -->

            <div class="analytics-card">

                <h3>
                    Gender Distribution
                </h3>


                <?php

                if (
                    mysqli_num_rows($gender_query) > 0
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


                        <progress
                            class="analytics-progress"
                            value="<?php echo $gender_percentage; ?>"
                            max="100"
                        ></progress>


                    </div>

                <?php

                    }

                } else {

                ?>

                    <p>
                        No gender data available.
                    </p>

                <?php

                }

                ?>

            </div>



            <!-- =============================================
                 MONTHLY REGISTRATION
            ============================================== -->

            <div class="analytics-card">

                <h3>
                    Recent Student Registrations
                </h3>


                <?php

                if (
                    mysqli_num_rows($monthly_query) > 0
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


                        <progress
                            class="analytics-progress"
                            value="<?php echo $monthly_percentage; ?>"
                            max="100"
                        ></progress>


                    </div>

                <?php

                    }

                } else {

                ?>

                    <p>
                        No registration data available.
                    </p>

                <?php

                }

                ?>

            </div>


        </section>



        <!-- =================================================
             RECENT STUDENTS
        ================================================== -->

        <section class="recent-card">


            <h3>
                Recently Added Students
            </h3>


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
                        mysqli_num_rows($recent_query) > 0
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

                                <?php

                                echo htmlspecialchars(
                                    $recent['firstname']
                                    . " "
                                    . $recent['lastname']
                                );

                                ?>

                            </td>


                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $recent['matric_no']
                                );

                                ?>

                            </td>


                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $recent['department']
                                );

                                ?>

                            </td>


                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $recent['level']
                                );

                                ?>

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
                                No students found.
                            </td>

                        </tr>

                    <?php

                    }

                    ?>


                    </tbody>


                </table>


            </div>


            <a
                href="view_students.php"
                class="view-all-btn"
            >
                View All Students
            </a>


        </section>


    </main>


</div>


</body>

</html>