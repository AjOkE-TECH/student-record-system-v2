<?php
include "config/database.php";

$check_admin = mysqli_query($conn, "SELECT * FROM admins");

if(mysqli_num_rows($check_admin) == 0){
    header("Location: register.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="login-container">
    <h2>Admin Login</h2>

    <form action="login.php" method="POST">

        <input type="text"
               name="username"
               placeholder="Username"
               required>

        <input type="password"
               name="password"
               placeholder="Password"
               required>

        <button type="submit" name="login">
            Login
        </button>

        <p>
           Don't have an account?
           <a href="register.php">Create Account</a>
       </p>

    </form>
</div>

</body>
</html>