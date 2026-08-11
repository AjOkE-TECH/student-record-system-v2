<?php

session_start();

include "config/database.php";

$error = "";

if (isset($_POST['login'])) {

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $query = mysqli_query(
        $conn,
        "SELECT * FROM admins WHERE username='$username'"
    );

    if (mysqli_num_rows($query) > 0) {

        $admin = mysqli_fetch_assoc($query);

        if (password_verify($password, $admin['password'])) {

            $_SESSION['admin'] = $admin['username'];

            header("Location: dashboard.php");
            exit();

        } else {

            $error = "Incorrect password!";

        }

    } else {

        $error = "Account not found! Please register first.";

    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - Student Record Management System</title>

    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <!-- Login CSS -->
    <link rel="stylesheet" href="assets/css/login.css">

</head>

<body>

    <!-- ================= HEADER ================= -->

    <header class="login-header">

        <div class="login-logo">

            <img
                src="assets/image/logo.png"
                alt="Student Record Management System Logo"
            >

            <div class="logo-text">

                <h2>SRMS</h2>

                <span>Student Record Management System</span>

            </div>

        </div>


        <nav class="login-nav">

            <a href="index.php">Home</a>

            <a href="index.php#about">About</a>

            <a href="index.php#features">Features</a>

            <a href="index.php#contact">Contact</a>

        </nav>

    </header>


    <!-- ================= LOGIN SECTION ================= -->

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


                <h1>Welcome Back</h1>

                <p>
                    Login to continue managing student records.
                </p>


                <!-- ERROR MESSAGE -->

                <?php if ($error != "") { ?>

                    <div class="error-box">

                        <i class="fa-solid fa-circle-exclamation"></i>

                        <span>
                            <?php echo htmlspecialchars($error); ?>
                        </span>

                    </div>

                <?php } ?>


                <!-- LOGIN FORM -->

                <form method="POST" action="">


                    <!-- USERNAME -->

                    <div class="input-group">

                        <label for="username">
                            Username
                        </label>

                        <div class="input-wrapper">

                            <i class="fa-solid fa-user"></i>

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

                            <i class="fa-solid fa-lock"></i>

                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Enter your password"
                                required
                                autocomplete="current-password"
                            >

                            <button
                                type="button"
                                class="password-toggle"
                                onclick="togglePassword()"
                                aria-label="Show password"
                            >

                                <i
                                    class="fa-solid fa-eye"
                                    id="eyeIcon"
                                ></i>

                            </button>

                        </div>

                    </div>


                    <!-- REMEMBER / FORGOT -->

                    <div class="login-options">

                        <label class="remember-me">

                            <input
                                type="checkbox"
                                name="remember"
                            >

                            <span>Remember me</span>

                        </label>


                        <a href="#" class="forgot-password">
                            Forgot Password?
                        </a>

                    </div>


                    <!-- LOGIN BUTTON -->

                    <button
                        type="submit"
                        name="login"
                        class="login-btn"
                    >

                        <i class="fa-solid fa-right-to-bracket"></i>

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


    <!-- ================= FOOTER ================= -->

    <footer class="login-footer">

        2026 Student Record Management System
        <span>|</span>
        Designed by Sekinat Mutolib

    </footer>


    <!-- ================= PASSWORD SCRIPT ================= -->

    <script>

        function togglePassword() {

            const password = document.getElementById("password");
            const eyeIcon = document.getElementById("eyeIcon");

            if (password.type === "password") {

                password.type = "text";

                eyeIcon.classList.remove("fa-eye");

                eyeIcon.classList.add("fa-eye-slash");

            } else {

                password.type = "password";

                eyeIcon.classList.remove("fa-eye-slash");

                eyeIcon.classList.add("fa-eye");

            }

        }

    </script>

</body>

</html>