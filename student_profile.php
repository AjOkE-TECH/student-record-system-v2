<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include "config/database.php";


/* CHECK ID */

if (!isset($_GET['id']) || empty($_GET['id'])) {

    header("Location: view_students.php");
    exit();

}

$id = (int)$_GET['id'];


/* GET STUDENT */

$query = mysqli_query(
    $conn,
    "SELECT * FROM students WHERE id = $id"
);


if (!$query || mysqli_num_rows($query) == 0) {

    header("Location: view_students.php");
    exit();

}


$student = mysqli_fetch_assoc($query);

/* PASSPORT */
if (!empty($student['passport'])) {
    $passport = "assets/upload/students/" .$student['passport'];
} else {
    $passport = "assets/image/default.png";

}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"content="width=device-width, initial-scale=1.0">
    <title>
        <?php
        echo htmlspecialchars(
            $student['firstname'] . " " . $student['lastname']
        );
        ?> - Student Profile
    </title>
    <link
        rel="stylesheet" href="assets/css/style.css"
    >
    <style>
        .profile-container {
            background: #ffffff;
            border-radius: 15px;
            padding: 35px;
            box-shadow:0 5px 15px rgba(0,0,0,0.05);
        }
        /* PROFILE TOP */
        .profile-top {
            display: flex;
            align-items: center;
            gap: 30px;
            padding-bottom: 30px;
            border-bottom:1px solid #eeeeee;
            margin-bottom: 30px;
        }
        .profile-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border:5px solid #E8F5E9;
            display: block;
        }
        .profile-name h2 {
            color: #006400;
            font-size: 32px;
            margin: 0 0 8px 0;
        }
        .profile-name p {
            color: #777;
            font-size: 17px;
            margin: 0;
        }
        /* DETAILS */
        .profile-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .profile-item {
            border:1px solid #eeeeee;
            border-radius: 10px;
            overflow: hidden;
            background: #ffffff;
        }
        .profile-label {
            background: #006400;
            color: #ffffff;
            padding: 14px 18px;
            font-weight: bold;
            font-size: 16px;
        }
        .profile-value {
            padding: 16px 18px;
            color: #444;
            background: #ffffff;
            font-size: 16px;
            min-height: 20px;
            word-break: break-word;
        }
        /* ACTION BUTTONS */
        .profile-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            flex-wrap: wrap;
        }
        .profile-btn {
            display: inline-block;
            padding: 13px 25px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            border: none;
            transition: 0.3s;
            cursor: pointer;
            font-size: 15px;
        }
        .edit-profile-btn {
            background: #006400;
            color: #ffffff;
        }
        .edit-profile-btn:hover {
            background: #008000;
        }
        .back-btn {
            background: #eeeeee;
            color: #444444;
        }
        .back-btn:hover {
            background: #dddddd;
        }
        .print-btn {
            background: #333333;
            color: #ffffff;
        }
        .print-btn:hover {
            background: #555555;
        }

    </style>

</head>


<body>


<div class="container">


    <!-- SIDEBAR -->

    <div class="sidebar">

        <h2>Management System</h2>

        <ul>

            <li>

                <a href="dashboard.php">

                    Dashboard

                </a>

            </li>


            <li>

                <a href="add_student.php">

                    Add Student

                </a>

            </li>


            <li>

                <a href="view_students.php">

                    View Students

                </a>

            </li>


            <li>

                <a href="search_student.php">

                    Search Student

                </a>

            </li>


            <li>

                <a href="logout.php">

                    Logout

                </a>

            </li>

        </ul>

    </div>



    <!-- MAIN CONTENT -->

    <div class="main-content">


        <!-- HEADER -->

        <div class="header">

            <div>

                <h1>Student Profile</h1>

                <p>
                    View complete student information.
                </p>

            </div>

        </div>



        <!-- PROFILE -->

        <div class="profile-container">


            <!-- PROFILE HEADER -->

            <div class="profile-top">


                <!-- PASSPORT -->

                <img
                    src="<?php echo htmlspecialchars($passport); ?>"
                    alt="Student Passport"
                    class="profile-image"
                    onerror="this.onerror=null;this.src='assets/image/default.png';"
                >


                <!-- NAME -->

                <div class="profile-name">

                    <h2>

                        <?php

                        echo htmlspecialchars(

                            $student['firstname']
                            . " "
                            . $student['lastname']

                        );

                        ?>

                    </h2>


                    <p>

                        Matric No:

                        <?php

                        echo htmlspecialchars(
                            $student['matric_no']
                        );

                        ?>

                    </p>

                </div>

            </div>



            <!-- STUDENT DETAILS -->

            <div class="profile-details">


                <!-- MATRIC NUMBER -->

                <div class="profile-item">

                    <div class="profile-label">
                        Matric Number
                    </div>

                    <div class="profile-value">

                        <?php

                        echo htmlspecialchars(
                            $student['matric_no']
                        );

                        ?>

                    </div>

                </div>



                <!-- FULL NAME -->

                <div class="profile-item">

                    <div class="profile-label">
                        Full Name
                    </div>

                    <div class="profile-value">

                        <?php

                        echo htmlspecialchars(

                            $student['firstname']
                            . " "
                            . $student['lastname']

                        );

                        ?>

                    </div>

                </div>



                <!-- GENDER -->

                <div class="profile-item">

                    <div class="profile-label">
                        Gender
                    </div>

                    <div class="profile-value">

                        <?php

                        echo htmlspecialchars(
                            $student['gender']
                        );

                        ?>

                    </div>

                </div>



                <!-- DEPARTMENT -->

                <div class="profile-item">

                    <div class="profile-label">
                        Department
                    </div>

                    <div class="profile-value">

                        <?php

                        echo htmlspecialchars(
                            $student['department']
                        );

                        ?>

                    </div>

                </div>



                <!-- LEVEL -->

                <div class="profile-item">

                    <div class="profile-label">
                        Level
                    </div>

                    <div class="profile-value">

                        <?php

                        echo htmlspecialchars(
                            $student['level']
                        );

                        ?>

                    </div>

                </div>



                <!-- PHONE -->

                <div class="profile-item">

                    <div class="profile-label">
                        Phone
                    </div>

                    <div class="profile-value">

                        <?php

                        echo htmlspecialchars(
                            $student['phone']
                        );

                        ?>

                    </div>

                </div>



                <!-- EMAIL -->

                <div class="profile-item">

                    <div class="profile-label">
                        Email
                    </div>

                    <div class="profile-value">

                        <?php

                        echo htmlspecialchars(
                            $student['email']
                        );

                        ?>

                    </div>

                </div>



                <!-- PASSPORT -->

                <div class="profile-item">

                    <div class="profile-label">
                        Passport
                    </div>

                    <div class="profile-value">

                        <?php if (!empty($student['passport'])): ?>

                            Passport uploaded

                        <?php else: ?>

                            No passport uploaded

                        <?php endif; ?>

                    </div>

                </div>


            </div>



            <!-- BUTTONS -->

            <div class="profile-actions">


                <!-- BACK -->

                <a
                    href="view_students.php"
                    class="profile-btn back-btn"
                >

                    ← Back to Students

                </a>


                <!-- EDIT -->

                <a
                    href="edit_student.php?id=<?php echo (int)$student['id']; ?>"
                    class="profile-btn edit-profile-btn"
                >

                    ✏ Edit Student

                </a>


                <!-- PRINT -->

                <button
                    type="button"
                    class="profile-btn print-btn"
                    onclick="window.print()"
                >

                    🖨 Print Profile

                </button>


            </div>


        </div>

    </div>

</div>


</body>

</html>