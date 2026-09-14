<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login");
    exit();
}

include "config/database.php";

$message = "";
$message_type = "error";

if (isset($_GET['id'])) {

    $id = (int) $_GET['id'];

    $update = mysqli_query(
        $conn,
        "UPDATE courses
         SET archived_at = NULL
         WHERE id = $id"
    );

    if ($update && mysqli_affected_rows($conn) > 0) {

        $message = "Course restored successfully.";
        $message_type = "success";

    } else {

        $message = "Course record not found or already active.";
    }

} else {

    $message = "No course specified.";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"content="width=device-width, initial-scale=1.0">
    <title>Restore Course - Student Record Management System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/admin.css">
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
                        <span> Dashboard </span>
                    </a>
                </li>

                <li>

                    <a href="add_student">

                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="7" r="4"/><path d="M3 21v-2a6 6 0 0 1 12 0v2"/><path d="M15 3h6"/><path d="M18 0v6"/></svg>
                        </span>

                        <span> Add Student </span>

                    </a>

                </li>

                <li>

                    <a href="view_students">

                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="7" r="4"/><path d="M3 21v-2a6 6 0 0 1 12 0v2"/><circle cx="17" cy="9" r="3"/><path d="M21 21v-2a4 4 0 0 0-7.5-1.5"/></svg>
                        </span>

                        <span>
                            View Students
                        </span>

                    </a>

                </li>

                <li>

                    <a href="archive_students">

                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="5" rx="1"/><path d="M4 8v12a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V8"/><path d="M10 12h4"/></svg>
                        </span>

                        <span> Archive </span>
                    </a>

                </li>

                <li>

                    <a href="result">

                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16v16H4z"/><path d="M8 16v-4"/><path d="M12 16V8"/><path d="M16 16v-7"/></svg>
                        </span>

                        <span> Results </span>

                    </a>

                </li>

                <li>

                    <a href="courses" class="active" >

                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                        </span>

                        <span> Courses </span>

                    </a>

                </li>

                <li>

                    <a href="sessions">

                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </span>

                        <span> Sessions </span>

                    </a>

                </li>

                <li>

                    <a href="search_student">

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

                    <a href="logout" class="logout-link">

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

        <header class="admin-topbar">

            <div>

                <p class="topbar-label">
                    COURSE DIRECTORY
                </p>

                <h1>
                    Restore Course
                </h1>

            </div>

        </header>

        <div class="page-content">

            <div class="message-page">

                <div class="student-message <?php echo $message_type; ?>">

                    <?php
                    echo htmlspecialchars($message);
                    ?>

                </div>

                <div class="message-actions">

                    <a
                        href="courses"
                        class="primary-button"
                    >
                        Back to Courses
                    </a>

                </div>

            </div>

        </div>

        <footer class="admin-footer">

            <span>
                2026 Student Record Management System
            </span>

            <span>
                |
            </span>

            <span>
                Designed by Sekinat Mutolib
            </span>

        </footer>

    </main>

</div>

<script src="assets/js/sidebar.js"></script>

</body>

</html>