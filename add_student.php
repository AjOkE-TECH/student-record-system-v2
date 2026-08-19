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

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Add Student - Student Record Management System
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

                    <a
                        href="add_student.php"
                        class="active"
                    >
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


        <section class="admin-page-header">

            <h1>
                Add Student
            </h1>

            <p>
                Register a new student in the system.
            </p>

        </section>



        <?php if (!empty($success)) { ?>

            <div class="success">

                <?php
                echo htmlspecialchars($success);
                ?>

            </div>

        <?php } ?>



        <?php if (!empty($error)) { ?>

            <div class="error">

                <?php
                echo htmlspecialchars($error);
                ?>

            </div>

        <?php } ?>



        <!-- =================================================
             FORM
        ================================================== -->

        <form
            method="POST"
            class="student-form"
            enctype="multipart/form-data"
        >


            <!-- MATRIC + FIRST NAME -->

            <div class="form-row">


                <div class="form-group">

                    <label>
                        Matric Number
                    </label>

                    <input
                        type="text"
                        name="matric_no"
                        placeholder="e.g. NCSF/23/0025"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        First Name
                    </label>

                    <input
                        type="text"
                        name="firstname"
                        placeholder="e.g. Sekinat"
                        required
                    >

                </div>


            </div>



            <!-- LAST NAME + GENDER -->

            <div class="form-row">


                <div class="form-group">

                    <label>
                        Last Name
                    </label>

                    <input
                        type="text"
                        name="lastname"
                        placeholder="e.g. Mutolib"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Gender
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



            <!-- DEPARTMENT + LEVEL -->

            <div class="form-row">


                <div class="form-group">

                    <label>
                        Department
                    </label>

                    <input
                        type="text"
                        name="department"
                        placeholder="e.g. Computer Science"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Level
                    </label>

                    <input
                        type="text"
                        name="level"
                        placeholder="e.g. ND II"
                        required
                    >

                </div>


            </div>



            <!-- PHONE + EMAIL -->

            <div class="form-row">


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


                <div class="form-group">

                    <label>
                        Email Address
                    </label>

                    <input
                        type="email"
                        name="email"
                        placeholder="e.g. student@gmail.com"
                        required
                    >

                </div>


            </div>



            <!-- PASSPORT -->

            <div class="form-group">

                <label>
                    Student Passport
                </label>

                <input
                    type="file"
                    name="passport"
                    accept="image/jpeg,image/png,image/webp"
                >

            </div>



            <!-- BUTTON -->

            <button
                type="submit"
                name="add_student"
            >
                Add Student
            </button>


        </form>


    </main>


</div>


</body>

</html>