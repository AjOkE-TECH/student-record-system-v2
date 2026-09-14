<?php

session_start();

if (!isset($_SESSION['admin'])) {

    header("Location: login");

    exit();
}

include "config/database.php";
include "config/matric.php";


/* ---------------------------------------------------------
   DEPARTMENTS FOR THE DROPDOWN
--------------------------------------------------------- */

$departments_query = mysqli_query(
    $conn,
    "SELECT id, department_name, department_code
     FROM departments
     ORDER BY FIELD(
        UPPER(department_name),
        'COMPUTER SCIENCE',
        'SCIENCE LABORATORY TECHNOLOGY',
        'ACCOUNTANCY',
        'MARKETING',
        'BUSINESS ADMINISTRATION'
     ), department_name ASC"
);


$success = "";
$error = "";
$email_warning = "";
$email_info = "";


/* =========================================================
   ADD STUDENT
========================================================= */

if (isset($_POST['add_student'])) {

    $firstname = trim($_POST['firstname']);

    $lastname = trim($_POST['lastname']);

    $gender = trim($_POST['gender']);

    $department_id = (int) ($_POST['department'] ?? 0);

    $level = trim($_POST['level']);

    $admission_year = $_POST['admission_year'] ?? date('Y');

    $phone = trim($_POST['phone']);

    $email = trim($_POST['email']);

    $passport = "";

    $email_ok = false;


    /* =====================================================
       PASSPORT UPLOAD
    ====================================================== */

    if (
        isset($_FILES['passport']) &&
        $_FILES['passport']['error'] === 0
    ) {


        $folder =
            "assets/upload/students/";


        /* Make sure the folder exists */

        if (!is_dir($folder)) {

            mkdir(
                $folder,
                0777,
                true
            );
        }


        $original_name =
            basename(
                $_FILES['passport']['name']
            );


        $file_extension =
            strtolower(
                pathinfo(
                    $original_name,
                    PATHINFO_EXTENSION
                )
            );


        $allowed_extensions = [
            'jpg',
            'jpeg',
            'png',
            'webp'
        ];


        if (
            in_array(
                $file_extension,
                $allowed_extensions
            )
        ) {


            $fileName =
                time()
                . "_"
                . uniqid()
                . "."
                . $file_extension;


            $target =
                $folder . $fileName;


            if (
                move_uploaded_file(
                    $_FILES['passport']['tmp_name'],
                    $target
                )
            ) {

                $passport = $fileName;

            }

        }

    }


    /* =====================================================
       VALIDATION
    ====================================================== */

    $department =
        srsGetDepartmentById(
            $conn,
            $department_id
        );

    if (!$department) {

        $error =
            "Please select a valid department.";

    } elseif (
        !in_array(
            $level,
            array('ND I', 'ND II', 'HND I', 'HND II'),
            true
        )
    ) {

        $error =
            "Level must be one of: ND I, ND II, HND I, HND II.";

    } else {

        /* Admission year is full-year (e.g. 2026, never 26). */
        $admission_year =
            srsValidateAdmissionYear(
                $admission_year
            );

        if ($admission_year === false) {

            $error =
                "Please enter a valid admission year (full year, e.g. 2026).";

        } else {

            /* =============================================
               TRANSACTION: generate matric + insert student
               The matric number is generated SERVER-SIDE
               using a serialized atomic sequence, so two
               simultaneous admins can never receive the same
               number.
            ============================================== */

            mysqli_begin_transaction($conn);

            $generation_error = "";

            $matric_no =
                srsAutoMatric(
                    $conn,
                    $department['id'],
                    $admission_year,
                    $generation_error
                );

            if ($generation_error !== "") {

                mysqli_rollback($conn);

                $error = $generation_error;

            } else {

                $firstname_safe =
                    mysqli_real_escape_string(
                        $conn,
                        $firstname
                    );

                $lastname_safe =
                    mysqli_real_escape_string(
                        $conn,
                        $lastname
                    );

                $gender_safe =
                    mysqli_real_escape_string(
                        $conn,
                        $gender
                    );

                $department_safe =
                    mysqli_real_escape_string(
                        $conn,
                        $department['department_name']
                    );

                $level_safe =
                    mysqli_real_escape_string(
                        $conn,
                        $level
                    );

                $admission_year_safe =
                    (int) $admission_year;

                $phone_safe =
                    mysqli_real_escape_string(
                        $conn,
                        $phone
                    );

                $email_safe =
                    mysqli_real_escape_string(
                        $conn,
                        $email
                    );

                $passport_safe =
                    mysqli_real_escape_string(
                        $conn,
                        $passport
                    );


                $sql = "
                    INSERT INTO students
                    (
                        matric_no,
                        firstname,
                        lastname,
                        gender,
                        department,
                        level,
                        admission_year,
                        phone,
                        email,
                        passport
                    )

                    VALUES
                    (
                        '$matric_no',
                        '$firstname_safe',
                        '$lastname_safe',
                        '$gender_safe',
                        '$department_safe',
                        '$level_safe',
                        $admission_year_safe,
                        '$phone_safe',
                        '$email_safe',
                        '$passport_safe'
                    )
                ";

                if (mysqli_query($conn, $sql)) {

                    mysqli_commit($conn);


                    /* =====================================
                       SEND EMAIL
                       The student is already committed and
                       saved. A failed email NEVER deletes or
                       rolls back the student record.
                    ====================================== */

                    if ($email !== "") {

                        if (file_exists("send_email.php")) {

                            require_once "send_email.php";

                            if (function_exists("sendStudentEmail")) {

                                $email_result = sendStudentEmail(
                                    $email,
                                    $firstname . " " . $lastname,
                                    $matric_no,
                                    $department['department_name'],
                                    $level,
                                    (string) $admission_year
                                );

                                if (is_array($email_result)) {

                                    if ($email_result['success'] === true) {

                                        $email_ok = true;

                                    } else {

                                        $email_warning =
                                            "The student was added, but the welcome email could not be sent."
                                            . " Reason: "
                                            . $email_result['message']
                                            . " (Student record was saved as requested.)";

                                    }

                                }

                            }

                        } else {

                            $email_warning =
                                "The student was added, but the welcome email could not be sent."
                                . " The mail library file (send_email.php) is missing.";

                        }

                    } else {

                        $email_info =
                            "No welcome email was sent because no email address was provided.";

                    }


                    $success =
                        "Student added successfully! Matric Number: "
                        . $matric_no;

                    if ($email_ok) {
                        $success .= " Welcome email sent to " . $email . ".";
                    }


                } else {

                    mysqli_rollback($conn);


                    if (
                        mysqli_errno($conn) === 1062
                    ) {

                        $error =
                            "A matching matric number already exists. Please try again.";

                    } else {

                        $error =
                            "Failed to add student. Please try again.";

                    }

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

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Student - Student Record Management System</title>

    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="assets/css/students.css">

</head>


<body>


<div class="admin-layout">


    <!--sidebar-->

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

                    <a
                        href="add_student"
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

                    <a href="view_students">

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

                    <a href="archive_students">

                        <span class="nav-icon">

                            <svg
                                viewBox="0 0 24 24"
                                width="18"
                                height="18"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >

                                <rect x="2" y="3" width="20" height="5" rx="1"/>
                                <path d="M4 8v12a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V8"/>
                                <path d="M10 12h4"/>

                            </svg>

                        </span>

                        <span>Archive</span>

                    </a>

                </li>


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
                            >

                                <path d="M4 4h16v16H4z"/>
                                <path d="M8 16v-4"/>
                                <path d="M12 16V8"/>
                                <path d="M16 16v-7"/>

                            </svg>

                        </span>

                        <span>Results</span>

                    </a>

                </li>


                <li>

                    <a href="courses">

                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                        </span>

                        <span>
                            Courses
                        </span>

                    </a>

                </li>


                <li>

                    <a href="sessions">

                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </span>

                        <span>
                            Sessions
                        </span>

                    </a>

                </li>


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
    ===================================================== -->

    <main class="admin-main">


        <header class="admin-topbar">

            <div>

                <div class="topbar-label">
                    STUDENT RECORDS
                </div>

                <h1>
                    Add Student
                </h1>

            </div>

        </header>


        <div class="page-content">


            <div class="add-student-page">


                <!-- PAGE INTRO -->

                <div class="student-page-header">

                    <p>
                        Enter the student's information below to create a new record.
                    </p>

                </div>



                <!-- SUCCESS -->

                <?php if (!empty($success)) { ?>

                    <div class="student-message success">

                        <?php
                        echo htmlspecialchars($success);
                        ?>

                    </div>

                <?php } ?>



                <!-- ERROR -->

                <?php if (!empty($error)) { ?>

                    <div class="student-message error">

                        <?php
                        echo htmlspecialchars($error);
                        ?>

                    </div>

                <?php } ?>



                <!-- EMAIL WARNING -->

                <?php if (!empty($email_warning)) { ?>

                    <div class="student-message warning">

                        <?php
                        echo htmlspecialchars($email_warning);
                        ?>

                    </div>

                <?php } ?>



                <!-- EMAIL INFO -->

                <?php if (!empty($email_info)) { ?>

                    <div class="student-message info">

                        <?php
                        echo htmlspecialchars($email_info);
                        ?>

                    </div>

                <?php } ?>



                <!-- FORM CARD -->

                <div class="student-form-card">


                    <div class="student-form-header">

                        <h2>
                            Student Information
                        </h2>

                        <p>
                            Complete all required fields to register the student.
                        </p>

                    </div>


                    <form
                        method="POST"
                        class="student-form"
                        enctype="multipart/form-data"
                    >


                        <div class="student-form-body">


                            <!-- =================================================
                                 PASSPORT FIRST
                            ================================================== -->

                            <div class="form-section-title">
                                Student Passport
                            </div>


                            <div class="passport-section">


                                <div class="passport-preview" id="passportPreview">

                                    <div class="passport-placeholder">

                                        <svg
                                            viewBox="0 0 24 24"
                                            width="32"
                                            height="32"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.5"
                                        >

                                            <rect
                                                x="3"
                                                y="3"
                                                width="18"
                                                height="18"
                                                rx="2"
                                            />

                                            <circle
                                                cx="9"
                                                cy="9"
                                                r="2"
                                            />

                                            <path
                                                d="M21 15l-5-5L5 21"
                                            />

                                        </svg>

                                        No photo

                                    </div>

                                </div>


                                <div class="passport-content">

                                    <h3>
                                        Upload Student Passport
                                    </h3>

                                    <p>
                                        Upload a clear passport photograph of the student.
                                        JPG, PNG format is supported.
                                    </p>


                                    <label class="passport-upload">

                                        <svg
                                            viewBox="0 0 24 24"
                                            width="17"
                                            height="17"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >

                                            <path d="M12 3v12"/>

                                            <path d="m7 8 5-5 5 5"/>

                                            <path d="M5 21h14"/>

                                        </svg>

                                        Choose Passport

                                        <input
                                            type="file"
                                            name="passport"
                                            id="passport"
                                            accept="image/jpeg,image/png,image/webp"
                                        >

                                    </label>

                                </div>

                            </div>



                            <!-- =================================================
                                 PERSONAL INFORMATION
                            ================================================== -->

                            <div class="form-section-title">
                                Personal Information
                            </div>


                            <div class="student-form-grid">


                                <!-- FIRST NAME -->

                                <div class="form-group">

                                    <label>
                                        First Name
                                        <span class="required">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        name="firstname"
                                        placeholder="e.g. Sekinat"
                                        required
                                    >

                                </div>


                                <!-- LAST NAME -->

                                <div class="form-group">

                                    <label>
                                        Last Name
                                        <span class="required">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        name="lastname"
                                        placeholder="e.g. Mutolib"
                                        required
                                    >

                                </div>


                                <!-- GENDER -->

                                <div class="form-group">

                                    <label>
                                        Gender
                                        <span class="required">*</span>
                                    </label>

                                    <select
                                        name="gender"
                                        required
                                    >

                                        <option value="">
                                            Select Gender
                                        </option>

                                        <option value="Male">
                                            Male
                                        </option>

                                        <option value="Female">
                                            Female
                                        </option>

                                    </select>

                                </div>

                            </div>



                            <!-- =================================================
                                 ACADEMIC INFORMATION
                            ================================================== -->

                            <div class="form-section-title">
                                Academic Information
                            </div>


                            <div class="student-form-grid">


                                <!-- DEPARTMENT (DROPDOWN) -->

                                <div class="form-group">

                                    <label>
                                        Department
                                        <span class="required">*</span>
                                    </label>

                                    <select
                                        name="department"
                                        id="department"
                                        required
                                    >

                                        <option value="">
                                            Select Department
                                        </option>

                                        <?php
                                        if (
                                            $departments_query &&
                                            mysqli_num_rows($departments_query) > 0
                                        ):
                                            while (
                                                $dept =
                                                mysqli_fetch_assoc($departments_query)
                                            ):
                                        ?>

                                            <option
                                                value="<?php echo (int) $dept['id']; ?>"
                                            >
                                                <?php echo htmlspecialchars($dept['department_name']); ?>
                                            </option>

                                        <?php
                                            endwhile;
                                        endif;
                                        ?>

                                    </select>

                                </div>


                                <!-- LEVEL (DROPDOWN) -->

                                <div class="form-group">

                                    <label>
                                        Level
                                        <span class="required">*</span>
                                    </label>

                                    <select
                                        name="level"
                                        id="level"
                                        required
                                    >

                                        <option value="">
                                            Select Level
                                        </option>

                                        <option value="ND I">
                                            ND I
                                        </option>

                                        <option value="ND II">
                                            ND II
                                        </option>

                                        <option value="HND I">
                                            HND I
                                        </option>

                                        <option value="HND II">
                                            HND II
                                        </option>

                                    </select>

                                </div>


                                <!-- ADMISSION YEAR -->

                                <div class="form-group">

                                    <label>
                                        Admission Year
                                        <span class="required">*</span>
                                    </label>

                                    <input
                                        type="number"
                                        name="admission_year"
                                        id="admission_year"
                                        value="<?php echo date('Y'); ?>"
                                        min="1950"
                                        max="2100"
                                        required
                                    >

                                    <small class="field-hint">
                                        Full year, e.g. 2026.
                                    </small>

                                </div>

                            </div>



                            <!-- =================================================
                                 CONTACT INFORMATION
                            ================================================== -->

                            <div class="form-section-title">
                                Contact Information
                            </div>


                            <div class="student-form-grid">


                                <!-- PHONE -->

                                <div class="form-group">

                                    <label>
                                        Phone Number
                                    </label>

                                    <input
                                        type="text"
                                        name="phone"
                                        placeholder="e.g. 07051716653"
                                    >

                                </div>


                                <!-- EMAIL -->

                                <div class="form-group">

                                    <label>
                                        Email Address
                                    </label>

                                    <input
                                        type="email"
                                        name="email"
                                        placeholder="e.g. student@gmail.com"
                                    >

                                </div>

                            </div>



                            <!-- =================================================
                                 ACTIONS
                            ================================================== -->

                            <div class="student-form-actions">


                                <a
                                    href="view_students"
                                    class="cancel-student"
                                >
                                    Cancel
                                </a>


                                <button
                                    type="submit"
                                    name="add_student"
                                    class="add-student-button"
                                >

                                    <svg
                                        viewBox="0 0 24 24"
                                        width="17"
                                        height="17"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >

                                        <circle cx="12" cy="12" r="9"/>

                                        <path d="M12 8v8"/>

                                        <path d="M8 12h8"/>

                                    </svg>

                                    Add Student

                                </button>


                            </div>


                        </div>

                    </form>

                </div>

            </div>


        </div>



        <!-- FOOTER -->

        <footer class="admin-footer">

            <span>SRMS</span>

            <span class="footer-dot">Â·</span>

            <span>
                Student Record Management System
            </span>

            <span class="footer-dot">Â·</span>

            <span>
                &copy; <?php echo date('Y'); ?>
            </span>

        </footer>


    </main>

</div>



<!-- =====================================================
     PASSPORT PREVIEW
====================================================== -->

<script>

    const passportInput =
        document.getElementById("passport");

    const passportPreview =
        document.getElementById("passportPreview");


    passportInput.addEventListener("change", function () {

        const file = this.files[0];

        if (!file) {
            return;
        }


        if (!file.type.startsWith("image/")) {
            return;
        }


        const reader = new FileReader();


        reader.onload = function (event) {

            passportPreview.innerHTML = `
                <img
                    src="${event.target.result}"
                    alt="Passport Preview"
                >
            `;

        };


        reader.readAsDataURL(file);

    });

</script>


<script src="assets/js/sidebar.js"></script>

</body>
</html>