<?php

require_once '../includes/admin_auth.php';

require_admin();


// ------------------------------------------------------------
// Handle Complaint Status Update
// ------------------------------------------------------------

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $form_action = $_POST['form_action'] ?? '';

    if ($form_action === 'update_status') {

        $complaint_id = (int) ($_POST['complaint_id'] ?? 0);

        $new_status = $_POST['status'] ?? '';

        $allowed_statuses = [
            'Pending',
            'In Progress',
            'Resolved'
        ];


        // ----------------------------------------------------
        // Validate Status
        // ----------------------------------------------------

        if (!in_array($new_status, $allowed_statuses, true)) {

            $error_message = 'Invalid complaint status.';

        } else {

            $complaint_found = false;


            // ------------------------------------------------
            // Find Complaint and Validate Status Transition
            // ------------------------------------------------

            foreach (
                $_SESSION['complaints']
                as $index => $complaint
            ) {

                if ($complaint['id'] == $complaint_id) {

                    $complaint_found = true;

                    $current_status = $complaint['status'];

                    $valid_transition = false;

                    if (
                        $current_status === 'Pending'
                        &&
                        $new_status === 'In Progress'
                    ) {

                        $valid_transition = true;

                    } elseif (
                        $current_status === 'In Progress'
                        &&
                        $new_status === 'Resolved'
                    ) {

                        $valid_transition = true;

                    } elseif (
                        $current_status === $new_status
                    ) {

                        $error_message =
                            'The complaint already has this status.';

                    }


                    if (!$valid_transition && $error_message === '') {

                        $error_message =
                            'Invalid status transition. Complaints must move from Pending to In Progress, then to Resolved.';

                    } elseif ($valid_transition) {

                        $_SESSION['complaints'][$index]['status']
                            = $new_status;

                        $success_message =
                            'Complaint status updated successfully.';
                    }

                    break;
                }

            }


            if (!$complaint_found && $error_message === '') {

                $error_message =
                    'Complaint could not be found.';
            }

        }

    }

}


// ------------------------------------------------------------
// Selected Complaint
// ------------------------------------------------------------

$selected_complaint = null;

$selected_id = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;


if ($selected_id > 0) {

    foreach ($_SESSION['complaints'] as $complaint) {

        if ($complaint['id'] == $selected_id) {

            $selected_complaint = $complaint;

            break;
        }

    }

}


// ------------------------------------------------------------
// Page
// ------------------------------------------------------------

$page_title = 'Complaint Management';

require_once '../includes/header.php';

?>


<div class="mb-4">

    <h2 class="fw-bold mb-1">
        Complaint Management
    </h2>

    <p class="text-muted mb-0">
        Review student complaints and update their status.
    </p>

</div>


<!-- Success Message -->

<?php if ($success_message): ?>

    <div class="alert alert-success">

        <?= e($success_message) ?>

    </div>

<?php endif; ?>


<!-- Error Message -->

<?php if ($error_message): ?>

    <div class="alert alert-danger">

        <?= e($error_message) ?>

    </div>

<?php endif; ?>


<!-- Complaint Details -->

