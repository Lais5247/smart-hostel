<?php

require_once 'includes/auth.php';

if (is_logged_in()) {

    if ($_SESSION['user_role'] === 'admin') {
        redirect('admin/dashboard.php');
    }

    if ($_SESSION['user_role'] === 'student') {
        redirect('student/dashboard.php');
    }

}

redirect('login.php');