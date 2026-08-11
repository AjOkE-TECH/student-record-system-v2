<?php
session_start();

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

include "config/database.php";

$id = $_GET['id'];

$query = mysqli_query($conn, "SELECT * FROM students WHERE id='$id'");
$student = mysqli_fetch_assoc($query);

$current_passport = $student['passport'];

if(isset($_POST['update_student'])){

    $matric_no = $_POST['matric_no'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $gender = $_POST['gender'];
    $department = $_POST['department'];
    $level = $_POST['level'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];

    $passport = $current_passport;

    if(isset($_FILES['passport']) && $_FILES['passport']['error'] == 0){

        $folder = "assets/upload/students/";

        $fileName = time() . "_" . basename($_FILES['passport']['name']);

        $target = $folder . $fileName;

        if(move_uploaded_file($_FILES['passport']['tmp_name'], $target)){

            if($current_passport != "" && file_exists($folder.$current_passport)){
                unlink($folder.$current_passport);
            }

            $passport = $fileName;
        }
    }

    $update = mysqli_query($conn, "UPDATE students SET
        matric_no='$matric_no',
        firstname='$firstname',
        lastname='$lastname',
        gender='$gender',
        department='$department',
        level='$level',
        phone='$phone',
        email='$email',
        passport='$passport'
        WHERE id='$id'");

    if($update){
        header("Location: view_students.php");
        exit();
    }else{
        $error = "Failed to update student.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Student</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="container">

    <div class="sidebar">
        <h2>Management System</h2>

        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="add_student.php">Add Student</a></li>
            <li><a href="view_students.php">View Students</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">

        <div class="header">
            <h1>Edit Student</h1>
        </div>

        <?php if(isset($error)){ ?>
            <div class="error">
                <?php echo $error; ?>
            </div>
        <?php } ?>

        <form method="POST" enctype="multipart/form-data" class="student-form">

            <input type="text"name="matric_no" value="<?php echo $student['matric_no']; ?>"
                   required>

            <input type="text"
                   name="firstname"
                   value="<?php echo $student['firstname']; ?>"
                   required>

            <input type="text"
                   name="lastname"
                   value="<?php echo $student['lastname']; ?>"
                   required>

            <select name="gender" required>
                <option value="Male" <?php if($student['gender']=="Male") echo "selected"; ?>>
                    Male
                </option>

                <option value="Female" <?php if($student['gender']=="Female") echo "selected"; ?>>
                    Female
                </option>
            </select>

            <input type="text"
                   name="department"
                   value="<?php echo $student['department']; ?>"
                   required>

            <input type="text"
                   name="level"
                   value="<?php echo $student['level']; ?>"
                   required>

            <input type="text"
                   name="phone"
                   value="<?php echo $student['phone']; ?>">

            <input type="email"
                   name="email"
                   value="<?php echo $student['email']; ?>"
                   required>

            <div class="input-group">

                <label>Current Passport</label>

                <?php
                if(!empty($student['passport'])){
                ?>
                    <img src="assets/upload/students/<?php echo $student['passport']; ?>"
                         width="120"
                         height="120"
                         style="border-radius:50%;object-fit:cover;margin-bottom:15px;">
                <?php
                }else{
                ?>
                    <img src="assets/image/default.png"
                         width="120"
                         height="120"
                         style="border-radius:50%;object-fit:cover;margin-bottom:15px;">
                <?php
                }
                ?>

                <label>Choose New Passport</label>

                <input type="file"
                       name="passport"
                       accept="image/*">

            </div>

            <button type="submit" name="update_student">
                Update Student
            </button>

        </form>

    </div>

</div>

</body>
</html>