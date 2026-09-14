<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login");
    exit();
}

include "config/database.php";
include "config/matric.php";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: view_students");
    exit();
}

$id = (int) $_GET['id'];

$query = mysqli_query(
    $conn,
    "SELECT * FROM students
     WHERE id = $id
     AND archived_at IS NULL"
);

if (!$query || mysqli_num_rows($query) == 0) {
    header("Location: view_students");
    exit();
}

$student = mysqli_fetch_assoc($query);

/* Departments for the dropdown. */
$departments_query = mysqli_query(
    $conn,
    "SELECT id, department_name, department_code
     FROM departments
     ORDER BY FIELD(
        UPPER(department_name),
        'COMPUTER SCIENCE',
        'SCIENCE LABORATORY TECHNOLOGY',
        'ACCOUNTANCY',
        'MARKETING',
        'BUSINESS ADMINISTRATION'
     ), department_name ASC"
);

/* Match the student's current department (if any) to a departments row. */
$current_department = srsGetDepartmentByName(
    $conn,
    $student['department'] ?? ''
);

$current_passport = $student['passport'];
$error = "";

if (isset($_POST['update_student'])) {

    /* The matric number is NEVER edited - it stays exactly as stored. */
    $matric_no = $student['matric_no'];

    $firstname = trim($_POST['firstname'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $department_selection = $_POST['department'] ?? '__keep__';
    $level_selection = trim($_POST['level'] ?? '__keep_level__');
    $admission_year_input = trim($_POST['admission_year'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');

    $passport = $current_passport;

    if (isset($_FILES['passport']) && $_FILES['passport']['error'] == 0) {

        $folder = "assets/upload/students/";

        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $original_name = basename($_FILES['passport']['name']);
        $file_extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));

        $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($file_extension, $allowed_extensions)) {

            $fileName = time() . "_" . uniqid() . "." . $file_extension;
            $target = $folder . $fileName;

            if (move_uploaded_file($_FILES['passport']['tmp_name'], $target)) {

                if ($current_passport != "" && file_exists($folder . $current_passport)) {
                    unlink($folder . $current_passport);
                }

                $passport = $fileName;
            }
        }
    }

    /* ---- Department: resolve dropdown id, or keep existing ---- */

    $department = $student['department'] ?? null;

    if ($department_selection !== '__keep__' && $department_selection !== '') {

        $dept_row = srsGetDepartmentById($conn, (int) $department_selection);

        if (!$dept_row) {
            $error = "Please select a valid department.";
        } else {
            $department = $dept_row['department_name'];
        }
    }

    /* ---- Level: ND I / ND II / HND I / HND II, or keep the
          existing (legacy) value so old records are never lost ---- */

    $allowed_levels = array('ND I', 'ND II', 'HND I', 'HND II');

    $level = $student['level'] ?? null;

    if (in_array($level_selection, $allowed_levels, true)) {
        $level = $level_selection;
    } elseif ($level_selection !== '__keep_level__') {
        $error = "Level must be one of: ND I, ND II, HND I, HND II.";
    }

    /* ---- Admission year: keep unless a valid full year is entered ---- */

    $admission_year_value = $student['admission_year'] ?? null;

    if ($admission_year_input !== '') {

        $validated_year = srsValidateAdmissionYear($admission_year_input);

        if ($validated_year === false) {
            $error = "Please enter a valid admission year (full year, e.g. 2026).";
        } else {
            $admission_year_value = $validated_year;
        }
    }

    if ($error === '') {

        $matric_no_safe = mysqli_real_escape_string($conn, $matric_no);
        $firstname_safe = mysqli_real_escape_string($conn, $firstname);
        $lastname_safe = mysqli_real_escape_string($conn, $lastname);
        $gender_safe = mysqli_real_escape_string($conn, $gender);
        $department_safe = mysqli_real_escape_string($conn, $department ?? '');
        $level_safe = mysqli_real_escape_string($conn, $level ?? '');
        $phone_safe = mysqli_real_escape_string($conn, $phone);
        $email_safe = mysqli_real_escape_string($conn, $email);
        $passport_safe = mysqli_real_escape_string($conn, $passport);

        $admission_year_sql = ($admission_year_value === null || $admission_year_value === '')
            ? "NULL"
            : (int) $admission_year_value;

        $update = mysqli_query($conn, "UPDATE students SET
            firstname='$firstname_safe',
            lastname='$lastname_safe',
            gender='$gender_safe',
            department='$department_safe',
            level='$level_safe',
            admission_year=$admission_year_sql,
            phone='$phone_safe',
            email='$email_safe',
            passport='$passport_safe'
            WHERE id='$id' AND archived_at IS NULL");

        if ($update) {
            header("Location: student_profile?id=" . $id);
            exit();
        } else {
            $error = "Failed to update student.";
        }
    }
}

$admin_name = "Administrator";

if (isset($_SESSION['admin_name']) && !empty($_SESSION['admin_name'])) {
    $admin_name = $_SESSION['admin_name'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student - Student Record Management System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="assets/css/students.css">
</head>
<body>

<div class="admin-layout">

    <aside class="admin-sidebar">

        <div class="admin-brand">

            <button
                class="sidebar-toggle"
                id="sidebarToggle"
                type="button"
                aria-label="Toggle sidebar"
                aria-expanded="true"
            >
                <span></span>
                <span></span>
                <span></span>
            </button>

            <img
                class="logo-image"
                src="assets/image/record_logo.png"
                alt="Student Record Management System Logo"
            >

        </div>


        <nav class="admin-navigation">

            <div class="navigation-title">
                MAIN MENU
            </div>

            <ul>

                <li>

                    <a href="dashboard">

                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                        </span>

                        <span>Dashboard</span>

                    </a>

                </li>

                <li>

                    <a href="add_student">

                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="7" r="4"/><path d="M3 21v-2a6 6 0 0 1 12 0v2"/><path d="M15 3h6"/><path d="M18 0v6"/></svg>
                        </span>

                        <span>Add Student</span>

                    </a>

                </li>

                <li>

                    <a
                        href="view_students"
                        class="active"
                    >

                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="7" r="4"/><path d="M3 21v-2a6 6 0 0 1 12 0v2"/><circle cx="17" cy="9" r="3"/><path d="M21 21v-2a4 4 0 0 0-7.5-1.5"/></svg>
                        </span>

                        <span>View Students</span>

                    </a>

                </li>

                <li>

                    <a href="archive_students">

                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="5" rx="1"/><path d="M4 8v12a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V8"/><path d="M10 12h4"/></svg>
                        </span>

                        <span>Archive</span>

                    </a>

                </li>

                <li>

                    <a href="result">

                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16v16H4z"/><path d="M8 16v-4"/><path d="M12 16V8"/><path d="M16 16v-7"/></svg>
                        </span>

                        <span>Results</span>

                    </a>

                </li>

                <li>

                    <a href="courses">

                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                        </span>

                        <span>
                            Courses
                        </span>

                    </a>

                </li>

                <li>

                    <a href="sessions">

                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </span>

                        <span>
                            Sessions
                        </span>

                    </a>

                </li>

                <li>

                    <a href="search_student">

                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
                        </span>

                        <span>Search Student</span>

                    </a>

                </li>

            </ul>

            <div class="navigation-title navigation-bottom-title">
                ACCOUNT
            </div>

            <ul>

                <li>

                    <a href="logout" class="logout-link">

                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        </span>

                        <span>Logout</span>

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

        <header class="admin-topbar">

            <div>

                <div class="topbar-label">
                    STUDENT RECORDS
                </div>

                <h1>
                    Edit Student
                </h1>

            </div>

            <div class="admin-profile">

                <div class="profile-avatar">
                    <?php echo strtoupper(substr($admin_name, 0, 1)); ?>
                </div>

                <div class="profile-details">

                    <strong>
                        <?php echo htmlspecialchars($admin_name); ?>
                    </strong>

                    <span>
                        Administrator
                    </span>

                </div>

            </div>

        </header>

        <div class="page-content">

            <div class="edit-student-page">

                <div class="student-page-header">

                    <p>
                        Update the student's information and save the changes.
                    </p>

                </div>

                <?php if (!empty($error)) { ?>

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

                    <form method="POST" enctype="multipart/form-data">

                        <div class="student-form-body">

                            <div class="form-section-title">
                                Student Passport
                            </div>

                            <div class="passport-section">

                                <div class="passport-preview">

                                    <?php if (!empty($student['passport'])) { ?>

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
                                            <?php if ($student['gender'] == "Male") echo "selected"; ?>
                                        >
                                            Male
                                        </option>

                                        <option
                                            value="Female"
                                            <?php if ($student['gender'] == "Female") echo "selected"; ?>
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
                                        readonly
                                    >

                                    <small class="field-hint">
                                        Is never changed - matric numbers are permanently reserved.
                                    </small>

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

                                    <select
                                        name="department"
                                        required
                                    >

                                        <?php if (!$current_department && $student['department']) { ?>

                                            <option value="__keep__" selected>
                                                <?php echo htmlspecialchars($student['department']); ?>
                                                (keep existing)
                                            </option>

                                        <?php } ?>

                                        <?php if (!$current_department && empty($student['department'])) { ?>

                                            <option value="" selected>
                                                No Department
                                            </option>

                                        <?php } ?>

                                        <?php
                                        if (
                                            $departments_query &&
                                            mysqli_num_rows($departments_query) > 0
                                        ):
                                            while (
                                                $dept =
                                                mysqli_fetch_assoc($departments_query)
                                            ):
                                        ?>

                                            <option
                                                value="<?php echo (int) $dept['id']; ?>"
                                                <?php
                                                if (
                                                    $current_department
                                                    && (int) $current_department['id']
                                                       === (int) $dept['id']
                                                ) {
                                                    echo "selected";
                                                }
                                                ?>
                                            >
                                                <?php echo htmlspecialchars($dept['department_name']); ?>
                                            </option>

                                        <?php
                                            endwhile;
                                        endif;
                                        ?>

                                    </select>

                                </div>

                                <div class="form-group">

                                    <label>
                                        Level
                                        <span class="required">*</span>
                                    </label>

                                    <select
                                        name="level"
                                        required
                                    >

                                        <?php if (empty($student['level'])) { ?>

                                            <option value="" selected>
                                                Select Level
                                            </option>

                                        <?php } ?>

                                        <?php
                                        $edit_allowed_levels = array('ND I', 'ND II', 'HND I', 'HND II');
                                        ?>

                                        <?php foreach ($edit_allowed_levels as $level_option) { ?>

                                            <option
                                                value="<?php echo $level_option; ?>"
                                                <?php if ($student['level'] === $level_option) echo "selected"; ?>
                                            >
                                                <?php echo $level_option; ?>
                                            </option>

                                        <?php } ?>

                                        <?php
                                        if (
                                            !in_array(
                                                $student['level'],
                                                $edit_allowed_levels,
                                                true
                                            )
                                            && !empty($student['level'])
                                        ) {
                                        ?>

                                            <option value="__keep_level__" selected>
                                                <?php echo htmlspecialchars($student['level']); ?>
                                                (keep existing)
                                            </option>

                                        <?php } ?>

                                    </select>

                                </div>

                                <div class="form-group">

                                    <label>
                                        Admission Year
                                    </label>

                                    <input
                                        type="number"
                                        name="admission_year"
                                        value="<?php
                                        if (
                                            ($student['admission_year'] ?? null) !== null
                                            && $student['admission_year'] !== ''
                                        ) {
                                            echo (int) $student['admission_year'];
                                        }
                                        ?>"
                                        min="1950"
                                        max="2100"
                                    >

                                    <small class="field-hint">
                                        Full year, e.g. 2026. Leave empty to keep the current value.
                                    </small>

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
                                    href="student_profile?id=<?php echo (int) $student['id']; ?>"
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

        <footer class="admin-footer">

            <span>SRMS</span>

            <span class="footer-dot">·</span>

            <span>
                Student Record Management System
            </span>

            <span class="footer-dot">·</span>

            <span>
                &copy; <?php echo date('Y'); ?>
            </span>

        </footer>

    </main>

</div>

<script src="assets/js/sidebar.js"></script>

</body>
</html>