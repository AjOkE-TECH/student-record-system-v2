<?php
session_start();

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

include "config/database.php";

if(!isset($_GET['id']) || empty($_GET['id'])){
    header("Location: view_students.php");
    exit();
}

$id = (int)$_GET['id'];

$query = mysqli_query($conn, "SELECT * FROM students WHERE id='$id'");

if(mysqli_num_rows($query) == 0){
    header("Location: view_students.php");
    exit();
}

$student = mysqli_fetch_assoc($query);

if(!empty($student['passport'])){
    $passport = "assets/upload/students/" . $student['passport'];
}else{
    $passport = "assets/image/default.png";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Profile</title>
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        .profile-container{
            background:#ffffff;
            border-radius:15px;
            padding:35px;
            box-shadow:0 5px 15px rgba(0,0,0,.05);
        }

        .profile-top{
            display:flex;
            align-items:center;
            gap:30px;
            padding-bottom:30px;
            border-bottom:1px solid #eeeeee;
            margin-bottom:30px;
        }

        .profile-image{
            width:150px;
            height:150px;
            border-radius:50%;
            object-fit:cover;
            border:5px solid #E8F5E9;
        }

        .profile-name h2{
            color:#006400;
            font-size:32px;
            margin-bottom:8px;
        }

        .profile-name p{
            color:#777;
            font-size:17px;
        }

        .profile-details{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:20px;
        }

        .profile-item{
            border:1px solid #eeeeee;
            border-radius:10px;
            overflow:hidden;
        }

        .profile-label{
            background:#006400;
            color:#ffffff;
            padding:14px 18px;
            font-weight:bold;
        }

        .profile-value{
            padding:16px 18px;
            color:#444;
            background:#ffffff;
            font-size:16px;
        }

        .profile-actions{
            display:flex;
            gap:15px;
            margin-top:30px;
        }

        .profile-btn{
            display:inline-block;
            padding:13px 25px;
            border-radius:25px;
            text-decoration:none;
            font-weight:bold;
            transition:.3s;
        }

        .edit-profile-btn{
            background:#006400;
            color:#ffffff;
        }

        .edit-profile-btn:hover{
            background:#008000;
        }

        .back-btn{
            background:#eeeeee;
            color:#444;
        }

        .back-btn:hover{
            background:#dddddd;
        }

        .print-btn{
            background:#333333;
            color:#ffffff;
            border:none;
            cursor:pointer;
            font-size:15px;
        }

        .print-btn:hover{
            background:#555555;
        }

        @media(max-width:900px){

            .profile-details{
                grid-template-columns:1fr;
            }

            .profile-top{
                flex-direction:column;
                text-align:center;
            }
        }

        @media print{

            .sidebar,
            .header,
            .profile-actions{
                display:none;
            }

            .container{
                display:block;
            }

            .main-content{
                padding:0;
            }

            .profile-container{
                box-shadow:none;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <div class="sidebar">

        <h2>Management System</h2>

        <ul>
            <li>
                <a href="dashboard.php">Dashboard</a>
            </li>

            <li>
                <a href="add_student.php">Add Student</a>
            </li>

            <li>
                <a href="view_students.php">View Students</a>
            </li>

            <li>
                <a href="search_student.php">Search Student</a>
            </li>

            <li>
                <a href="logout.php">Logout</a>
            </li>
        </ul>

    </div>

    <div class="main-content">

        <div class="header">

            <div>
                <h1>Student Profile</h1>
                <p>View student information</p>
            </div>

        </div>

        <div class="profile-container">

            <div class="profile-top">

                <img
                    src="<?php echo $passport; ?>"
                    alt="Student Passport"
                    class="profile-image"
                >

                <div class="profile-name">

                    <h2>
                        <?php
                        echo htmlspecialchars(
                            $student['firstname'] . " " . $student['lastname']
                        );
                        ?>
                    </h2>

                    <p>
                        Matric No:
                        <?php echo htmlspecialchars($student['matric_no']); ?>
                    </p>

                </div>

            </div>

            <div class="profile-details">

                <div class="profile-item">

                    <div class="profile-label">
                        Matric Number
                    </div>

                    <div class="profile-value">
                        <?php echo htmlspecialchars($student['matric_no']); ?>
                    </div>

                </div>

                <div class="profile-item">

                    <div class="profile-label">
                        Full Name
                    </div>

                    <div class="profile-value">
                        <?php
                        echo htmlspecialchars(
                            $student['firstname'] . " " . $student['lastname']
                        );
                        ?>
                    </div>

                </div>

                <div class="profile-item">

                    <div class="profile-label">
                        Gender
                    </div>

                    <div class="profile-value">
                        <?php echo htmlspecialchars($student['gender']); ?>
                    </div>

                </div>

                <div class="profile-item">

                    <div class="profile-label">
                        Department
                    </div>

                    <div class="profile-value">
                        <?php echo htmlspecialchars($student['department']); ?>
                    </div>

                </div>

                <div class="profile-item">

                    <div class="profile-label">
                        Level
                    </div>

                    <div class="profile-value">
                        <?php echo htmlspecialchars($student['level']); ?>
                    </div>

                </div>

                <div class="profile-item">

                    <div class="profile-label">
                        Phone
                    </div>

                    <div class="profile-value">
                        <?php echo htmlspecialchars($student['phone']); ?>
                    </div>

                </div>

                <div class="profile-item">

                    <div class="profile-label">
                        Email
                    </div>

                    <div class="profile-value">
                        <?php echo htmlspecialchars($student['email']); ?>
                    </div>

                </div>

            </div>

            <div class="profile-actions">

                <a
                    href="view_students.php"
                    class="profile-btn back-btn"
                >
                    Back to Students
                </a>

                <a
                    href="edit_student.php?id=<?php echo $student['id']; ?>"
                    class="profile-btn edit-profile-btn"
                >
                    Edit Student
                </a>

                <button
                    type="button"
                    class="profile-btn print-btn"
                    onclick="window.print()"
                >
                    Print Profile
                </button>

            </div>

        </div>

    </div>

</div>

</body>
</html>