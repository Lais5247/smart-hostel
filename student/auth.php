<?php

require_once __DIR__ . '/../config/session.php';


/*
|--------------------------------------------------------------------------
| Find User By Email
|--------------------------------------------------------------------------
*/

function find_user_by_email($email)
{
    foreach ($_SESSION['users'] as $user) {

        if (strtolower($user['email']) === strtolower($email)) {
            return $user;
        }

    }

    return null;
}


/*
|--------------------------------------------------------------------------
| Find User By ID
|--------------------------------------------------------------------------
*/

function find_user_by_id($user_id)
{
    foreach ($_SESSION['users'] as $user) {

        if ($user['id'] == $user_id) {
            return $user;
        }

    }

    return null;
}


/*
|--------------------------------------------------------------------------
| Login User
|--------------------------------------------------------------------------
*/

function login_user($user)
{
    // Store authenticated user information
    $_SESSION['logged_in'] = true;
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_name'] = $user['name'];
}


/*
|--------------------------------------------------------------------------
| Logout User
|--------------------------------------------------------------------------
*/

function logout_user()
{
    unset($_SESSION['logged_in']);
    unset($_SESSION['user_id']);
    unset($_SESSION['user_role']);
    unset($_SESSION['user_name']);
}


/*
|--------------------------------------------------------------------------
| Check Login
|--------------------------------------------------------------------------
*/

function is_logged_in()
{
    return isset($_SESSION['logged_in']) &&
           $_SESSION['logged_in'] === true;
}


/*
|--------------------------------------------------------------------------
| Get Current User
|--------------------------------------------------------------------------
*/

function current_user()
{
    if (!is_logged_in()) {
        return null;
    }

    return find_user_by_id($_SESSION['user_id']);
}


/*
|--------------------------------------------------------------------------
| Redirect Helper
|--------------------------------------------------------------------------
*/

function redirect($url)
{
    header("Location: $url");
    exit;
}


/*
|--------------------------------------------------------------------------
| Escape Output
|--------------------------------------------------------------------------
*/

function e($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}