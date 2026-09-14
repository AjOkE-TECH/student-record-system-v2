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

if (
    !isset($_GET['student_id']) ||
    empty($_GET['student_id'])
) {
    header("Location: result");
    exit();
}

$student_id = (int) $_GET['student_id'];


/* =========================================================
   GET STUDENT
========================================================= */

$student_query = mysqli_query(
    $conn,
    "SELECT
        id,
        firstname,
        lastname,
        matric_no
     FROM students
     WHERE id = $student_id"
);


if (
    !$student_query ||
    mysqli_num_rows($student_query) == 0
) {
    header("Location: result");
    exit();
}


$student = mysqli_fetch_assoc(
    $student_query
);


/* =========================================================
   FORM VARIABLES
========================================================= */

$error = "";

$session_id = "";
$course_id = "";
$course_code = "";
$course_title = "";
$semester = "";
$score = "";


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
   GET ALL ACTIVE SESSIONS
========================================================= */

$sessions_query = mysqli_query(
    $conn,
    "SELECT id, session_name
     FROM sessions
     ORDER BY session_name DESC"
);


/* =========================================================
   GET ALL ACTIVE COURSES (for JavaScript cascade)
========================================================= */

$all_courses_query = mysqli_query(
    $conn,
    "SELECT
        courses.id,
        courses.course_code,
        courses.course_title,
        courses.session_id
     FROM courses
     WHERE courses.archived_at IS NULL
     ORDER BY courses.course_code ASC
     LIMIT 500"
);

$all_courses = [];
if ($all_courses_query) {
    while ($c = mysqli_fetch_assoc($all_courses_query)) {
        $c['id'] = (int) $c['id'];
        $c['session_id'] = (int) $c['session_id'];
        $all_courses[] = $c;
    }
}


