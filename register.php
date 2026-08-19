<?php

session_start();

require_once "config/database.php";

$error = "";
$generated_username = "";

/* =========================================
   REGISTER ACCOUNT
   ========================================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $full_name        = trim($_POST["full_name"] ?? "");
    $email            = trim($_POST["email"] ?? "");
    $phone            = trim($_POST["phone"] ?? "");
    $country          = trim($_POST["country"] ?? "");
    $state            = trim($_POST["state"] ?? "");
    $address          = trim($_POST["address"] ?? "");
    $password         = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    /* =========================================
       VALIDATION
       ========================================= */

    if (
        empty($full_name) ||
        empty($email) ||
        empty($phone) ||
        empty($country) ||
        empty($state) ||
        empty($address) ||
        empty($password) ||
        empty($confirm_password)
    ) {

        $error = "Please fill in all fields.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } elseif ($password !== $confirm_password) {

        $error = "Passwords do not match.";

    } elseif (strlen($password) < 8) {

        $error = "Password must be at least 8 characters.";

    } else {

        /* =========================================
           CHECK IF EMAIL ALREADY EXISTS
           ========================================= */

        $check_email = $conn->prepare(
            "SELECT id FROM admins WHERE email = ? LIMIT 1"
        );

        $check_email->bind_param("s", $email);
        $check_email->execute();
        $email_result = $check_email->get_result();

        if ($email_result->num_rows > 0) {

            $error = "Email address is already registered.";

        } else {

            /* =========================================
               GENERATE USERNAME FROM FULL NAME
               ========================================= */

            $name_parts = preg_split(
                "/\s+/",
                strtolower(trim($full_name))
            );

            if (count($name_parts) >= 2) {

                $first_name = $name_parts[0];
                $last_name  = end($name_parts);

                $first_name = preg_replace(
                    "/[^a-z0-9]/",
                    "",
                    $first_name
                );

                $last_name = preg_replace(
                    "/[^a-z0-9]/",
                    "",
                    $last_name
                );

                $base_username = $first_name . "." . $last_name;

            } else {

                $base_username = preg_replace(
                    "/[^a-z0-9]/",
                    "",
                    strtolower($full_name)
                );
            }

            /* Prevent empty username */
            if (empty($base_username)) {
                $base_username = "user";
            }

            $username = $base_username;
            $count = 1;

            /* =========================================
               CHECK USERNAME AVAILABILITY
               ========================================= */

            while (true) {

                $check_username = $conn->prepare(
                    "SELECT id FROM admins WHERE username = ? LIMIT 1"
                );

                $check_username->bind_param(
                    "s",
                    $username
                );

                $check_username->execute();

                $username_result =
                    $check_username->get_result();

                if ($username_result->num_rows === 0) {
                    break;
                }

                $username = $base_username . $count;
                $count++;
            }

            /* =========================================
               HASH PASSWORD
               ========================================= */

            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            /* =========================================
               INSERT ACCOUNT
               
               NOTE:
               There is NO role column here.
               ========================================= */

            $insert = $conn->prepare(
                "INSERT INTO admins
                (
                    username,
                    full_name,
                    email,
                    phone,
                    address,
                    state,
                    country,
                    password
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );

            $insert->bind_param(
                "ssssssss",
                $username,
                $full_name,
                $email,
                $phone,
                $address,
                $state,
                $country,
                $hashed_password
            );

            if ($insert->execute()) {

                /* Save generated username for login page */
                $_SESSION["generated_username"] = $username;

                header(
                    "Location: login.php?registered=1"
                );

                exit();

            } else {

                $error =
                    "Account creation failed. Please try again.";
            }
        }
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
        Create Account - Student Record Management System
    </title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>


<body class="register-page">


<!--header-->

<header class="register-header">

    <div class="register-logo">

        <img
            src="assets/image/logo.png"alt="SRMS Logo" >

        <div class="register-logo-text">

            <strong>SRMS</strong>

            <span>
                Student Record Management System
            </span>

        </div>

    </div>


    <nav class="register-nav">

        <a href="login.php">
            Login
        </a>

    </nav>

</header>


<!-- =========================================
     MAIN REGISTRATION AREA
     ========================================= -->

<main class="register-wrapper">


    <!-- =====================================
         LEFT IMAGE
         ===================================== -->

    <section class="register-image">

        <img
            src="assets/image/image.jpg"
            alt="Students learning"
        >

    </section>


    <!-- =====================================
         RIGHT FORM SECTION
         ===================================== -->

    <section class="register-form-section">

        <div class="register-form-container">


            <!-- HEADING -->

            <div class="register-heading">

                <h1>
                    Create Account
                </h1>

                <p>
                    Register your account to access
                    the Student Record Management System.
                </p>

            </div>


            <!-- ERROR MESSAGE -->

            <?php if (!empty($error)): ?>

                <div class="register-error">

                    <?php
                    echo htmlspecialchars(
                        $error,
                        ENT_QUOTES,
                        "UTF-8"
                    );
                    ?>

                </div>

            <?php endif; ?>


            <!-- =================================
                 REGISTRATION FORM
                 ================================= -->

            <form
                method="POST"
                action=""
                class="register-form"
                autocomplete="off"
            >


               <div class="form-row">

    <!-- FULL NAME -->
    <div class="form-group">

        <label for="full_name">
            Full Name
        </label>

        <input
            type="text"
            id="full_name"
            name="full_name"
            placeholder="Enter your full name"
            value="<?= htmlspecialchars(
                $_POST["full_name"] ?? "",
                ENT_QUOTES,
                "UTF-8"
            ) ?>"
            autocomplete="name"
            required
        >

    </div>


    <!-- EMAIL -->
    <div class="form-group">

        <label for="email">
            Email Address
        </label>

        <input
            type="email"
            id="email"
            name="email"
            placeholder="Enter your email address"
            value="<?= htmlspecialchars(
                $_POST["email"] ?? "",
                ENT_QUOTES,
                "UTF-8"
            ) ?>"
            required
        >

    </div>

