<?php

require_once 'includes/auth.php';

if (is_logged_in()) {

    if ($_SESSION['user_role'] === 'admin') {
        redirect('admin/dashboard.php');
    }

    redirect('student/dashboard.php');
}


$error = '';
$success = '';


// ------------------------------------------------------------
// Process Registration
// ------------------------------------------------------------

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';


    if ($name === '' || $email === '' || $password === '' || $confirm_password === '') {

        $error = 'Please fill in all fields.';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = 'Please enter a valid email address.';

    } elseif (strlen($password) < 6) {

        $error = 'Password must be at least 6 characters long.';

    } elseif ($password !== $confirm_password) {

        $error = 'Passwords do not match.';

    } elseif (find_user_by_email($email)) {

        $error = 'An account with this email already exists.';

    } else {

        // Generate a new user ID.
        $user_id = 1;

        if (!empty($_SESSION['users'])) {
            $user_ids = array_column($_SESSION['users'], 'id');
            $user_id = max($user_ids) + 1;
        }


        // Generate a new student ID.
        $student_id = 'STU-001';

        if (!empty($_SESSION['students'])) {

            $student_numbers = [];

            foreach ($_SESSION['students'] as $student) {

                if (preg_match('/^STU-(\d+)$/', $student['student_id'], $matches)) {
                    $student_numbers[] = (int) $matches[1];
                }

            }

            if (!empty($student_numbers)) {
                $student_id = 'STU-' . str_pad(max($student_numbers) + 1, 3, '0', STR_PAD_LEFT);
            }
        }


        // Create login account.
        $_SESSION['users'][] = [
            'id' => $user_id,
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => 'student'
        ];


        // Create student profile with basic information.
        $_SESSION['students'][] = [
            'id' => $user_id,
            'user_id' => $user_id,
            'student_id' => $student_id,
            'name' => $name,
            'email' => $email,
            'phone' => '',
            'department' => '',
            'semester' => '',
            'address' => ''
        ];


        $success = 'Registration successful. You can now log in.';

        $_POST = [];
    }
}


$page_title = 'Registration';

require_once 'includes/header.php';
?>

<div class="row justify-content-center">

    <div class="col-md-6 col-lg-5">

        <div class="card shadow-sm">

            <div class="card-body p-4">

                <div class="text-center mb-4">

                    <h2 class="fw-bold">
                        Student Registration
                    </h2>

                    <p class="text-muted mb-0">
                        Create your Smart Hostel student account.
                    </p>

                </div>


                <?php if ($error): ?>

                    <div class="alert alert-danger">
                        <?= e($error) ?>
                    </div>

                <?php endif; ?>


                <?php if ($success): ?>

                    <div class="alert alert-success">
                        <?= e($success) ?>
                    </div>

                    <a href="login.php" class="btn btn-primary w-100">
                        Go to Login
                    </a>

                <?php else: ?>

                    <form method="POST" action="register.php">

                        <div class="mb-3">

                            <label for="name" class="form-label">
                                Full Name
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="name"
                                name="name"
                                value="<?= e($_POST['name'] ?? '') ?>"
                                required
                            >

                        </div>


                        <div class="mb-3">

                            <label for="email" class="form-label">
                                Email Address
                            </label>

                            <input
                                type="email"
                                class="form-control"
                                id="email"
                                name="email"
                                value="<?= e($_POST['email'] ?? '') ?>"
                                required
                            >

                        </div>


                        <div class="mb-3">

                            <label for="password" class="form-label">
                                Password
                            </label>

                            <input
                                type="password"
                                class="form-control"
                                id="password"
                                name="password"
                                minlength="6"
                                required
                            >

                        </div>


                        <div class="mb-4">

                            <label for="confirm_password" class="form-label">
                                Confirm Password
                            </label>

                            <input
                                type="password"
                                class="form-control"
                                id="confirm_password"
                                name="confirm_password"
                                minlength="6"
                                required
                            >

                        </div>


                        <button
                            type="submit"
                            class="btn btn-primary w-100"
                        >
                            Register
                        </button>

                    </form>

                <?php endif; ?>


                <div class="text-center mt-3">

                    <a href="login.php">
                        Already have an account? Login
                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

<?php require_once 'includes/footer.php'; ?>
