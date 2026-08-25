<?php

session_start();

if (!isset($_SESSION['admin'])) {

    header("Location: login.php");

    exit();
}

include "config/database.php";


$success = "";
$error = "";


/* =========================================================
   ADD STUDENT
========================================================= */

if (isset($_POST['add_student'])) {


    $matric_no = trim($_POST['matric_no']);

    $firstname = trim($_POST['firstname']);

    $lastname = trim($_POST['lastname']);

    $gender = trim($_POST['gender']);

    $department = trim($_POST['department']);

    $level = trim($_POST['level']);

    $phone = trim($_POST['phone']);

    $email = trim($_POST['email']);

    $passport = "";


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
       SECURE VALUES
    ====================================================== */

    $matric_no_safe =
        mysqli_real_escape_string(
            $conn,
            $matric_no
        );

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
            $department
        );

    $level_safe =
        mysqli_real_escape_string(
            $conn,
            $level
        );

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


    /* =====================================================
       INSERT STUDENT
    ====================================================== */

    $sql = "
        INSERT INTO students
        (
            matric_no,
            firstname,
            lastname,
            gender,
            department,
            level,
            phone,
            email,
            passport
        )

        VALUES
        (
            '$matric_no_safe',
            '$firstname_safe',
            '$lastname_safe',
            '$gender_safe',
            '$department_safe',
            '$level_safe',
            '$phone_safe',
            '$email_safe',
            '$passport_safe'
        )
    ";


    if (mysqli_query($conn, $sql)) {


        /* =================================================
           SEND EMAIL
        ================================================== */

        if (file_exists("send_email.php")) {

            require_once "send_email.php";

            if (function_exists("sendStudentEmail")) {

                sendStudentEmail(
                    $email,
                    $firstname . " " . $lastname
                );

            }

        }


        $success =
            "Student added successfully!";


    } else {


        $error =
            "Failed to add student. Please try again.";

    }

}

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Student - Student Record Management System</title>

    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="assets/css/students.css">

</head>


<body>


<div class="admin-layout">


    <!--sidebar-->

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

                    <a
                        href="add_student.php"
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

                    <a href="search_student.php">

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


                                <!-- MATRIC NUMBER -->

                                <div class="form-group">

                                    <label>
                                        Matric Number
                                        <span class="required">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        name="matric_no"
                                        placeholder="e.g. NCSF/23/0025"
                                        required
                                    >

                                </div>

                            </div>



                            <!-- =================================================
                                 ACADEMIC INFORMATION
                            ================================================== -->

                            <div class="form-section-title">
                                Academic Information
                            </div>


                            <div class="student-form-grid">


                                <!-- DEPARTMENT -->

                                <div class="form-group">

                                    <label>
                                        Department
                                        <span class="required">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        name="department"
                                        placeholder="e.g. Computer Science"
                                        required
                                    >

                                </div>


                                <!-- LEVEL -->

                                <div class="form-group">

                                    <label>
                                        Level
                                        <span class="required">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        name="level"
                                        placeholder="e.g. ND II"
                                        required
                                    >

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
                                        <span class="required">*</span>
                                    </label>

                                    <input
                                        type="email"
                                        name="email"
                                        placeholder="e.g. student@gmail.com"
                                        required
                                    >

                                </div>

                            </div>



                            <!-- =================================================
                                 ACTIONS
                            ================================================== -->

                            <div class="student-form-actions">


                                <a
                                    href="view_students.php"
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


</body>
</html>