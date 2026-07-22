<?php
include "config/database.php";

$check_admin = mysqli_query($conn, "SELECT * FROM admins");

if(mysqli_num_rows($check_admin) > 0){
    header("Location: index.php");
    exit();
}

if(isset($_POST['register'])){

    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = mysqli_query($conn,
        "SELECT * FROM admins WHERE username='$username'");

    if(mysqli_num_rows($check) > 0){
        $error = "Username already exists!";
    }else{

        $insert = mysqli_query($conn,
            "INSERT INTO admins (username, password)
             VALUES ('$username', '$password')");

        if($insert){
            header("Location: index.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Account</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="login-container">
    <form method="POST">

        <h2>Create Account</h2>

        <?php if(isset($error)){ ?>
            <p class="error"><?php echo $error; ?></p>
        <?php } ?>

        <input type="text" name="username"
               placeholder="Choose Username" required>

        <input type="password" name="password"
               placeholder="Choose Password" required>

        <button type="submit" name="register">Create Account</button>

        <p>
            Already have an account?
            <a href="index.php">Login</a>
        </p>

    </form>
</div>

</body>
</html>