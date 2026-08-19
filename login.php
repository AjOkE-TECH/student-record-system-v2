<?php

session_start();


include "config/database.php";


$error = "";


/* LOGIN ADMIN */

if (isset($_POST['login'])) {

    $username = mysqli_real_escape_string(
        $conn,
        trim($_POST['username'])
    );

    $password = $_POST['password'];


    /* Find administrator by username */

    $query = mysqli_query(
        $conn,
        "SELECT * FROM admins WHERE username='$username'"
    );


    if (mysqli_num_rows($query) > 0) {

        $admin = mysqli_fetch_assoc($query);


        /* Verify password */

        if (password_verify(
            $password,
            $admin['password']
        )) {

            $_SESSION['admin'] =
                $admin['username'];

            $_SESSION['admin_id'] =
                $admin['id'];

            $_SESSION['admin_name'] =
                $admin['full_name'];


            header(
                "Location: dashboard.php"
            );

            exit();


        } else {

            $error =
                "Incorrect password!";

        }


    } else {

        $error =
            "Account not found! Please register first.";

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
        Login - Student Record Management System
    </title>


    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >


    <link
        rel="stylesheet"
        href="assets/css/login.css"
    >

</head>


<body>


    <!-- HEADER -->

    <header class="login-header">


        <div class="login-logo">

            <img
                src="assets/image/logo.png"
                alt="Student Record Management System Logo"
            >


            <div class="logo-text">

                <h2>SRMS</h2>

                <span>
                    Student Record Management System
                </span>

            </div>

        </div>


        <nav class="login-nav">

            <a href="index.php">
                Home
            </a>

            <a href="index.php#about">
                About
            </a>

            <a href="index.php#features">
                Features
            </a>

            <a href="index.php#contact">
                Contact
            </a>

        </nav>


    </header>


    <!-- LOGIN SECTION -->

    <section class="login-section">


        <!-- LEFT IMAGE -->

        <div class="login-image">

            <img
                src="assets/image/image.jpg"
                alt="Student"
            >

        </div>


        <!-- RIGHT LOGIN FORM -->

        <div class="login-form-area">


            <div class="login-content">


                <h1>
                    Welcome Back
                </h1>


                <p>
                    Login to continue managing student records.
                </p>


                <!-- SUCCESS MESSAGE -->

                <?php if (isset($_GET['registered'])) { ?>

                    <div class="success-box">

                        <i class="fa-solid fa-circle-check"></i>

                        <span>

                            Account created successfully.
                            Please use your generated username to login.

                        </span>

                    </div>

                <?php } ?>


                <!-- ERROR MESSAGE -->

                <?php if ($error != "") { ?>

                    <div class="error-box">

                        <i class="fa-solid fa-circle-exclamation"></i>

                        <span>

                            <?php
                            echo htmlspecialchars($error);
                            ?>

                        </span>

                    </div>

                <?php } ?>


                <!-- LOGIN FORM -->

                <form
                    method="POST"
                    action=""
                >


                    <!-- USERNAME -->

                    <div class="input-group">

                        <label for="username">

                            Username

                        </label>


                        <div class="input-wrapper">

                            <input
                                type="text"
                                id="username"
                                name="username"
                                placeholder="Enter your username"
                                required
                                autocomplete="username"
                            >

                        </div>

                    </div>


                    <!-- PASSWORD -->

                    <div class="input-group">

                        <label for="password">

                            Password

                        </label>


                        <div class="input-wrapper">

                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Enter your password"
                                required
                                autocomplete="current-password"
                            >

                        </div>

                    </div>


                    <!-- LOGIN BUTTON -->

                    <button
                        type="submit"
                        name="login"
                        class="login-btn"
                    >

                        Login

                    </button>


                </form>


                <!-- REGISTER -->

                <div class="register-text">

                    Don't have an account?

                    <a href="register.php">

                        Create Account

                    </a>

                </div>


            </div>

        </div>


    </section>


    <!-- FOOTER -->

    <footer class="login-footer">

        2026 Student Record Management System

        <span>|</span>

        Designed by Sekinat Mutolib

    </footer>


</body>

</html>