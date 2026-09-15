<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login");
    exit();
}

include "config/database.php";

$current_page = basename($_SERVER['PHP_SELF']);

$admin_name = "Administrator";

if (
    isset($_SESSION['admin_name']) &&
    !empty($_SESSION['admin_name'])
) {
    $admin_name = $_SESSION['admin_name'];
}

$success = "";
$error   = "";
$form_name = "";

/* =========================================================
   ADD SESSION (POST)
========================================================= */

if (isset($_POST['add_session'])) {

    $form_name = trim($_POST['session_name'] ?? "");

    if ($form_name === "") {

        $error = "Session name is required.";

    } else {

        $form_name = trim($form_name);

        $dup_stmt = $conn->prepare(
            "SELECT id FROM sessions WHERE session_name = ? LIMIT 1"
        );

        if ($dup_stmt) {

            $dup_stmt->bind_param("s", $form_name);
            $dup_stmt->execute();
            $dup_result = $dup_stmt->get_result();

            if ($dup_result->num_rows > 0) {

                $error = "This academic session already exists.";

            } else {

                $ins_stmt = $conn->prepare(
                    "INSERT INTO sessions (session_name) VALUES (?)"
                );

                if ($ins_stmt) {

                    $ins_stmt->bind_param("s", $form_name);

                    if ($ins_stmt->execute()) {

                        $success  = "Session '" . htmlspecialchars($form_name) . "' added successfully.";
                        $form_name = "";

                    } else {

                        $error = "Unable to add session: " . $conn->error;

                    }

                    $ins_stmt->close();

                } else {

                    $error = "Database error: " . $conn->error;

                }

            }

            $dup_stmt->close();

        } else {

            $error = "Database error: " . $conn->error;

        }

    }

}

/* =========================================================
   GET ALL SESSIONS
========================================================= */

$sessions_query = mysqli_query(
    $conn,
    "SELECT
        id,
        session_name,
        created_at
     FROM sessions
     ORDER BY session_name DESC"
);

$sessions_total = 0;
if ($sessions_query) {
    $sessions_total = mysqli_num_rows($sessions_query);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <title>Sessions - Student Record Management System</title>

    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="assets/css/courses.css">

</head>

<body>

<div class="admin-layout">

    <!-- SIDEBAR -->
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
                    <a href="dashboard"
                       class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
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
                        <span> View Students </span>
                    </a>
                </li>

                <li>
                    <a href="archive_students">
                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="5" rx="1"/><path d="M4 8v12a 1 1 0 0 0 1 1h14a 1 1 0 0 0 1-1V8"/><path d="M10 12h4"/></svg>
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
                    <a href="courses">
                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                        </span>
                        <span> Courses </span>
                    </a>
                </li>

                <li>
                    <a href="sessions" class="active">
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
                        <span> Search Student </span>
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
                        <span> Logout </span>
                    </a>
                </li>
            </ul>

        </nav>

        <div class="sidebar-footer">
            <strong>Student Record Management System</strong>
            <span>Administration Panel</span>
        </div>

    </aside>

    <!-- MAIN -->
    <main class="admin-main">

        <!-- TOPBAR -->
        <header class="admin-topbar">
            <div>
                <p class="topbar-label">ACADEMIC SESSIONS</p>
                <h1>Sessions</h1>
            </div>
            <div class="admin-profile">
                <div class="profile-avatar">
                    <?php echo strtoupper(substr($admin_name, 0, 1)); ?>
                </div>
                <div class="profile-details">
                    <strong><?php echo htmlspecialchars($admin_name); ?></strong>
                    <span>Administrator</span>
                </div>
            </div>
        </header>

        <!-- PAGE CONTENT -->
        <section class="courses-page">

           
            <!-- SUCCESS / ERROR MESSAGE -->
            <?php if ($success !== ""): ?>
                <div class="courses-table-card" style="margin-bottom:18px; padding:14px 22px; background:#ecfdf5; border-color:#d1fae5; color:#006400;">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>
            <?php if ($error !== ""): ?>
                <div class="courses-table-card" style="margin-bottom:18px; padding:14px 22px; background:#fef2f2; border-color:#fecdd3; color:#991b1b;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <!-- ADD SESSION CARD -->
            <div class="courses-table-card" style="margin-bottom:20px;">

                <div class="courses-card-header">
                    <div>
                        <h3>Add New Session</h3>
                        <p>Add an academic session (e.g. 2025/2026, 2026/2027).</p>
                    </div>
                </div>

                <form
                    method="post"
                    action="sessions"
                    class="courses-filter-form"
                    style="padding:14px 22px; border-top:1px solid #f0f0f0; gap:10px;"
                >
                    <div class="courses-search-box" style="max-width:300px; flex:none;">
                        <input
                            type="text"
                            name="session_name"
                            value="<?php echo htmlspecialchars($form_name); ?>"
                            placeholder="e.g. 2026/2027"
                            required
                        >
                    </div>
                    <button type="submit" name="add_session" class="courses-filter-btn">
                        Add Session
                    </button>
                </form>

            </div>

            <!-- SESSIONS TABLE CARD -->
            <div class="courses-table-card">

                <div class="courses-card-header">
                    <div>
                        <h3>All Sessions</h3>
                        <p>Academic sessions currently in the system.</p>
                    </div>
                    <div class="courses-table-count">
                        <strong><?php echo number_format($sessions_total); ?></strong>
                        <span>session<?php echo $sessions_total == 1 ? '' : 's'; ?></span>
                    </div>
                </div>

                <?php if ($sessions_query && mysqli_num_rows($sessions_query) > 0): ?>

                    <div class="courses-table-wrapper">
                        <table class="courses-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Session Name</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php $sn = 1; while ($row = mysqli_fetch_assoc($sessions_query)): ?>
                                <tr>
                                    <td class="courses-serial"><?php echo $sn++; ?></td>
                                    <td>
                                        <span class="courses-session-badge">
                                            <?php echo htmlspecialchars($row['session_name']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="courses-date-text">
                                            <?php echo date("M j, Y", strtotime($row['created_at'])); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                <?php else: ?>

                    <div class="courses-empty">
                        <div class="courses-empty-icon">S</div>
                        <h3>No sessions found</h3>
                        <p>There are currently no academic sessions in the system.</p>
                        <p style="font-size:13px; color:#9ca3af; margin:0;">
                            Add a session above to start creating courses and results.
                        </p>
                    </div>

                <?php endif; ?>

            </div>

        </section>

        <footer class="admin-footer">
            <span>2026 Student Record Management System</span>
            <span>|</span>
            <span>Designed by Sekinat Mutolib</span>
        </footer>

    </main>

</div>

<script src="assets/js/sidebar.js"></script>

</body>
</html>