/* =========================================================
   PROCESS FORM
========================================================= */

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
) {

    $session_id = (int) ($_POST['session_id'] ?? 0);

    $course_id = (int) ($_POST['course_id'] ?? 0);

    $course_code = trim(
        $_POST['course_code'] ?? ""
    );

    $course_title = trim(
        $_POST['course_title'] ?? ""
    );

    $semester = trim(
        $_POST['semester'] ?? ""
    );

    $score = trim(
        $_POST['score'] ?? ""
    );


    /* =====================================================
       VALIDATION
    ===================================================== */

    if (
        $session_id <= 0 ||
        $course_id <= 0 ||
        $semester === "" ||
        $score === ""
    ) {

        $error =
            "Please fill in all result fields.";

    }

    elseif (
        !is_numeric($score) ||
        $score < 0 ||
        $score > 100
    ) {

        $error =
            "Score must be a number between 0 and 100.";

    }

    else {

        $score = (float) $score;


        /* =================================================
           VERIFY COURSE EXISTS + BELONGS TO SESSION
        ================================================= */

        $course_check = mysqli_query(
            $conn,
            "SELECT
                courses.id,
                courses.course_code,
                courses.course_title,
                sessions.session_name
             FROM courses
             INNER JOIN sessions
                ON courses.session_id = sessions.id
             WHERE courses.id = $course_id
               AND courses.session_id = $session_id
               AND courses.archived_at IS NULL"
        );

        if (
            !$course_check ||
            mysqli_num_rows($course_check) == 0
        ) {

            $error =
                "Selected course does not exist in the chosen session.";

        } else {

            $course_data = mysqli_fetch_assoc($course_check);

            $course_code = $course_data['course_code'];
            $course_title = $course_data['course_title'];
            $session = $course_data['session_name'];


            /* =================================================
               CALCULATE GRADE
            ================================================= */

            if ($score >= 70) {

                $grade = "A";
                $remark = "Excellent";

            }

            elseif ($score >= 60) {

                $grade = "B";
                $remark = "Very Good";

            }

            elseif ($score >= 50) {

                $grade = "C";
                $remark = "Good";

            }

            elseif ($score >= 45) {

                $grade = "D";
                $remark = "Pass";

            }

            elseif ($score >= 40) {

                $grade = "E";
                $remark = "Pass";

            }

            else {

                $grade = "F";
                $remark = "Fail";

            }


            /* =================================================
               INSERT RESULT
            ================================================= */

            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO results
                (
                    student_id,
                    course_id,
                    course_code,
                    course_title,
                    semester,
                    session,
                    score,
                    grade,
                    remark
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );


            if (!$stmt) {

                $error =
                    "Unable to prepare result query: "
                    . mysqli_error($conn);

            }

            else {


                mysqli_stmt_bind_param(
                    $stmt,
                    "iissssdss",
                    $student_id,
                    $course_id,
                    $course_code,
                    $course_title,
                    $semester,
                    $session,
                    $score,
                    $grade,
                    $remark
                );


                if (
                    mysqli_stmt_execute($stmt)
                ) {

                    mysqli_stmt_close($stmt);


                    header(
                        "Location: student_profile?id="
                        . $student_id
                        . "#results"
                    );

                    exit();

                }


                else {

                    $error =
                        "Unable to save result: "
                        . mysqli_stmt_error($stmt);

                    mysqli_stmt_close($stmt);

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
    <meta name="viewport"content="width=device-width, initial-scale=1.0">
    <title> Add Result - Student Record Management System</title>
    <link rel="stylesheet"href="assets/css/admin.css">
    <link rel="stylesheet" href="assets/css/style.css" >
</head>

<body>
<div class="admin-layout">


    <!--sidebar-->
    <aside class="admin-sidebar">
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


        <nav class="admin-navigation">
            <div class="navigation-title"> MAIN MENU</div>

            <ul>
                <li>
                    <a href="dashboard">
                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                        </span>
                        <span> Dashboard </span>
                    </a>
                </li>

                <li>
                    <a href="add_student">
                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="7" r="4"/><path d="M3 21v-2a6 6 0 0 1 12 0v2"/><path d="M15 3h6"/><path d="M18 0v6"/></svg>
                        </span>
                        <span> Add Student </span>
                    </a>
                </li>

                <li>
                    <a href="view_students">
                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="7" r="4"/><path d="M3 21v-2a6 6 0 0 1 12 0v2"/><circle cx="17" cy="9" r="3"/><path d="M21 21v-2a4 4 0 0 0-7.5-1.5"/></svg>
                        </span>
                        <span> View Students </span>
                    </a>
                </li>

                <li>
                    <a href="archive_students">
                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="5" rx="1"/><path d="M4 8v12a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V8"/><path d="M10 12h4"/></svg>
                        </span>
                        <span> Archive </span>
                    </a>
                </li>

                <li>
                    <a
                        href="result"
                        class="active"
                    >
                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16v16H4z"/><path d="M8 16v-4"/><path d="M12 16V8"/><path d="M16 16v-7"/></svg>
                        </span>
                        <span> Results </span>
                    </a>
                </li>

<li>
                    <a href="courses">
                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                        </span>
                        <span> Courses </span>
                    </a>
                </li>
                <li>
                    <a href="sessions">
                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </span>
                        <span> Sessions </span>
                    </a>
                </li>
                <li>
                    <a href="search_student">
                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
                        </span>
                        <span> Search Student </span>
                    </a>
                </li>
            </ul>


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
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        </span>
                        <span> Logout </span>
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



        <!-- =====================================================
             RESULT FORM
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



            <!-- STUDENT SUMMARY -->

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



            <?php

            if (!empty($error)) {

            ?>

                <div class="result-error">

                    <?php

                    echo htmlspecialchars(
                        $error
                    );

                    ?>

                </div>

            <?php

            }

            ?>



            <form
                method="POST"
                action=""
                class="result-form"
                id="addResultForm"
            >


                <div class="result-form-grid">


                    <!-- SESSION (DROPDOWN) -->

                    <div class="result-form-group">

                        <label for="session_id">
                            Academic Session
                        </label>

                        <select
                            id="session_id"
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
                                    $sess_row =
                                    mysqli_fetch_assoc($sessions_query)
                                ):
                            ?>

                                <option
                                    value="<?php echo (int)$sess_row['id']; ?>"
                                    <?php echo ($session_id == (int)$sess_row['id']) ? 'selected' : ''; ?>
                                >
                                    <?php
                                    echo htmlspecialchars(
                                        $sess_row['session_name']
                                    );
                                    ?>
                                </option>

                            <?php
                                endwhile;
                            endif;
                            ?>

                        </select>

                    </div>



                    <!-- COURSE (DROPDOWN, auto-populated) -->

                    <div class="result-form-group">

                        <label for="course_id">
                            Course
                        </label>

                        <select
                            id="course_id"
                            name="course_id"
                            required
                        >

                            <option value="">
                                Select a session first
                            </option>

                        </select>

                    </div>



                    <!-- COURSE CODE (hidden, auto-filled) -->

                    <input
                        type="hidden"
                        id="course_code"
                        name="course_code"
                        value="<?php echo htmlspecialchars($course_code); ?>"
                    >



                    <!-- COURSE TITLE (hidden, auto-filled) -->

                    <input
                        type="hidden"
                        id="course_title"
                        name="course_title"
                        value="<?php echo htmlspecialchars($course_title); ?>"
                    >



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



                <!-- GRADING SCALE -->

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



                <!-- ACTIONS -->

                <div class="result-form-actions">


                    <a
                        href="student_profile?id=<?php echo $student_id; ?>"
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


<script>

    (function () {

        var allCourses = <?php echo json_encode($all_courses); ?>;

        var sessionSelect = document.getElementById('session_id');

        var courseSelect = document.getElementById('course_id');

        var courseCodeInput = document.getElementById('course_code');

        var courseTitleInput = document.getElementById('course_title');


        function loadCourses() {

            var sessionVal = parseInt(sessionSelect.value, 10);

            courseSelect.innerHTML = '';


            if (!sessionVal || sessionVal <= 0) {

                var opt = document.createElement('option');

                opt.value = '';

                opt.textContent = 'Select a session first';

                courseSelect.appendChild(opt);

                courseCodeInput.value = '';

                courseTitleInput.value = '';

                return;

            }


            var optPlaceholder = document.createElement('option');

            optPlaceholder.value = '';

            optPlaceholder.textContent = 'Select Course';

            courseSelect.appendChild(optPlaceholder);


            var count = 0;

            for (var i = 0; i < allCourses.length; i++) {

                if (allCourses[i].session_id === sessionVal) {

                    var opt = document.createElement('option');

                    opt.value = allCourses[i].id;

                    opt.textContent = allCourses[i].course_code + ' - ' + allCourses[i].course_title;

                    courseSelect.appendChild(opt);

                    count++;

                }

            }


            if (count === 0) {

                var optEmpty = document.createElement('option');

                optEmpty.value = '';

                optEmpty.textContent = 'No courses for this session';

                courseSelect.appendChild(optEmpty);

            }

        }


        function onCourseChange() {

            var selectedVal = parseInt(courseSelect.value, 10);

            if (!selectedVal) {

                courseCodeInput.value = '';

                courseTitleInput.value = '';

                return;

            }


            for (var i = 0; i < allCourses.length; i++) {

                if (allCourses[i].id === selectedVal) {

                    courseCodeInput.value = allCourses[i].course_code;

                    courseTitleInput.value = allCourses[i].course_title;

                    return;

                }

            }

        }


        sessionSelect.addEventListener('change', function () {

            courseSelect.value = '';

            courseCodeInput.value = '';

            courseTitleInput.value = '';

            loadCourses();

        });


        courseSelect.addEventListener('change', onCourseChange);


        /* Populate on page load if a session is already selected */
        loadCourses();

        <?php if ($course_id != ""): ?>
            courseSelect.value = "<?php echo (int)$course_id; ?>";
            onCourseChange();
        <?php endif; ?>

    })();

</script>


</body>

</html>