<?php if ($selected_complaint): ?>

    <div class="card shadow-sm mb-4">

        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-center mb-3">

                <h5 class="fw-bold mb-0">
                    Complaint Details
                </h5>

                <a
                    href="complaints.php"
                    class="btn btn-outline-secondary btn-sm"
                >
                    Close Details
                </a>

            </div>


            <div class="table-responsive">

                <table class="table table-bordered mb-0">

                    <tbody>

                        <tr>

                            <th style="width: 25%;">
                                Complaint ID
                            </th>

                            <td>
                                <?= e($selected_complaint['id']) ?>
                            </td>

                        </tr>


                        <tr>

                            <th>
                                Student
                            </th>

                            <td>
                                <?= e(
                                    $selected_complaint['student_name']
                                ) ?>
                            </td>

                        </tr>


                        <tr>

                            <th>
                                Student ID
                            </th>

                            <td>
                                <?= e(
                                    $selected_complaint[
                                        'student_id_number'
                                    ]
                                ) ?>
                            </td>

                        </tr>


                        <tr>

                            <th>
                                Subject
                            </th>

                            <td>
                                <?= e(
                                    $selected_complaint['subject']
                                ) ?>
                            </td>

                        </tr>


                        <tr>

                            <th>
                                Description
                            </th>

                            <td>
                                <?= nl2br(
                                    e(
                                        $selected_complaint[
                                            'description'
                                        ]
                                    )
                                ) ?>
                            </td>

                        </tr>


                        <tr>

                            <th>
                                Submitted At
                            </th>

                            <td>
                                <?= e(
                                    $selected_complaint[
                                        'submitted_at'
                                    ]
                                ) ?>
                            </td>

                        </tr>


                        <tr>

                            <th>
                                Current Status
                            </th>

                            <td>

                                <?php

                                $detail_status =
                                    $selected_complaint['status'];

                                $detail_badge =
                                    'bg-warning text-dark';

                                if (
                                    $detail_status ===
                                    'In Progress'
                                ) {

                                    $detail_badge =
                                        'bg-primary';

                                } elseif (
                                    $detail_status ===
                                    'Resolved'
                                ) {

                                    $detail_badge =
                                        'bg-success';

                                }

                                ?>

                                <span
                                    class="badge
                                    <?= $detail_badge ?>
                                    status-badge"
                                >

                                    <?= e($detail_status) ?>

                                </span>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>


            <!-- Update Status -->

            <div class="mt-4">

                <h6 class="fw-bold mb-3">
                    Update Complaint Status
                </h6>


                <form
                    method="POST"
                    class="row g-3 align-items-end"
                >

                    <input
                        type="hidden"
                        name="form_action"
                        value="update_status"
                    >

                    <input
                        type="hidden"
                        name="complaint_id"
                        value="<?= e(
                            $selected_complaint['id']
                        ) ?>"
                    >


                    <div class="col-md-6">

                        <label
                            for="status"
                            class="form-label"
                        >
                            Status
                        </label>

                        <select
                            id="status"
                            name="status"
                            class="form-select"
                            required
                        >

                            <option
                                value="Pending"
                                <?= $selected_complaint['status']
                                    === 'Pending'
                                    ? 'selected'
                                    : '' ?>
                            >
                                Pending
                            </option>

                            <option
                                value="In Progress"
                                <?= $selected_complaint['status']
                                    === 'In Progress'
                                    ? 'selected'
                                    : '' ?>
                            >
                                In Progress
                            </option>

                            <option
                                value="Resolved"
                                <?= $selected_complaint['status']
                                    === 'Resolved'
                                    ? 'selected'
                                    : '' ?>
                            >
                                Resolved
                            </option>

                        </select>

                    </div>


                    <div class="col-md-auto">

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            Update Status
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

<?php elseif ($selected_id > 0): ?>

    <div class="alert alert-danger">

        The selected complaint could not be found.

    </div>

<?php endif; ?>


<!-- All Complaints -->

<div class="card shadow-sm">

    <div class="card-body p-4">

        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>

                <h5 class="fw-bold mb-1">
                    All Complaints
                </h5>

                <p class="text-muted mb-0">
                    Complaints submitted by all students.
                </p>

            </div>

            <span class="badge bg-secondary">

                <?= count($_SESSION['complaints']) ?>

                Complaints

            </span>

        </div>


        <?php if (empty($_SESSION['complaints'])): ?>

            <div class="alert alert-info mb-0">

                No complaints have been submitted yet.

            </div>

        <?php else: ?>


            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-light">

                        <tr>

                            <th>
                                Student
                            </th>

                            <th>
                                Student ID
                            </th>

                            <th>
                                Subject
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
                            array_reverse($_SESSION['complaints'])
                            as $complaint
                        ): ?>

                            <?php

                            $status =
                                $complaint['status'];

                            $badge_class =
                                'bg-warning text-dark';

                            if ($status === 'In Progress') {

                                $badge_class =
                                    'bg-primary';

                            } elseif (
                                $status === 'Resolved'
                            ) {

                                $badge_class =
                                    'bg-success';

                            }

                            ?>


                            <tr>

                                <td>

                                    <?= e(
                                        $complaint['student_name']
                                    ) ?>

                                </td>


                                <td>

                                    <?= e(
                                        $complaint[
                                            'student_id_number'
                                        ]
                                    ) ?>

                                </td>


                                <td>

                                    <?= e(
                                        $complaint['subject']
                                    ) ?>

                                </td>


                                <td>

                                    <?= e(
                                        $complaint['submitted_at']
                                    ) ?>

                                </td>


                                <td>

                                    <span
                                        class="badge
                                        <?= $badge_class ?>
                                        status-badge"
                                    >

                                        <?= e($status) ?>

                                    </span>

                                </td>


                                <td>

                                    <a
                                        href="complaints.php?id=<?= e(
                                            $complaint['id']
                                        ) ?>"
                                        class="btn btn-sm
                                        btn-outline-primary"
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