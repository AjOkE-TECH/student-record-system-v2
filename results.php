<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include "config/database.php";

$current_page = basename($_SERVER['PHP_SELF']);

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include "config/database.php";

/* check student ID*/

if (!isset($_GET['student_id']) || empty($_GET['student_id'])) {
    header("Location: results.php");
    exit();
}

$student_id = (int) $_GET['student_id'];

/* get student*/

$student_query = mysqli_query(
    $conn,
    "SELECT id, firstname, lastname, matric_no
     FROM students
     WHERE id = $student_id"
);

if (
    !$student_query ||
    mysqli_num_rows($student_query) == 0
) {
    header("Location: results.php");
    exit();
}

$student = mysqli_fetch_assoc($student_query);


/* form variable */

$error = "";

$course_code = "";
$course_title = "";
$semester = "";
$session = "";
$score = "";

/*proccess form */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $course_code = trim($_POST['course_code'] ?? "");
    $course_title = trim($_POST['course_title'] ?? "");
    $semester = trim($_POST['semester'] ?? "");
    $session = trim($_POST['session'] ?? "");
    $score = trim($_POST['score'] ?? "");


    /* validation */

    if (
        empty($course_code) ||
        empty($course_title) ||
        empty($semester) ||
        empty($session) ||
        $score === ""
    ) {

        $error = "Please fill in all result fields.";

    } elseif (
        !is_numeric($score) ||
        $score < 0 ||
        $score > 100
    ) {

        $error = "Score must be a number between 0 and 100.";

    } else {

        $score = (float) $score;

        /*calculate grade */

        if ($score >= 70) {

            $grade = "A";
            $remark = "Excellent";

        } elseif ($score >= 60) {

            $grade = "B";
            $remark = "Very Good";

        } elseif ($score >= 50) {

            $grade = "C";
            $remark = "Good";

        } elseif ($score >= 45) {

            $grade = "D";
            $remark = "Pass";

        } elseif ($score >= 40) {

            $grade = "E";
            $remark = "Pass";

        } else {

            $grade = "F";
            $remark = "Fail";
        }


        /*insert result */

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO results
            (
                student_id,
                course_code,
                course_title,
                semester,
                session,
                score,
                grade,
                remark
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );


        if ($stmt) {

            mysqli_stmt_bind_param(
                $stmt,
                "issssdss",
                $student_id,
                $course_code,
                $course_title,
                $semester,
                $session,
                $score,
                $grade,
                $remark
            );


            if (mysqli_stmt_execute($stmt)) {

                mysqli_stmt_close($stmt);

                header(
                    "Location: student_profile.php?id="
                    . $student_id
                    . "#results"
                );

                exit();

            } else {

                $error = "Unable to save result.";

                mysqli_stmt_close($stmt);
            }

        } else {

            $error = "Unable to prepare result query.";
        }
    }
}


