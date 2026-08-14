<?php
include "config/database.php";
$error = "";
$generated_username = "";
/* REGISTER ADMIN */
if (isset($_POST['register'])) {
    $full_name = trim($_POST['full_name']);
    $email     = trim($_POST['email']);
    $phone     = trim($_POST['phone']);
    $address   = trim($_POST['address']);
    $state     = trim($_POST['state']);
    $country   = trim($_POST['country']);
    $passwor = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    /* Check password */
    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters.";
    } else {
        /* Protect email */
        $email = mysqli_real_escape_string(
            $conn,
            $email
        );
        /* Check email */
        $check_email = mysqli_query(
            $conn,
            "SELECT id FROM admins WHERE email='$email'"
        );
        if (mysqli_num_rows($check_email) > 0) {
            $error = "Email address is already registered.";
        } else {
            /* Protect input */
            $full_name = mysqli_real_escape_string(
                $conn,
                $full_name
            );
            $phone = mysqli_real_escape_string(
                $conn,
                $phone
            );
            $address = mysqli_real_escape_string(
                $conn,
                $address
            );
            $state = mysqli_real_escape_string(
                $conn,
                $state
            );
            $country = mysqli_real_escape_string(
                $conn,
                $country
            );
            /* GENERATE USERNAME */
            $name_parts = preg_split(
                '/\s+/',
                strtolower(trim($full_name))
            );
            if (count($name_parts) >= 2) {
                $first_name = $name_parts[0];
                $last_name = end($name_parts);
                $base_username =
                    preg_replace(
                        '/[^a-z0-9]/', '', $first_name
                    )
                    . "."
                    .
                    preg_replace('/[^a-z0-9]/', '', $last_name
                    );
            } else {
                $base_username =
                    preg_replace(
                        '/[^a-z0-9]/',
                        '',
                        strtolower($full_name)
                    );
            }
            $username = $base_username;
            $count = 1;
            /* CHECK USERNAME */
            while (true) {
                $username_check = mysqli_query(
                    $conn,
                    "SELECT id FROM admins WHERE username='$username'"
                );
                if (mysqli_num_rows($username_check) == 0) {
                    break;
                }
                $username = $base_username . $count; $count++;
            }
            /* Hash password */
            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );
            /* Insert administrator */
            $insert = mysqli_query(
                $conn,
                "INSERT INTO admins
                (username, full_name, email, phone, address, state, country, password, role)
                VALUES ('$username', '$full_name', '$email','$phone','$address','$state', '$country', '$hashed_password', 'Administrator' )"
            );
            if ($insert) {
                /*
                 * Save generated username
                 * temporarily for display
                 */
                session_start();
                $_SESSION['generated_username'] = $username;
                header("Location: login.php?registered=1");
                exit();
            } else {
                $error ="Account creation failed. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" >
    <title> Create Account - Student Record Management System </title>
    <link rel="stylesheet"href="assets/css/login.css">
</head>
<body>
    <!-- HEADER -->
    <header class="login-header">
        <div class="login-logo">
            <img src="assets/image/logo.png"alt="Student Record Management System Logo" >
            <div class="logo-text">
                <h2>SRMS</h2>
                <span> Student Record Management System </span>
            </div>
        </div>
        <nav class="login-nav">
            <a href="index.php"> Home </a>
            <a href="index.php#about"> About </a>
            <a href="index.php#features"> Features </a>
            <a href="index.php#contact">Contact </a>
        </nav>
    </header>
    <!-- REGISTER SECTION -->
    <section class="login-section">
        <!-- LEFT IMAGE -->
        <div class="login-image">
            <img src="assets/image/image.jpg" alt="Student" >
        </div>
        <!-- RIGHT REGISTER FORM -->
        <div class="login-form-area">
            <div class="login-content">
                <h1>Create Account</h1>
                <p> Create your administrator account to get started. </p>
                <!-- ERROR MESSAGE -->
                <?php if ($error != "") { ?>
                    <div class="error-box">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span> <?php echo htmlspecialchars($error);  ?> </span>
                    </div>
                <?php } ?>
                <!-- REGISTER FORM -->
                <form method="POST"action="" >
                    <!-- FULL NAME -->
                    <div class="input-group">
                        <label for="full_name"> Full Name </label>
                        <div class="input-wrapper">
                            <input type="text" id="full_name"name="full_name"
                                placeholder="Enter your full name" required >
                        </div>
                    </div>
                    <!-- EMAIL -->
                    <div class="input-group">
                        <label for="email"> Email Address </label>
                        <div class="input-wrapper">
                            <input type="email" id="email" name="email"
                                placeholder="Enter your email address"required >
                        </div>
                    </div>
                    <!-- PHONE -->
                    <div class="input-group">
                        <label for="phone">Phone Number </label>
                        <div class="input-wrapper">
                            <input type="tel" id="phone"name="phone"
                                placeholder="Enter your phone number" required >
                        </div>
                    </div>
                    <!-- ADDRESS -->
                    <div class="input-group">
                        <label for="address"> Address </label>
                        <div class="input-wrapper">
                            <input type="text" id="address" name="address"
                                placeholder="Enter your address" required >
                        </div>
                    </div>
                    <!-- STATE -->
                    <div class="input-group">
                        <label for="state"> State</label>
                        <div class="input-wrapper">
                            <select id="state" name="state" required >
                                <option value=""> Select State </option>
                                <option value="Abia"> Abia </option>
                                <option value="Adamawa">  Adamawa</option>
                                <option value="Akwa Ibom">  Akwa Ibom </option>
                                <option value="Anambra">   Anambra </option>
                                <option value="Bauchi"> Bauchi </option>
                                <option value="Bayelsa"> Bayelsa</option>
                                <option value="Benue">Benue </option>
                                <option value="Borno"> Borno </option>
                                <option value="Cross River">  Cross River  </option>
                                <option value="Delta"> Delta </option>
                                <option value="Ebonyi">Ebonyi</option>
                                <option value="Edo"> Edo</option>
                                <option value="Ekiti"> Ekiti</option>
                                <option value="Enugu">Enugu</option>
                                <option value="Gombe"> Gombe</option>
                                <option value="Imo"> Imo</option>
                                <option value="Jigawa">Jigawa </option>
                                <option value="Kaduna"> Kaduna </option>
                                <option value="Kano"> Kano </option>
                                <option value="Katsina">Katsina</option>
                                <option value="Kebbi"> Kebbi </option>
                                <option value="Kogi"> Kogi </option>
                                <option value="Kwara"> Kwara</option>
                                <option value="Lagos"> Lagos</option>
                                <option value="Nasarawa">Nasarawa</option>
                                <option value="Niger">Niger</option>
                                <option value="Ogun"> Ogun</option>
                                <option value="Ondo">Ondo</option>
                                <option value="Osun">Osun </option>
                                <option value="Oyo"> Oyo</option>
                                <option value="Plateau"> Plateau </option>
                                <option value="Rivers"> Rivers</option>
                                <option value="Sokoto">Sokoto </option>
                                <option value="Taraba">Taraba</option>
                                <option value="Yobe">Yobe</option>
                                <option value="Zamfara">Zamfara</option>
                                <option value="FCT"> FCT</option>
                            </select>
                        </div>
                    </div>
                    <!-- COUNTRY -->
                    <div class="input-group">
                        <label for="country">Country </label>
                        <div class="input-wrapper">
                            <select id="country"name="country" required>
                                <option value=""> Select Country</option>
                                <option value="Nigeria"> Nigeria</option>
                                <option value="Ghana"> Ghana</option>
                                <option value="Kenya"> Kenya</option>
                                <option value="South Africa">South Africa</option>
                                <option value="United Kingdom"> United Kingdom </option>
                                <option value="United States"> United States </option>
                                <option value="Other"> Other</option>
                            </select>
                        </div>
                    </div>
                    <!-- PASSWORD -->
                    <div class="input-group">
                        <label for="password"> Password</label>
                        <div class="input-wrapper">
                            <input
                                type="password"id="password" name="password"
                                placeholder="Create password"minlength="8" required>
                        </div>
                    </div>
                    <!-- CONFIRM PASSWORD -->
                    <div class="input-group">
                        <label for="confirm_password"> Confirm Password</label>
                        <div class="input-wrapper">
                            <input
                                type="password"id="confirm_password"name="confirm_password"
                                placeholder="Confirm password"minlength="8"required>
                        </div>
                    </div>
                    <!-- CREATE ACCOUNT BUTTON -->
                    <button type="submit"name="register"class="login-btn"> Create Account </button>
                </form>
                <!-- LOGIN -->
                <div class="register-text">
                    Already have an account?
                    <a href="login.php">
                        Login
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