<?php

session_start();

if (!isset($_SESSION['admin'])) {

    header("Location: login.php");

    exit();
}

include "config/database.php";


$search_result = null;

$keyword = "";


/* =========================================================
   SEARCH
   Supports both GET and POST
========================================================= */

if (
    isset($_GET['search']) ||
    isset($_POST['search']) ||
    isset($_POST['keyword'])
) {


    if (
        isset($_GET['search'])
    ) {

        $keyword =
            trim($_GET['search']);

    } else {

        $keyword =
            trim(
                $_POST['keyword'] ?? ''
            );
    }


    if ($keyword !== '') {


        $keyword_safe =
            mysqli_real_escape_string(
                $conn,
                $keyword
            );


        $search_result =
            mysqli_query(
                $conn,

                "SELECT *
                 FROM students

                 WHERE matric_no LIKE '%$keyword_safe%'

                 OR firstname LIKE '%$keyword_safe%'

                 OR lastname LIKE '%$keyword_safe%'

                 OR email LIKE '%$keyword_safe%'

                 OR department LIKE '%$keyword_safe%'

                 OR level LIKE '%$keyword_safe%'

                 OR phone LIKE '%$keyword_safe%'

                 ORDER BY id DESC"
            );

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
        Search Student - Student Record Management System
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

                    <a href="dashboard.php">
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

                    <a
                        href="search_student.php"
                        class="active"
                    >
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

        <section class="admin-page-header">

            <h1>
                Search Student
            </h1>

            <p>
                Search student records by name,
                matric number, email or other details.
            </p>

        </section>



        <!-- SEARCH -->

        <div class="admin-card">


            <form
                method="GET"
                class="search-form"
            >


                <input
                    type="text"
                    name="search"
                    value="<?php echo htmlspecialchars($keyword); ?>"
                    placeholder="Enter matric number, name or email"
                    required
                >


                <button type="submit">
                    Search
                </button>


            </form>


        </div>



        <!-- =================================================
             SEARCH RESULTS
        ================================================== -->

        <?php

        if ($search_result !== null) {

        ?>


            <section class="recent-card">


                <h3>
                    Search Results
                </h3>


                <div class="table-wrapper">


                    <table class="students-table">


                        <thead>

                            <tr>

                                <th>
                                    S/N
                                </th>

                                <th>
                                    Passport
                                </th>

                                <th>
                                    Matric No
                                </th>

                                <th>
                                    Name
                                </th>

                                <th>
                                    Gender
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
                                    Email
                                </th>

                                <th>
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody>


                        <?php

                        if (
                            mysqli_num_rows(
                                $search_result
                            ) > 0
                        ) {


                            $sn = 1;


                            while (
                                $row =
                                mysqli_fetch_assoc(
                                    $search_result
                                )
                            ) {

                        ?>


                            <tr>


                                <!-- S/N -->

                                <td>

                                    <?php
                                    echo $sn++;
                                    ?>

                                </td>



                                <!-- PASSPORT -->

                                <td>


                                    <?php

                                    if (
                                        !empty(
                                            $row['passport']
                                        )
                                    ) {

                                    ?>

                                        <img
                                            src="assets/upload/students/<?php echo htmlspecialchars($row['passport']); ?>"
                                            class="passport-image"
                                            alt="Student Passport"
                                            onerror="this.onerror=null;this.src='assets/image/default.png';"
                                        >

                                    <?php

                                    } else {

                                    ?>

                                        <img
                                            src="assets/image/default.png"
                                            class="passport-image"
                                            alt="No Passport"
                                        >

                                    <?php

                                    }

                                    ?>


                                </td>



                                <!-- MATRIC -->

                                <td>

                                    <?php

                                    echo htmlspecialchars(
                                        $row['matric_no']
                                    );

                                    ?>

                                </td>



                                <!-- NAME -->

                                <td>

                                    <a
                                        href="student_profile.php?id=<?php echo (int)$row['id']; ?>"
                                        class="student-name-link"
                                    >

                                        <?php

                                        echo htmlspecialchars(
                                            $row['firstname']
                                            . " "
                                            . $row['lastname']
                                        );

                                        ?>

                                    </a>

                                </td>



                                <!-- GENDER -->

                                <td>

                                    <?php

                                    echo htmlspecialchars(
                                        $row['gender']
                                    );

                                    ?>

                                </td>



                                <!-- DEPARTMENT -->

                                <td>

                                    <?php

                                    echo htmlspecialchars(
                                        $row['department']
                                    );

                                    ?>

                                </td>



                                <!-- LEVEL -->

                                <td>

                                    <?php

                                    echo htmlspecialchars(
                                        $row['level']
                                    );

                                    ?>

                                </td>



                                <!-- PHONE -->

                                <td>

                                    <?php

                                    echo htmlspecialchars(
                                        $row['phone']
                                    );

                                    ?>

                                </td>



                                <!-- EMAIL -->

                                <td>

                                    <?php

                                    echo htmlspecialchars(
                                        $row['email']
                                    );

                                    ?>

                                </td>



                                <!-- ACTION -->

                                <td>

                                    <div class="action-buttons">


                                        <a
                                            href="student_profile.php?id=<?php echo (int)$row['id']; ?>"
                                            class="action-btn view-btn"
                                            title="View Student"
                                        >
                                            👁
                                        </a>


                                        <a
                                            href="edit_student.php?id=<?php echo (int)$row['id']; ?>"
                                            class="action-btn edit-btn"
                                            title="Edit Student"
                                        >
                                            ✏
                                        </a>


                                    </div>

                                </td>


                            </tr>


                        <?php

                            }

                        } else {

                        ?>


                            <tr>

                                <td
                                    colspan="10"
                                    class="no-students"
                                >

                                    No student records
                                    matched your search.

                                </td>

                            </tr>


                        <?php

                        }

                        ?>


                        </tbody>


                    </table>


                </div>


            </section>


        <?php } ?>


    </main>


</div>


</body>

</html>