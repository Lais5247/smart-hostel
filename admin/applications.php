<?php

require_once '../includes/admin_auth.php';

require_admin();


$success = '';
$error = '';


// ------------------------------------------------------------
// Process application action
// ------------------------------------------------------------

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $application_id = isset($_POST['application_id'])
        ? (int) $_POST['application_id']
        : 0;

    $action = $_POST['action'] ?? '';


    if ($application_id <= 0) {

        $error = 'Invalid application selected.';

    } elseif (!in_array($action, ['approve', 'reject'])) {

        $error = 'Invalid application action.';

    } else {

        $application_found = false;


        foreach ($_SESSION['applications'] as &$application) {

            if ($application['id'] == $application_id) {

                $application_found = true;


                // ------------------------------------------------
                // Approve
                // ------------------------------------------------

                if ($action === 'approve') {

                    if ($application['status'] === 'Approved') {

                        $error = 'This application is already approved.';

                    } elseif ($application['status'] === 'Rejected') {

                        $error = 'A rejected application cannot be approved directly.';

                    } else {

                        $application['status'] = 'Approved';

                        $success =
                            'Application APP-' .
                            $application_id .
                            ' has been approved.';
                    }


                // ------------------------------------------------
                // Reject
                // ------------------------------------------------

                } elseif ($action === 'reject') {

                    if ($application['status'] === 'Rejected') {

                        $error = 'This application is already rejected.';

                    } elseif ($application['status'] === 'Approved') {

                        $error =
                            'An approved application cannot be rejected directly.';

                    } else {

                        $application['status'] = 'Rejected';

                        $success =
                            'Application APP-' .
                            $application_id .
                            ' has been rejected.';
                    }

                }


                break;
            }

        }

        unset($application);


        if (!$application_found && $error === '') {

            $error = 'Application not found.';

        }

    }
}


// ------------------------------------------------------------
// Find selected application for details
// ------------------------------------------------------------

$selected_application = null;

if (isset($_GET['view'])) {

    $view_id = (int) $_GET['view'];


    foreach ($_SESSION['applications'] as $application) {

        if ($application['id'] == $view_id) {

            $selected_application = $application;

            break;
        }

    }
}


// ------------------------------------------------------------
// Count applications by status
// ------------------------------------------------------------

$total_applications = count($_SESSION['applications']);

$pending_count = 0;
$approved_count = 0;
$rejected_count = 0;


foreach ($_SESSION['applications'] as $application) {

    if ($application['status'] === 'Pending') {
        $pending_count++;
    }

    if ($application['status'] === 'Approved') {
        $approved_count++;
    }

    if ($application['status'] === 'Rejected') {
        $rejected_count++;
    }
}


$page_title = 'Application Management';

require_once '../includes/header.php';

?>


<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h2 class="fw-bold mb-1">
            Application Management
        </h2>

        <p class="text-muted mb-0">
            Review and manage student hostel applications.
        </p>

    </div>

</div>


<!-- Messages -->

<?php if ($success): ?>

    <div class="alert alert-success">
        <?= e($success) ?>
    </div>

<?php endif; ?>


<?php if ($error): ?>

    <div class="alert alert-danger">
        <?= e($error) ?>
    </div>

<?php endif; ?>


<!-- Application Statistics -->

<div class="row g-4 mb-4">

    <div class="col-md-6 col-lg-3">

        <div class="card shadow-sm">

            <div class="card-body">

                <h6 class="text-muted">
                    Total Applications
                </h6>

                <h3 class="fw-bold mb-0">
                    <?= $total_applications ?>
                </h3>

            </div>

        </div>

    </div>


    <div class="col-md-6 col-lg-3">

        <div class="card shadow-sm">

            <div class="card-body">

                <h6 class="text-muted">
                    Pending
                </h6>

                <h3 class="fw-bold text-warning mb-0">
                    <?= $pending_count ?>
                </h3>

            </div>

        </div>

    </div>


    <div class="col-md-6 col-lg-3">

        <div class="card shadow-sm">

            <div class="card-body">

                <h6 class="text-muted">
                    Approved
                </h6>

                <h3 class="fw-bold text-success mb-0">
                    <?= $approved_count ?>
                </h3>

            </div>

        </div>

    </div>


    <div class="col-md-6 col-lg-3">

        <div class="card shadow-sm">

            <div class="card-body">

                <h6 class="text-muted">
                    Rejected
                </h6>

                <h3 class="fw-bold text-danger mb-0">
                    <?= $rejected_count ?>
                </h3>

            </div>

        </div>

    </div>

</div>


<!-- Application Details -->

