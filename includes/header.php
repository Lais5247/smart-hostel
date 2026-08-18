<?php

if (!isset($page_title)) {
    $page_title = 'Smart Hostel Management System';
}

require_once __DIR__ . '/auth.php';

// Pages inside /student or /admin need one level up for root files/assets.
$base_path = '';
$current_directory = basename(dirname($_SERVER['SCRIPT_NAME']));

if ($current_directory === 'student' || $current_directory === 'admin') {
    $base_path = '../';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= e($page_title) ?> | SHMS</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="<?= $base_path ?>assets/css/style.css"
    >

</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">

    <div class="container">

        <?php

        $home_link = $base_path . 'index.php';

        if (is_logged_in()) {

            if ($_SESSION['user_role'] === 'student') {
                $home_link = $base_path . 'student/dashboard.php';
            } elseif ($_SESSION['user_role'] === 'admin') {
                $home_link = $base_path . 'admin/dashboard.php';
            }

        }

        ?>

        <a class="navbar-brand fw-bold" href="<?= e($home_link) ?>">
            Smart Hostel
        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarNav"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">

            <ul class="navbar-nav me-auto">

                <?php if (is_logged_in() && $_SESSION['user_role'] === 'student'): ?>

                    <li class="nav-item">
                        <a class="nav-link" href="<?= $base_path ?>student/dashboard.php">
                            Dashboard
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?= $base_path ?>student/profile.php">
                            Profile
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?= $base_path ?>student/application.php">
                            Application
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?= $base_path ?>student/room.php">
                            My Room
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?= $base_path ?>student/complaints.php">
                            Complaints
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?= $base_path ?>student/notices.php">
                            Notices
                        </a>
                    </li>

                <?php endif; ?>

                <?php if (is_logged_in() && $_SESSION['user_role'] === 'admin'): ?>

                    <li class="nav-item">
                        <a class="nav-link" href="<?= $base_path ?>admin/dashboard.php">
                            Dashboard
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?= $base_path ?>admin/students.php">
                            Students
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?= $base_path ?>admin/applications.php">
                            Applications
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?= $base_path ?>admin/rooms.php">
                            Rooms
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?= $base_path ?>admin/complaints.php">
                            Complaints
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?= $base_path ?>admin/notices.php">
                            Notices
                        </a>
                    </li>

                <?php endif; ?>

            </ul>

            <?php if (is_logged_in()): ?>

                <span class="navbar-text text-white me-3">
                    <?= e($_SESSION['user_name']) ?>
                </span>

                <a class="btn btn-light btn-sm" href="<?= $base_path ?>logout.php">
                    Logout
                </a>

            <?php else: ?>

                <a class="btn btn-light btn-sm me-2" href="<?= $base_path ?>login.php">
                    Login
                </a>

                <a class="btn btn-outline-light btn-sm" href="<?= $base_path ?>register.php">
                    Register
                </a>

            <?php endif; ?>

        </div>

    </div>

</nav>

<main class="container py-4">
