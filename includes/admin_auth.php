<?php

require_once __DIR__ . '/auth.php';


/*
|--------------------------------------------------------------------------
| Check Admin Login
|--------------------------------------------------------------------------
*/

function admin_is_logged_in()
{
    return is_logged_in()
        && isset($_SESSION['user_role'])
        && $_SESSION['user_role'] === 'admin';
}


/*
|--------------------------------------------------------------------------
| Require Admin Authentication
|--------------------------------------------------------------------------
*/

function require_admin()
{
    if (!admin_is_logged_in()) {
        redirect('../login.php');
    }
}