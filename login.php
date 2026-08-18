<?php

require_once 'includes/auth.php';


// ------------------------------------------------------------
// If already logged in, redirect to the correct dashboard
// ------------------------------------------------------------

if (is_logged_in()) {

    if ($_SESSION['user_role'] === 'admin') {
        redirect('admin/dashboard.php');
    }

    if ($_SESSION['user_role'] === 'student') {
        redirect('student/dashboard.php');
    }
}


// ------------------------------------------------------------
// Variables for messages
// ------------------------------------------------------------

$error = '';


// ------------------------------------------------------------
// Process Login
// ------------------------------------------------------------

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';


    // Basic validation

    if ($email === '' || $password === '') {

        $error = 'Please enter both email and password.';

    } else {

        // Find user from temporary session data

        $user = find_user_by_email($email);


        // Verify user and password

        if ($user && password_verify($password, $user['password'])) {

            // Prevent session fixation
            session_regenerate_id(true);

            // Store login information
            login_user($user);


            // Redirect according to role

            if ($user['role'] === 'admin') {

                redirect('admin/dashboard.php');

            } elseif ($user['role'] === 'student') {

                redirect('student/dashboard.php');

            } else {

                // Safety fallback
                logout_user();

                $error = 'Invalid user role.';

            }

        } else {

            $error = 'Invalid email or password.';

        }

    }
}


$page_title = 'Login';

require_once 'includes/header.php';

?>

<div class="row justify-content-center">

    <div class="col-md-5 col-lg-4">

        <div class="card shadow-sm">

            <div class="card-body p-4">

                <div class="text-center mb-4">

                    <h2 class="fw-bold">
                        Smart Hostel
                    </h2>

                    <p class="text-muted mb-0">
                        Management System
                    </p>

                </div>


                <?php if ($error): ?>

                    <div class="alert alert-danger">
                        <?= e($error) ?>
                    </div>

                <?php endif; ?>


                <form method="POST" action="login.php">

                    <div class="mb-3">

                        <label for="email"
                               class="form-label">

                            Email Address

                        </label>

                        <input
                            type="email"
                            class="form-control"
                            id="email"
                            name="email"
                            value="<?= e($_POST['email'] ?? '') ?>"
                            placeholder="Enter your email"
                            required
                        >

                    </div>


                    <div class="mb-3">

                        <label for="password"
                               class="form-label">

                            Password

                        </label>

                        <input
                            type="password"
                            class="form-control"
                            id="password"
                            name="password"
                            placeholder="Enter your password"
                            required
                        >

                    </div>


                    <button
                        type="submit"
                        class="btn btn-primary w-100">

                        Login

                    </button>

                </form>


                <hr class="my-4">


                <div class="small text-muted">

                    <p class="mb-2 fw-semibold">
                        Demo Accounts
                    </p>

                    <p class="mb-1">
                        <strong>Admin:</strong>
                        admin@hostel.com
                    </p>

                    <p class="mb-1">
                        <strong>Password:</strong>
                        admin123
                    </p>

                    <p class="mb-1 mt-3">
                        <strong>Student:</strong>
                        student@hostel.com
                    </p>

                    <p class="mb-0">
                        <strong>Password:</strong>
                        student123
                    </p>

                </div>

            </div>

        </div>

    </div>

</div>


<?php require_once 'includes/footer.php'; ?>