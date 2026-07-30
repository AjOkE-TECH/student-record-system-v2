<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "config/database.php";

$message = "";
$messageType = "";

if(isset($_POST['login'])){

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $query = mysqli_query($conn,
        "SELECT * FROM admins WHERE username='$username'");

    if(mysqli_num_rows($query) > 0){

        $admin = mysqli_fetch_assoc($query);

        if(password_verify($password, $admin['password'])){

            $_SESSION['admin'] = $admin['username'];

            $message = "Login Successful!";
            $messageType = "success";

        }else{

            $message = "Invalid password.";
            $messageType = "error";

        }

    }else{

        $message = "Account not found. Please register first.";
        $messageType = "error";

    }

}
?>

<!DOCTYPE html>
<html>
<head>

    <title>Login - Student Record System</title>

    <link rel="stylesheet" href="assets/css/style.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>

<div class="login-container">

    <h2>Student Record System</h2>

    <form method="POST">

        <input
            type="text"
            name="username"
            placeholder="Username"
            required
        >

        <input
            type="password"
            name="password"
            placeholder="Password"
            required
        >

        <button type="submit" name="login">
            Login
        </button>

    </form>

    <p style="text-align:center;margin-top:15px;">
        <a href="register.php">
            Don't have an account? Register
        </a>
    </p>

</div>

<?php if($message != ""){ ?>

<script>

Swal.fire({

    icon: "<?php echo $messageType; ?>",

    title:
        "<?php echo $messageType == 'success' ? 'Success' : 'Login Failed'; ?>",

    text: 
       "<?php echo $message; ?>",

    confirmButtonColor: 
         "#006400"

}).then(function(){

<?php if($messageType == "success"){ ?>

    window.location.href = "dashboard.php";

<?php } ?>

});

</script>

<?php } ?>

</body>
</html>