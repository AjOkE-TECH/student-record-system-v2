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

    $query = mysqli_query(
        $conn,
        "SELECT * FROM admins WHERE username='$username'"
    );

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
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Login - Student Record System</title>

    <link rel="stylesheet"href="assets/css/style.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body class="login-page">

    <div class="login-box">

        <div class="login-logo">

            <h1>Student Record System</h1>

            <p>
                Login to continue managing student records.
            </p>

        </div>

        <?php if($message != "" && $messageType == "error"){ ?>

            <div class="alert">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php } ?>

        <form method="POST">

            <label for="username">
                Username
            </label>

            <input
                type="text"
                id="username"
                name="username"
                placeholder="Enter username"
                required
            >

            <label for="password">
                Password
            </label>

            <input
                type="password"
                id="password"
                name="password"
                placeholder="Enter password"
                required
            >

            <button type="submit" name="login">
                Login
            </button>

        </form>

        <div class="login-register">

            Don't have an account?

            <a href="register.php">
                Create Account
            </a>

        </div>

    </div>


<?php if($message != ""){ ?>

<script>

Swal.fire({

    icon: "<?php echo $messageType; ?>",

    title:
        "<?php echo $messageType == 'success'
        ? 'Success'
        : 'Login Failed'; ?>",

    text:
        "<?php echo htmlspecialchars($message); ?>",

    confirmButtonColor: "#006400"

}).then(function(){

<?php if($messageType == "success"){ ?>

    window.location.href = "dashboard.php";

<?php } ?>

});

</script>

<?php } ?>

</body>
</html>