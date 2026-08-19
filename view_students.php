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

$limit = 5;


$page =
    isset($_GET['page'])
    ? (int)$_GET['page']
    : 1;


if ($page < 1) {

    $page = 1;

}


$start =
    ($page - 1) * $limit;


/* =========================================================
   GET STUDENTS
========================================================= */

$query = mysqli_query(
    $conn,
    "SELECT *
     FROM students
     ORDER BY id DESC
     LIMIT $start, $limit"
);


/* =========================================================
   COUNT STUDENTS
========================================================= */

$total_query = mysqli_query(
    $conn,
    "SELECT COUNT(id) AS total
     FROM students"
);


$total_result =
    mysqli_fetch_assoc(
        $total_query
    );


$total_records =
    $total_result['total'];


$total_pages =
    ($total_records > 0)
    ? ceil($total_records / $limit)
    : 1;

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
        View Students - Student Record Management System
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

                    <a
                        href="view_students.php"
                        class="active"
                    >
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

        <section class="admin-page-header">

            <h1>
                Student Records
            </h1>

            <p>
                View and manage registered students.
            </p>

        </section>



        <!-- SEARCH + CREATE -->

        <div class="top-actions">


            <form
                action="search_student.php"
                method="GET"
                class="search-form"
            >


                <input
                    type="text"
                    name="search"
                    placeholder="Search by name, matric number or email"
                >


                <button type="submit">
                    Search
                </button>


            </form>


            <a
                href="add_student.php"
                class="create-btn"
            >
                + Create Student
            </a>


        </div>



        <!-- TABLE -->

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
                            Actions
                        </th>

                    </tr>

                </thead>



                <tbody>


                <?php

                if (
                    mysqli_num_rows($query) > 0
                ) {


                    $sn = $start + 1;


                    while (
                        $row =
                        mysqli_fetch_assoc($query)
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



                        <!-- MATRIC NUMBER -->

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



                        <!-- ACTIONS -->

                        <td>


                            <div class="action-buttons">


                                <!-- VIEW -->

                                <a
                                    href="student_profile.php?id=<?php echo (int)$row['id']; ?>"
                                    class="action-btn view-btn"
                                    title="View Student"
                                >
                                    👁
                                </a>



                                <!-- EDIT -->

                                <a
                                    href="edit_student.php?id=<?php echo (int)$row['id']; ?>"
                                    class="action-btn edit-btn"
                                    title="Edit Student"
                                >
                                    ✏
                                </a>



                                <!-- DELETE -->

                                <a
                                    href="delete_student.php?id=<?php echo (int)$row['id']; ?>"
                                    class="action-btn delete-btn"
                                    title="Delete Student"
                                    onclick="return confirm('Are you sure you want to delete this student?')"
                                >
                                    🗑
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
                            No student records found.
                        </td>

                    </tr>


                <?php

                }

                ?>


                </tbody>


            </table>


        </div>



        <!-- =================================================
             PAGINATION
        ================================================== -->

        <?php if ($total_pages > 1) { ?>


            <div class="pagination">


                <?php

                for (
                    $i = 1;
                    $i <= $total_pages;
                    $i++
                ) {

                ?>


                    <a
                        href="view_students.php?page=<?php echo $i; ?>"
                        class="<?php echo ($page == $i) ? 'active' : ''; ?>"
                    >

                        <?php
                        echo $i;
                        ?>

                    </a>


                <?php

                }

                ?>


            </div>


        <?php } ?>


    </main>


</div>


</body>

</html>