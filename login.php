<?php
session_start();
include "config/database.php";

$error = "";

if(isset($_POST['login']))
{
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM admins WHERE username='$username'");

    if(mysqli_num_rows($query) > 0)
    {
        $admin = mysqli_fetch_assoc($query);

        if(password_verify($password, $admin['password']))
        {
            $_SESSION['admin'] = $admin['username'];
            header("Location: dashboard.php");
            exit();
        }
        else
        {
            $error = "Incorrect password!";
        }
    }
    else
    {
        $error = "Account not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Student Record Management System</title>

<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>

<header class="top-header">

    <div class="logo-area">

        <img src="assets/image/logo.png" alt="Logo">

        <div>

            <h2>SRMS</h2>

            <span>Student Record Management System</span>

        </div>

    </div>

    <nav>

       <a href="index.php#home">Home</a>
       <a href="index.php#about">About</a>
       <a href="index.php#features">Features</a>
       <a href="index.php#contact">Contact</a>

    </nav>

</header>


<section class="login-section">

    <div class="login-image">

        <img src="assets/image/image.jpg" alt="Students">

    </div>


    <div class="login-box">

        <div class="login-content">

            <h1>Welcome Back</h1>

            <p>
                Login to continue managing student records.
            </p>

            <?php if($error != ""){ ?>

                <div class="error-box">

                    <i class="fa-solid fa-circle-exclamation"></i>

                    <?php echo $error; ?>

                </div>

            <?php } ?>

            <form method="POST">

                <div class="input-group">

                    <label>Username</label>

                    <input type="text" name="username" placeholder="Username">
                </div>

                <div class="input-group">

                    <label>Password</label>

                    <input
                    type="password"name="password"placeholder="Enter your password"required>

                </div>

                <button
                type="submit"name="login"class="login-btn">

                    <i class="fa-solid fa-right-to-bracket"></i>

                    Login

                </button>

            </form>

            <div class="bottom-text">

                Don't have an account?

                <a href="register.php">

                    Create Account

                </a>

            </div>

        </div>

    </div>

</section>

<footer class="footer">

    2026 Student Record Management System |
    Designed by Sekinat Mutolib

</footer>

</body>

</html>