</div>


              <div class="form-row">

    <!-- PHONE -->
    <div class="form-group">

        <label for="phone">
            Phone Number
        </label>

        <input
            type="tel"
            id="phone"
            name="phone"
            placeholder="Enter your phone number"
            value="<?= htmlspecialchars(
                $_POST["phone"] ?? "",
                ENT_QUOTES,
                "UTF-8"
            ) ?>"
            required
        >

    </div>


    <!-- COUNTRY -->
    <div class="form-group">

        <label for="country">
            Country
        </label>

        <select
            id="country"
            name="country"
            required
        >

            <option value="">Select Country</option>

            <option value="Nigeria"
                <?= (($_POST["country"] ?? "") === "Nigeria") ? "selected" : "" ?>>
                Nigeria
            </option>

            <option value="Ghana"
                <?= (($_POST["country"] ?? "") === "Ghana") ? "selected" : "" ?>>
                Ghana
            </option>

            <option value="Kenya"
                <?= (($_POST["country"] ?? "") === "Kenya") ? "selected" : "" ?>>
                Kenya
            </option>

            <option value="South Africa"
                <?= (($_POST["country"] ?? "") === "South Africa") ? "selected" : "" ?>>
                South Africa
            </option>

            <option value="United Kingdom"
                <?= (($_POST["country"] ?? "") === "United Kingdom") ? "selected" : "" ?>>
                United Kingdom
            </option>

            <option value="United States"
                <?= (($_POST["country"] ?? "") === "United States") ? "selected" : "" ?>>
                United States
            </option>

            <option value="Other"
                <?= (($_POST["country"] ?? "") === "Other") ? "selected" : "" ?>>
                Other
            </option>

        </select>

    </div>