/* admin name */

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0" >
    <title> Add Result - Student Record Management System</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="admin-layout">


    <!--side bar-->
    <aside class="admin-sidebar">
        <div class="admin-brand">
            <div class="brand-mark">
                <svg
                    viewBox="0 0 24 24" width="24"
                    height="24"fill="none"
                    stroke="white" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path  d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
                </svg>
            </div>
            <div class="brand-text">
                <h2> Student Record</h2>
                <span> Management System </span>
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
                        class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>"
                    >

                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" 
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" 
                                stroke-linejoin="round">
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

                    <a
                        href="add_student.php"
                        class="<?php echo ($current_page == 'add_student.php') ? 'active' : ''; ?>"
                    >

                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
                    <a href="view_students.php"
                        class="<?php echo ($current_page == 'view_students.php') ? 'active' : ''; ?>">

                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" 
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round">
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

                    <a href="results.php"
                      class="<?php echo ($current_page == 'results.php') ? 'active' : ''; ?>">

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

                    <li>

                    <a href="search_student.php"
                        class="<?php echo ($current_page == 'search_student.php') ? 'active' : ''; ?>">
                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" 
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" 
                                    stroke-linejoin="round">
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
            <div class="navigation-title navigation-bottom-title"> ACCOUNT</div>
            <ul>
                <li>
                    <a href="logout.php"> <span> Logout </span></a>
                </li>
            </ul>
        </nav>
        <div class="sidebar-footer">
            <strong> Student Record Management System</strong>
            <span> Administration Panel </span>
        </div>
    </aside>

    <!--main -->
    <main class="admin-main">
        <header class="admin-topbar">
            <div>

                <p class="topbar-label">
                    STUDENT RESULTS
                </p>

                <h1>
                    Add Result
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



        <!-- =====================================================
             FORM
        ====================================================== -->

        <section class="result-form-page">


            <div class="result-form-header">

                <span>
                    RESULT ENTRY
                </span>

                <h2>
                    Add Student Result
                </h2>

                <p>
                    Enter the academic result for this student.
                </p>

            </div>



            <!-- STUDENT -->

            <div class="result-student-summary">

                <div class="result-student-avatar">

                    <?php
                    echo strtoupper(
                        substr(
                            $student['firstname'],
                            0,
                            1
                        )
                    );
                    ?>

                </div>


                <div>

                    <strong>

                        <?php
                        echo htmlspecialchars(
                            $student['firstname']
                            . " "
                            . $student['lastname']
                        );
                        ?>

                    </strong>

                    <span>

                        Matric No:
                        <?php
                        echo htmlspecialchars(
                            $student['matric_no']
                        );
                        ?>

                    </span>

                </div>

            </div>



            <?php if (!empty($error)): ?>

                <div class="result-error">

                    <?php
                    echo htmlspecialchars($error);
                    ?>

                </div>

            <?php endif; ?>



            <form
                method="POST"
                action=""
                class="result-form"
            >


                <div class="result-form-grid">


                    <!-- COURSE CODE -->

                    <div class="result-form-group">

                        <label for="course_code">
                            Course Code
                        </label>

                        <input
                            type="text"
                            id="course_code"
                            name="course_code"
                            value="<?php echo htmlspecialchars($course_code); ?>"
                            placeholder="e.g. CSC301"
                            required
                        >

                    </div>



                    <!-- COURSE TITLE -->

                    <div class="result-form-group">

                        <label for="course_title">
                            Course Title
                        </label>

                        <input
                            type="text"
                            id="course_title"
                            name="course_title"
                            value="<?php echo htmlspecialchars($course_title); ?>"
                            placeholder="e.g. Software Engineering"
                            required
                        >

                    </div>



                    <!-- SEMESTER -->

                    <div class="result-form-group">

                        <label for="semester">
                            Semester
                        </label>

                        <select
                            id="semester"
                            name="semester"
                            required
                        >

                            <option value="">
                                Select Semester
                            </option>

                            <option
                                value="First Semester"
                                <?php
                                echo $semester === "First Semester"
                                    ? "selected"
                                    : "";
                                ?>
                            >
                                First Semester
                            </option>

                            <option
                                value="Second Semester"
                                <?php
                                echo $semester === "Second Semester"
                                    ? "selected"
                                    : "";
                                ?>
                            >
                                Second Semester
                            </option>

                        </select>

                    </div>



                    <!-- SESSION -->

                    <div class="result-form-group">

                        <label for="session">
                            Academic Session
                        </label>

                        <input
                            type="text"
                            id="session"
                            name="session"
                            value="<?php echo htmlspecialchars($session); ?>"
                            placeholder="e.g. 2025/2026"
                            required
                        >

                    </div>



                    <!-- SCORE -->

                    <div class="result-form-group">

                        <label for="score">
                            Score
                        </label>

                        <input
                            type="number"
                            id="score"
                            name="score"
                            value="<?php echo htmlspecialchars($score); ?>"
                            min="0"
                            max="100"
                            step="0.01"
                            placeholder="Enter score"
                            required
                        >

                        <small>
                            Enter a score between 0 and 100.
                        </small>

                    </div>


                </div>



                <div class="result-grade-info">

                    <strong>
                        Grading Scale
                    </strong>

                    <span>
                        70–100 A
                    </span>

                    <span>
                        60–69 B
                    </span>

                    <span>
                        50–59 C
                    </span>

                    <span>
                        45–49 D
                    </span>

                    <span>
                        40–44 E
                    </span>

                    <span>
                        0–39 F
                    </span>

                </div>



                <div class="result-form-actions">

                    <a
                        href="student_profile.php?id=<?php echo $student_id; ?>"
                        class="student-profile-btn back-profile-btn"
                    >
                        Cancel
                    </a>


                    <button
                        type="submit"
                        class="student-profile-btn edit-profile-btn"
                    >
                        Save Result
                    </button>

                </div>


            </form>


        </section>


    </main>


</div>


</body>

</html>