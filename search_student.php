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

                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>

                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>

                </svg>

            </div>

            <div class="brand-text">

                <h2>Student Record</h2>

                <span>Management System</span>

            </div>

        </div>


        <!-- NAVIGATION -->

        <nav class="admin-navigation">

            <div class="navigation-title">
                MAIN MENU
            </div>

            <ul>

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
                            >

                                <rect x="3" y="3" width="7" height="7" rx="1"/>
                                <rect x="14" y="3" width="7" height="7" rx="1"/>
                                <rect x="3" y="14" width="7" height="7" rx="1"/>
                                <rect x="14" y="14" width="7" height="7" rx="1"/>

                            </svg>

                        </span>

                        <span>Dashboard</span>

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
                            >

                                <circle cx="9" cy="7" r="4"/>
                                <path d="M3 21v-2a6 6 0 0 1 12 0v2"/>
                                <path d="M15 3h6"/>
                                <path d="M18 0v6"/>

                            </svg>

                        </span>

                        <span>Add Student</span>

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
                            >

                                <circle cx="9" cy="7" r="4"/>
                                <path d="M3 21v-2a6 6 0 0 1 12 0v2"/>
                                <circle cx="17" cy="9" r="3"/>
                                <path d="M21 21v-2a4 4 0 0 0-7.5-1.5"/>

                            </svg>

                        </span>

                        <span>View Students</span>

                    </a>

                </li>


                <li>

                    <a
                        href="search_student.php"
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
                            >

                                <circle cx="11" cy="11" r="7"/>
                                <path d="m20 20-4-4"/>

                            </svg>

                        </span>

                        <span>Search Student</span>

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
                            >

                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>

                                <polyline points="16 17 21 12 16 7"/>

                                <line x1="21" y1="12" x2="9" y2="12"/>

                            </svg>

                        </span>

                        <span>Logout</span>

                    </a>

                </li>

            </ul>

        </nav>

    </aside>



    <!-- =====================================================
         MAIN CONTENT
    ====================================================== -->

    <main class="admin-main">


        <!-- TOP HEADER -->

        <header class="admin-topbar">

            <div>

                <div class="topbar-label">
                    STUDENT RECORDS
                </div>

                <h1>
                    Search Student
                </h1>

            </div>

        </header>



        <!-- PAGE CONTENT -->

        <div class="page-content">


            <div class="search-student-page">


                <!-- PAGE INTRO -->

                <div class="search-page-header">

                    <div class="page-eyebrow">
                        STUDENT RECORDS
                    </div>


                    <p>
                        Search student records using a name, matric number,
                        department, email or phone number.
                    </p>

                </div>



                <!-- SEARCH CARD -->

                <div class="search-student-card">


                    <div class="search-card-heading">

                        <div class="search-heading-icon">

                            <svg
                                viewBox="0 0 24 24"
                                width="22"
                                height="22"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >

                                <circle cx="11" cy="11" r="7"/>

                                <path d="m20 20-4-4"/>

                            </svg>

                        </div>


                        <div>

                            <h2>
                                Search Student Records
                            </h2>

                            <p>
                                Enter any student information to find a matching record.
                            </p>

                        </div>

                    </div>



                    <form
                        method="GET"
                        class="search-student-form"
                    >

                        <div class="search-student-input">

                            <svg
                                viewBox="0 0 24 24"
                                width="19"
                                height="19"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >

                                <circle cx="11" cy="11" r="7"/>

                                <path d="m20 20-4-4"/>

                            </svg>


                            <input
                                type="text"
                                name="search"
                                value="<?php echo htmlspecialchars($keyword); ?>"
                                placeholder="Search by matric number, name, department, email or phone..."
                                required
                            >

                        </div>


                        <button
                            type="submit"
                            class="search-student-button"
                        >

                            <svg
                                viewBox="0 0 24 24"
                                width="17"
                                height="17"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >

                                <circle cx="11" cy="11" r="7"/>

                                <path d="m20 20-4-4"/>

                            </svg>

                            Search

                        </button>


                        <?php if ($keyword !== ""): ?>

                            <a
                                href="search_student.php"
                                class="search-clear-button"
                            >
                                Clear
                            </a>

                        <?php endif; ?>


                    </form>


                </div>



                <!-- SEARCH RESULTS -->

                <?php if ($search_result !== null): ?>


                    <div class="search-results-card">


                        <div class="search-results-header">

                            <div>

                                <h2>
                                    Search Results
                                </h2>

                                <p>
                                    Results matching:
                                    <strong>
                                        "<?php echo htmlspecialchars($keyword); ?>"
                                    </strong>
                                </p>

                            </div>


                            <div class="results-count">

                                <?php echo mysqli_num_rows($search_result); ?>

                                <span>
                                    result<?php echo mysqli_num_rows($search_result) == 1 ? '' : 's'; ?>
                                </span>

                            </div>

                        </div>



                        <?php if (mysqli_num_rows($search_result) > 0): ?>


                            <div class="search-results-table-wrapper">

                                <table class="search-results-table">

                                    <thead>

                                        <tr>

                                            <th>#</th>

                                            <th>Student</th>

                                            <th>Matric Number</th>

                                            <th>Gender</th>

                                            <th>Department</th>

                                            <th>Level</th>

                                            <th>Phone</th>

                                            <th>Action</th>

                                        </tr>

                                    </thead>


                                    <tbody>


                                    <?php

                                    $sn = 1;

                                    while (
                                        $row =
                                        mysqli_fetch_assoc($search_result)
                                    ):

                                        if (!empty($row['passport'])) {

                                            $student_passport =
                                                "assets/upload/students/"
                                                . $row['passport'];

                                        } else {

                                            $student_passport =
                                                "assets/image/default.png";

                                        }

                                        $full_name =
                                            $row['firstname']
                                            . " "
                                            . $row['lastname'];

                                    ?>


                                        <tr>


                                            <td class="search-serial">
                                                <?php echo $sn++; ?>
                                            </td>


                                            <td>

                                                <div class="search-student-cell">

                                                    <img
                                                        src="<?php echo htmlspecialchars($student_passport); ?>"
                                                        alt="Student Passport"
                                                        onerror="this.onerror=null;this.src='assets/image/default.png';"
                                                    >


                                                    <div>

                                                        <a
                                                            href="student_profile.php?id=<?php echo (int)$row['id']; ?>"
                                                            class="search-student-name"
                                                        >

                                                            <?php
                                                            echo htmlspecialchars($full_name);
                                                            ?>

                                                        </a>


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

                                                <span class="search-matric">

                                                    <?php
                                                    echo htmlspecialchars(
                                                        $row['matric_no']
                                                    );
                                                    ?>

                                                </span>

                                            </td>


                                            <td>

                                                <span class="search-gender">

                                                    <?php
                                                    echo htmlspecialchars(
                                                        $row['gender']
                                                    );
                                                    ?>

                                                </span>

                                            </td>


                                            <td>

                                                <span class="search-department">

                                                    <?php
                                                    echo htmlspecialchars(
                                                        $row['department']
                                                    );
                                                    ?>

                                                </span>

                                            </td>


                                            <td>

                                                <span class="search-level">

                                                    <?php
                                                    echo htmlspecialchars(
                                                        $row['level']
                                                    );
                                                    ?>

                                                </span>

                                            </td>


                                            <td>

                                                <span class="search-phone">

                                                    <?php
                                                    echo htmlspecialchars(
                                                        $row['phone']
                                                    );
                                                    ?>

                                                </span>

                                            </td>


                                            <td>

                                                <div class="search-actions">

                                                    <a
                                                        href="student_profile.php?id=<?php echo (int)$row['id']; ?>"
                                                        class="search-action view"
                                                    >
                                                        View
                                                    </a>


                                                    <a
                                                        href="edit_student.php?id=<?php echo (int)$row['id']; ?>"
                                                        class="search-action edit"
                                                    >
                                                        Edit
                                                    </a>

                                                </div>

                                            </td>


                                        </tr>


                                    <?php endwhile; ?>


                                    </tbody>

                                </table>

                            </div>


                        <?php else: ?>


                            <div class="search-empty-state">

                                <div class="search-empty-icon">

                                    <svg
                                        viewBox="0 0 24 24"
                                        width="32"
                                        height="32"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.6"
                                    >

                                        <circle cx="11" cy="11" r="7"/>

                                        <path d="m20 20-4-4"/>

                                        <path d="M8 8l6 6"/>

                                    </svg>

                                </div>


                                <h3>
                                    No Student Found
                                </h3>


                                <p>
                                    No student record matched
                                    "<?php echo htmlspecialchars($keyword); ?>".
                                    Try searching with another name,
                                    matric number or department.
                                </p>


                                <a
                                    href="search_student.php"
                                    class="search-empty-button"
                                >
                                    New Search
                                </a>

                            </div>


                        <?php endif; ?>


                    </div>


                <?php endif; ?>


            </div>


        </div>



        <!-- FOOTER -->

        <footer class="admin-footer">

            <span>SRMS</span>

            <span class="footer-dot">·</span>

            <span>
                Student Record Management System
            </span>

            <span class="footer-dot">·</span>

            <span>
                &copy; <?php echo date('Y'); ?>
            </span>

        </footer>


    </main>

</div>

</body>
</html>