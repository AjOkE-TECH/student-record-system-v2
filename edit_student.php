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
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>

<div class="admin-layout">

    <!--sidebar -->

    <aside class="admin-sidebar">


        <div class="admin-brand">

            <div class="brand-mark">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
            </div>

            <div class="brand-text">

                <h2>
                    Student Record
                </h2>

                <span>
                    Management System
                </span>

            </div>

        </div>


        <nav class="admin-navigation">

            <div class="navigation-title">
                MAIN MENU
            </div>

            <ul>


                <li>

                    <a
                        href="dashboard.php"
                        class="active"
                    >

                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                        </span>

                        <span>
                            Dashboard
                        </span>

                    </a>

                </li>


                <li>

                    <a href="add_student.php">

                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="7" r="4"/><path d="M3 21v-2a6 6 0 0 1 12 0v2"/><path d="M15 3h6"/><path d="M18 0v6"/></svg>
                        </span>

                        <span>
                            Add Student
                        </span>

                    </a>

                </li>


                <li>

                    <a href="view_students.php">

                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="7" r="4"/><path d="M3 21v-2a6 6 0 0 1 12 0v2"/><circle cx="17" cy="9" r="3"/><path d="M21 21v-2a4 4 0 0 0-7.5-1.5"/></svg>
                        </span>

                        <span>
                            View Students
                        </span>

                    </a>

                </li>


                <li>

                    <a href="search_student.php">

                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
                        </span>

                        <span>
                            Search Student
                        </span>

                    </a>

                </li>


            </ul>


            <div class="navigation-title navigation-bottom-title">
                ACCOUNT
            </div>


            <ul>

                <li>

                    <a href="logout.php" class="logout-link">

                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        </span>

                        <span>
                            Logout
                        </span>

                    </a>

                </li>

            </ul>

        </nav>


        <div class="sidebar-footer">

            <strong>
                Student Record Management System
            </strong>

            <span>
                Administration Panel
            </span>

        </div>


    </aside>
    <main class="admin-main">

        <div class="page-content">

    <div class="edit-student-page">

        <div class="student-page-header">

            <div class="page-eyebrow">
                STUDENT RECORDS
            </div>

            <h1>Edit Student</h1>

            <p>
                Update the student's information and save the changes.
            </p>

        </div>


        <?php if(isset($error)){ ?>

            <div class="student-message error">
                <?php echo htmlspecialchars($error); ?>
            </div>

        <?php } ?>


        <div class="student-form-card">

            <div class="student-form-header">

                <h2>Student Information</h2>

                <p>
                    Update the required student details below.
                </p>

            </div>


            <form
                method="POST"
                enctype="multipart/form-data"
            >

                <div class="student-form-body">


                    <div class="form-section-title">
                        Student Passport
                    </div>


                    <div class="passport-section">

                        <div class="passport-preview">

                            <?php if(!empty($student['passport'])){ ?>

                                <img
                                    src="assets/upload/students/<?php echo htmlspecialchars($student['passport']); ?>"
                                    alt="Student Passport"
                                    onerror="this.onerror=null;this.src='assets/image/default.png';"
                                >

                            <?php } else { ?>

                                <img
                                    src="assets/image/default.png"
                                    alt="Student Passport"
                                >

                            <?php } ?>

                        </div>


                        <div class="passport-content">

                            <h3>
                                Student Passport
                            </h3>

                            <p>
                                Upload a new passport photograph if you want to replace the current one.
                                JPG, PNG and WEBP formats are supported.
                            </p>


                            <label class="passport-upload">

                                Choose New Passport

                                <input
                                    type="file"
                                    name="passport"
                                    accept="image/jpeg,image/png,image/webp"
                                >

                            </label>

                        </div>

                    </div>



                    <div class="form-section-title">
                        Personal Information
                    </div>


                    <div class="student-form-grid">


                        <div class="form-group">

                            <label>
                                First Name
                                <span class="required">*</span>
                            </label>

                            <input
                                type="text"
                                name="firstname"
                                value="<?php echo htmlspecialchars($student['firstname']); ?>"
                                required
                            >

                        </div>


                        <div class="form-group">

                            <label>
                                Last Name
                                <span class="required">*</span>
                            </label>

                            <input
                                type="text"
                                name="lastname"
                                value="<?php echo htmlspecialchars($student['lastname']); ?>"
                                required
                            >

                        </div>


                        <div class="form-group">

                            <label>
                                Gender
                                <span class="required">*</span>
                            </label>

                            <select name="gender" required>

                                <option value="">
                                    Select Gender
                                </option>

                                <option
                                    value="Male"
                                    <?php if($student['gender']=="Male") echo "selected"; ?>
                                >
                                    Male
                                </option>

                                <option
                                    value="Female"
                                    <?php if($student['gender']=="Female") echo "selected"; ?>
                                >
                                    Female
                                </option>

                            </select>

                        </div>


                        <div class="form-group">

                            <label>
                                Matric Number
                                <span class="required">*</span>
                            </label>

                            <input
                                type="text"
                                name="matric_no"
                                value="<?php echo htmlspecialchars($student['matric_no']); ?>"
                                required
                            >

                        </div>

                    </div>



                    <div class="form-section-title">
                        Academic Information
                    </div>


                    <div class="student-form-grid">


                        <div class="form-group">

                            <label>
                                Department
                                <span class="required">*</span>
                            </label>

                            <input
                                type="text"
                                name="department"
                                value="<?php echo htmlspecialchars($student['department']); ?>"
                                required
                            >

                        </div>


                        <div class="form-group">

                            <label>
                                Level
                                <span class="required">*</span>
                            </label>

                            <input
                                type="text"
                                name="level"
                                value="<?php echo htmlspecialchars($student['level']); ?>"
                                required
                            >

                        </div>

                    </div>



                    <div class="form-section-title">
                        Contact Information
                    </div>


                    <div class="student-form-grid">


                        <div class="form-group">

                            <label>
                                Phone Number
                            </label>

                            <input
                                type="text"
                                name="phone"
                                value="<?php echo htmlspecialchars($student['phone']); ?>"
                            >

                        </div>


                        <div class="form-group">

                            <label>
                                Email Address
                                <span class="required">*</span>
                            </label>

                            <input
                                type="email"
                                name="email"
                                value="<?php echo htmlspecialchars($student['email']); ?>"
                                required
                            >

                        </div>

                    </div>



                    <div class="student-form-actions">

                        <a
                            href="view_students.php"
                            class="cancel-student"
                        >
                            Cancel
                        </a>


                        <button
                            type="submit"
                            name="update_student"
                            class="add-student-button"
                        >
                            Update Student
                        </button>

                    </div>


                </div>

            </form>

        </div>

    </div>

</div>

</body>
</html>