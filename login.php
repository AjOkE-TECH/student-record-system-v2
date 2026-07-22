<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "config/database.php";

if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM admins WHERE username='$username'");

    if(mysqli_num_rows($query) > 0){
        $admin = mysqli_fetch_assoc($query);
        if(password_verify($password, $admin['password'])){
            $_SESSION['admin'] = $admin['username'];
            header("Location: dashboard.php");
            exit();
        } else {
            echo "<script>alert('Invalid password'); window.location='index.php';</script>";
        }
    } else {
        echo "<script>alert('Account not found. Please register first.'); window.location='register.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Student Record System</title>
    <link rel="stylesheet" href="assets/css/style.css"> 
</head>
<body>
    <div class="login-container">
        <h2>Student Record System</h2>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form>
        <p style="text-align:center; margin-top:15px;">
            <a href="register.php">Don't have an account? Register</a>
        </p>
    </div>
</body>
</html>