<?php if ($selected_application): ?>

    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">

                <h5 class="fw-bold mb-0">
                    Application Details
                </h5>

                <a
                    href="applications.php"
                    class="btn btn-sm btn-outline-secondary"
                >
                    Close Details
                </a>

            </div>


            <div class="table-responsive">

                <table class="table table-bordered align-middle mb-0">

                    <tbody>

                        <tr>

                            <th width="30%">
                                Application ID
                            </th>

                            <td>
                                APP-<?= e($selected_application['id']) ?>
                            </td>

                        </tr>


                        <tr>

                            <th>
                                Student ID
                            </th>

                            <td>
                                <?= e($selected_application['student_id_number']) ?>
                            </td>

                        </tr>


                        <tr>

                            <th>
                                Student Name
                            </th>

                            <td>
                                <?= e($selected_application['student_name']) ?>
                            </td>

                        </tr>


                        <tr>

                            <th>
                                Preferred Block
                            </th>

                            <td>
                                Block <?= e($selected_application['preferred_block']) ?>
                            </td>

                        </tr>


                        <tr>

                            <th>
                                Reason
                            </th>

                            <td>
                                <?= nl2br(e($selected_application['reason'])) ?>
                            </td>

                        </tr>


                        <tr>

                            <th>
                                Submitted At
                            </th>

                            <td>
                                <?= e($selected_application['submitted_at']) ?>
                            </td>

                        </tr>


                        <tr>

                            <th>
                                Status
                            </th>

                            <td>

                                <?php

                                $detail_status =
                                    $selected_application['status'];

                                if ($detail_status === 'Approved') {

                                    $detail_badge = 'bg-success';

                                } elseif ($detail_status === 'Rejected') {

                                    $detail_badge = 'bg-danger';

                                } else {

                                    $detail_badge =
                                        'bg-warning text-dark';
                                }

                                ?>

                                <span class="badge <?= $detail_badge ?> status-badge">

                                    <?= e($detail_status) ?>

                                </span>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>


            <?php if ($selected_application['status'] === 'Pending'): ?>

                <div class="mt-4 d-flex gap-2">

                    <form method="POST">

                        <input
                            type="hidden"
                            name="application_id"
                            value="<?= e($selected_application['id']) ?>"
                        >

                        <input
                            type="hidden"
                            name="action"
                            value="approve"
                        >

                        <button
                            type="submit"
                            class="btn btn-success"
                        >
                            Approve Application
                        </button>

                    </form>


                    <form method="POST">

                        <input
                            type="hidden"
                            name="application_id"
                            value="<?= e($selected_application['id']) ?>"
                        >

                        <input
                            type="hidden"
                            name="action"
                            value="reject"
                        >

                        <button
                            type="submit"
                            class="btn btn-danger"
                        >
                            Reject Application
                        </button>

                    </form>

                </div>

            <?php elseif ($selected_application['status'] === 'Approved'): ?>

                <div class="alert alert-success mt-4 mb-0">

                    This student is now eligible for room allocation.

                </div>

            <?php elseif ($selected_application['status'] === 'Rejected'): ?>

                <div class="alert alert-danger mt-4 mb-0">

                    This application was rejected.

                </div>

            <?php endif; ?>

        </div>

    </div>

<?php endif; ?>


<!-- Applications Table -->

<div class="card shadow-sm">

    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>

                <h5 class="fw-bold mb-1">
                    Student Applications
                </h5>

                <p class="text-muted mb-0">
                    All submitted hostel applications.
                </p>

            </div>

        </div>


        <?php if (empty($_SESSION['applications'])): ?>

            <div class="alert alert-info mb-0">

                No hostel applications have been submitted yet.

            </div>

        <?php else: ?>

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-light">

                        <tr>

                            <th>
                                Application
                            </th>

                            <th>
                                Student
                            </th>

                            <th>
                                Preferred Block
                            </th>

                            <th>
                                Submitted
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php foreach (
                            $_SESSION['applications']
                            as $application
                        ): ?>

                            <tr>

                                <td>

                                    <strong>
                                        APP-<?= e($application['id']) ?>
                                    </strong>

                                    <br>

                                    <small class="text-muted">
                                        <?= e($application['student_id_number']) ?>
                                    </small>

                                </td>


                                <td>
                                    <?= e($application['student_name']) ?>
                                </td>


                                <td>
                                    Block <?= e($application['preferred_block']) ?>
                                </td>


                                <td>
                                    <?= e($application['submitted_at']) ?>
                                </td>


                                <td>

                                    <?php

                                    $status =
                                        $application['status'];

                                    if ($status === 'Approved') {

                                        $badge = 'bg-success';

                                    } elseif ($status === 'Rejected') {

                                        $badge = 'bg-danger';

                                    } else {

                                        $badge =
                                            'bg-warning text-dark';
                                    }

                                    ?>

                                    <span class="badge <?= $badge ?> status-badge">

                                        <?= e($status) ?>

                                    </span>

                                </td>


                                <td>

                                    <a
                                        href="applications.php?view=<?= e($application['id']) ?>"
                                        class="btn btn-sm btn-outline-primary"
                                    >
                                        View Details
                                    </a>

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