</div>
              <div class="form-row">

    <!-- STATE -->
    <div class="form-group">

        <label for="state">
            State
        </label>

        <select
            id="state"
            name="state"
            required
        >

            <option value="">Select State</option>

            <option value="Abia">Abia</option>
            <option value="Adamawa">Adamawa</option>
            <option value="Akwa Ibom">Akwa Ibom</option>
            <option value="Anambra">Anambra</option>
            <option value="Bauchi">Bauchi</option>
            <option value="Bayelsa">Bayelsa</option>
            <option value="Benue">Benue</option>
            <option value="Borno">Borno</option>
            <option value="Cross River">Cross River</option>
            <option value="Delta">Delta</option>
            <option value="Ebonyi">Ebonyi</option>
            <option value="Edo">Edo</option>
            <option value="Ekiti">Ekiti</option>
            <option value="Enugu">Enugu</option>
            <option value="Gombe">Gombe</option>
            <option value="Imo">Imo</option>
            <option value="Jigawa">Jigawa</option>
            <option value="Kaduna">Kaduna</option>
            <option value="Kano">Kano</option>
            <option value="Katsina">Katsina</option>
            <option value="Kebbi">Kebbi</option>
            <option value="Kogi">Kogi</option>
            <option value="Kwara">Kwara</option>
            <option value="Lagos">Lagos</option>
            <option value="Nasarawa">Nasarawa</option>
            <option value="Niger">Niger</option>
            <option value="Ogun">Ogun</option>
            <option value="Ondo">Ondo</option>
            <option value="Osun">Osun</option>
            <option value="Oyo">Oyo</option>
            <option value="Plateau">Plateau</option>
            <option value="Rivers">Rivers</option>
            <option value="Sokoto">Sokoto</option>
            <option value="Taraba">Taraba</option>
            <option value="Yobe">Yobe</option>
            <option value="Zamfara">Zamfara</option>
            <option value="FCT">Federal Capital Territory</option>

        </select>

    </div>


    <!-- ADDRESS -->
    <div class="form-group">

        <label for="address">
            Address
        </label>

        <input
            type="text"
            id="address"
            name="address"
            placeholder="Enter your full address"
            value="<?= htmlspecialchars(
                $_POST["address"] ?? "",
                ENT_QUOTES,
                "UTF-8"
            ) ?>"
            required
        >

    </div>

</div>

              <div class="form-row">

    <!-- PASSWORD -->
    <div class="form-group">

        <label for="password">
            Password
        </label>

        <div class="password-wrapper">

            <input
                type="password"
                id="password"
                name="password"
                placeholder="Create password"
                minlength="8"
                required
            >

            <button
                type="button"
                class="password-toggle"
                onclick="togglePassword('password', this)"
                aria-label="Show password"
            >
                👁
            </button>

        </div>

    </div>


    <!-- CONFIRM PASSWORD -->
    <div class="form-group">

        <label for="confirm_password">
            Confirm Password
        </label>

        <div class="password-wrapper">

            <input
                type="password"
                id="confirm_password"
                name="confirm_password"
                placeholder="Confirm password"
                minlength="8"
                required
            >

            <button
                type="button"
                class="password-toggle"
                onclick="togglePassword('confirm_password', this)"
                aria-label="Show password"
            >
                👁
            </button>

        </div>

    </div>

</div>
                <!-- CREATE ACCOUNT BUTTON -->

                <button
                    type="submit"
                    name="register"
                    class="register-button"
                >
                    Create Account
                </button>


            </form>


            <!-- LOGIN LINK -->

            <div class="register-login">

                Already have an account?

                <a href="login.php">
                    Login
                </a>

            </div>


        </div>

    </section>

</main>


<!-- =========================================
     FOOTER
     ========================================= -->

<footer class="register-footer">

    <span>
        2026 Student Record Management System
    </span>

    <span class="footer-divider">
        |
    </span>

    <span>
        Designed by Sekinat Mutolib
    </span>

</footer>


<!-- =========================================
     PASSWORD TOGGLE
     ========================================= -->

<script>

function togglePassword(inputId, button) {

    const input = document.getElementById(inputId);

    if (input.type === "password") {

        input.type = "text";

        button.textContent = "🙈";

    } else {

        input.type = "password";

        button.textContent = "👁";

    }

}

</script>


</body>
</html>