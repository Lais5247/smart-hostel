<?php

require_once '../includes/admin_auth.php';

require_admin();


$page_title = 'Student Management';

require_once '../includes/header.php';

?>

<div class="mb-4">

    <h2 class="fw-bold mb-1">
        Student Management
    </h2>

    <p class="text-muted mb-0">
        View registered student information.
    </p>

</div>


<div class="card shadow-sm">

    <div class="card-body p-4">

        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>
                <h5 class="fw-bold mb-1">
                    Registered Students
                </h5>
                <p class="text-muted mb-0">
                    Student records currently stored in the session.
                </p>
            </div>

            <span class="badge bg-secondary">
                <?= count($_SESSION['students']) ?> Students
            </span>

        </div>


        <?php if (empty($_SESSION['students'])): ?>

            <div class="alert alert-info mb-0">
                No students are currently registered.
            </div>

        <?php else: ?>

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-light">

                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Department</th>
                            <th>Semester</th>
                            <th>Address</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($_SESSION['students'] as $student): ?>

                            <tr>

                                <td>
                                    <?= e($student['student_id']) ?>
                                </td>

                                <td>
                                    <?= e($student['name']) ?>
                                </td>

                                <td>
                                    <?= e($student['email']) ?>
                                </td>

                                <td>
                                    <?= e($student['phone']) ?: 'Not provided' ?>
                                </td>

                                <td>
                                    <?= e($student['department']) ?: 'Not provided' ?>
                                </td>

                                <td>
                                    <?= e($student['semester']) ?: 'Not provided' ?>
                                </td>

                                <td>
                                    <?= e($student['address']) ?: 'Not provided' ?>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php endif; ?>

    </div>

</div>

<?php require_once '../includes/footer.php'; ?>
