<?php
include "config/database.php";
$error = "";

/* REGISTER ADMIN */

if (isset($_POST['register'])) {
    $full_name = trim($_POST['full_name']);
    $email     = trim($_POST['email']);
    $phone     = trim($_POST['phone']);
    $address   = trim($_POST['address']);
    $state     = trim($_POST['state']);
    $country   = trim($_POST['country']);
    $username  = trim($_POST['username']);
    $password  = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    /* Check password */
    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters.";
    } else {
        /* Check username */
        $check_username = mysqli_query(
            $conn,
            "SELECT id FROM admins WHERE username='$username'"
        );
        /* Check email */
        $check_email = mysqli_query(
            $conn,
            "SELECT id FROM admins WHERE email='$email'"
        );
        if (mysqli_num_rows($check_username) > 0) {
            $error = "Username already exists.";
        } elseif (mysqli_num_rows($check_email) > 0) {
            $error = "Email address is already registered.";
        } else {
            /* Hash password */
            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );
            /* Insert administrator */
            $insert = mysqli_query(
                $conn,
                "INSERT INTO admins
                (
                    full_name,
                    email,
                    phone,
                    address,
                    state,
                    country,
                    username,
                    password,
                    role
                )
                VALUES
                (
                    '$full_name',
                    '$email',
                    '$phone',
                    '$address',
                    '$state',
                    '$country',
                    '$username',
                    '$hashed_password',
                    'Administrator'
                )"
            );
            if ($insert) {
                header("Location: login.php?registered=1");
                exit();
            } else {
                $error = "Account creation failed. Please try again.";
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
    <title>Create Account - SRMS</title>
    <link rel="stylesheet"href="/students_record_system/assets/css/style.css">>
</head>
<body class="register-page">
<!-- HEADER -->

<header class="auth-header">
    <div class="auth-logo">
        <img
            src="assets/image/logo.png"alt="SRMS Logo">
        <div>
            <h1>SRMS</h1>
            <p> Student Record Management System</p>
        </div>
    </div>
    <nav>
        <a href="index.php">Home</a>
        <a href="index.php#about">About</a>
        <a href="index.php#features">Features</a>
        <a href="index.php#contact">Contact</a>
    </nav>
</header>
<!--MAIN-->
<main class="register-main">
    <!-- IMAGE -->
    <div class="register-image">
        <img src="assets/image/image.jpg"alt="Students learning">
    </div>
    <!-- FORM -->
    <div class="register-form">
        <div class="register-content">
            <h2> Create Account</h2>
            <p class="register-subtitle">
                Create your administrator account to get started.
            </p>
            <?php if (!empty($error)) { ?>
                <div class="error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php } ?>
            <form method="POST">
                <!-- FULL NAME + EMAIL -->
                <div class="form-row">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input
                            type="text"name="full_name"
                            placeholder="Enter full name"required>
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input
                            type="email"name="email"
                            placeholder="Enter email address"required>
                    </div>
                </div>
                <!-- PHONE + STATE -->
                <div class="form-row">
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input
                            type="tel"name="phone"
                            placeholder="Enter phone number"required>
                    </div>
                    <div class="form-group">
                        <label>State</label>
                        <select name="state" required>
                            <option value="">
                                Select State
                            </option>
                            <option>Abia</option>
                            <option>Adamawa</option>
                            <option>Akwa Ibom</option>
                            <option>Anambra</option>
                            <option>Bauchi</option>
                            <option>Bayelsa</option>
                            <option>Benue</option>
                            <option>Borno</option>
                            <option>Cross River</option>
                            <option>Delta</option>
                            <option>Ebonyi</option>
                            <option>Edo</option>
                            <option>Ekiti</option>
                            <option>Enugu</option>
                            <option>Gombe</option>
                            <option>Imo</option>
                            <option>Jigawa</option>
                            <option>Kaduna</option>
                            <option>Kano</option>
                            <option>Katsina</option>
                            <option>Kebbi</option>
                            <option>Kogi</option>
                            <option>Kwara</option>
                            <option>Lagos</option>
                            <option>Nasarawa</option>
                            <option>Niger</option>
                            <option>Ogun</option>
                            <option>Ondo</option>
                            <option>Osun</option>
                            <option>Oyo</option>
                            <option>Plateau</option>
                            <option>Rivers</option>
                            <option>Sokoto</option>
                            <option>Taraba</option>
                            <option>Yobe</option>
                            <option>Zamfara</option>
                            <option>FCT</option>
                        </select>
                    </div>
                </div>
                <!-- ADDRESS -->
                <div class="form-group">
                    <label>Address</label>
                    <input
                        type="text"name="address"placeholder="Enter your address"required>
                </div>
                <!-- COUNTRY + USERNAME -->
                <div class="form-row">
                    <div class="form-group">
                        <label>Country</label>
                        <select name="country" required>
                            <option value="">
                                Select Country
                            </option>
                            <option value="Nigeria">
                                Nigeria
                            </option>
                            <option value="Ghana">
                                Ghana
                            </option>
                            <option value="Kenya">
                                Kenya
                            </option>
                            <option value="South Africa">
                                South Africa
                            </option>
                            <option value="United Kingdom">
                                United Kingdom
                            </option>
                            <option value="United States">
                                United States
                            </option>
                            <option value="Other">
                                Other
                            </option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input
                            type="text" name="username"
                            placeholder="Choose a username" required>
                    </div>
                </div>
                <!-- PASSWORD + CONFIRM PASSWORD -->
                <div class="form-row">
                    <div class="form-group">
                        <label>Password</label>
                        <div class="password-wrapper">
                            <input
                                type="password"id="password"name="password"
                                placeholder="Create password" minlength="8"required>
                            <button
                                type="button class="password-toggle"
                                onclick="togglePassword('password', this)">👁
                            </button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <div class="password-wrapper">
                            <input
                                type="password"id="confirm_password"name="confirm_password"
                                placeholder="Confirm password"minlength="8"required>

                            <button
                                type="button"class="password-toggle"
                                onclick="togglePassword('confirm_password', this)">👁
                            </button>
                        </div>
                    </div>
                </div>
                <!-- CREATE ACCOUNT -->
                <button
                    type="submit"name="register"class="register-btn">
                    Create Account
                </button>
            </form>
            <p class="login-text">
                Already have an account?
                <a href="login.php">
                    Login
                </a>
            </p>
        </div>
    </div>
</main>
<!--FOOTER -->

<footer class="auth-footer">
    <span>
        2026 Student Record Management System
    </span>
    <span>|</span>
    <span>
        Designed by Sekinat Mutolib
    </span>
</footer>
<script>
function togglePassword(id, button) {
    const password =
        document.getElementById(id);
    if (password.type === "password") {
        password.type = "text";
        button.textContent = "🙈";
    } else {
        password.type = "password";
        button.textContent = "👁";
    }
}
</script>
</body>
</html>