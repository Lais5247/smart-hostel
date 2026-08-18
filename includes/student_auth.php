<?php

require_once __DIR__ . '/auth.php';


/*
|--------------------------------------------------------------------------
| Check Student Login
|--------------------------------------------------------------------------
*/

function student_is_logged_in()
{
    return is_logged_in()
        && isset($_SESSION['user_role'])
        && $_SESSION['user_role'] === 'student';
}


/*
|--------------------------------------------------------------------------
| Require Student Authentication
|--------------------------------------------------------------------------
*/

function require_student()
{
    if (!student_is_logged_in()) {
        redirect('../login.php');
    }
}


/*
|--------------------------------------------------------------------------
| Get Current Student
|--------------------------------------------------------------------------
*/

function current_student()
{
    if (!student_is_logged_in()) {
        return null;
    }

    $user_id = $_SESSION['user_id'];

    foreach ($_SESSION['students'] as $student) {

        if ($student['user_id'] == $user_id) {
            return $student;
        }

    }

    return null;
}


/*
|--------------------------------------------------------------------------
| Get Current Student ID
|--------------------------------------------------------------------------
*/

function current_student_id()
{
    $student = current_student();

    return $student ? $student['id'] : null;
}