<?php

require_once '../includes/admin_auth.php';

require_admin();


// ------------------------------------------------------------
// Messages
// ------------------------------------------------------------

$success_message = '';
$error_message = '';


// ------------------------------------------------------------
// Form Handling
// ------------------------------------------------------------

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $form_action = $_POST['form_action'] ?? '';


    // ========================================================
    // Create Notice
    // ========================================================

    if ($form_action === 'create') {

        $title = trim($_POST['title'] ?? '');

        $content = trim($_POST['content'] ?? '');


        if ($title === '' || $content === '') {

            $error_message =
                'Please fill in both the title and content.';

        } else {


            // ------------------------------------------------
            // Generate New Notice ID
            // ------------------------------------------------

            $new_id = 1;

            if (!empty($_SESSION['notices'])) {

                $ids = array_column(
                    $_SESSION['notices'],
                    'id'
                );

                $new_id = max($ids) + 1;

            }


            // ------------------------------------------------
            // Create Notice
            // ------------------------------------------------

            $_SESSION['notices'][] = [

                'id' => $new_id,

                'title' => $title,

                'content' => $content,

                'status' => 'Published',

                'created_at' => date('Y-m-d H:i:s')

            ];


            $success_message =
                'Notice published successfully.';

        }

    }


    // ========================================================
    // Edit Notice
    // ========================================================

    elseif ($form_action === 'edit') {

        $notice_id = (int) ($_POST['notice_id'] ?? 0);

        $title = trim($_POST['title'] ?? '');

        $content = trim($_POST['content'] ?? '');


        if ($notice_id <= 0) {

            $error_message =
                'Invalid notice selected.';

        } elseif (
            $title === '' ||
            $content === ''
        ) {

            $error_message =
                'Please fill in both the title and content.';

        } else {

            $notice_found = false;


            foreach (
                $_SESSION['notices']
                as $index => $notice
            ) {

                if ($notice['id'] == $notice_id) {

                    $_SESSION['notices'][$index]['title']
                        = $title;

                    $_SESSION['notices'][$index]['content']
                        = $content;

                    $notice_found = true;

                    break;

                }

            }


            if ($notice_found) {

                $success_message =
                    'Notice updated successfully.';

            } else {

                $error_message =
                    'Notice could not be found.';

            }

        }

    }


    // ========================================================
    // Delete Notice
    // ========================================================

    elseif ($form_action === 'delete') {

        $notice_id = (int) ($_POST['notice_id'] ?? 0);


        if ($notice_id <= 0) {

            $error_message =
                'Invalid notice selected.';

        } else {

            $notice_found = false;


            foreach (
                $_SESSION['notices']
                as $index => $notice
            ) {

                if ($notice['id'] == $notice_id) {

                    unset($_SESSION['notices'][$index]);

                    $notice_found = true;

                    break;

                }

            }


            if ($notice_found) {

                // Re-index the array after deletion
                $_SESSION['notices'] =
                    array_values($_SESSION['notices']);


                $success_message =
                    'Notice deleted successfully.';

            } else {

                $error_message =
                    'Notice could not be found.';

            }

        }

    }

}


// ------------------------------------------------------------
// Edit Mode
// ------------------------------------------------------------

$edit_notice = null;

$edit_id = isset($_GET['edit'])
    ? (int) $_GET['edit']
    : 0;


if ($edit_id > 0) {

    foreach ($_SESSION['notices'] as $notice) {

        if ($notice['id'] == $edit_id) {

            $edit_notice = $notice;

            break;

        }

    }

}


// ------------------------------------------------------------
// Sort Notices by Latest First
// ------------------------------------------------------------

$notices = $_SESSION['notices'];

usort(
    $notices,
    function ($a, $b) {

        return strtotime($b['created_at'])
            <=> strtotime($a['created_at']);

    }
);


// ------------------------------------------------------------
// Page
// ------------------------------------------------------------

$page_title = 'Notice Management';

require_once '../includes/header.php';

?>


<div class="mb-4">

    <h2 class="fw-bold mb-1">
        Notice Management
    </h2>

    <p class="text-muted mb-0">
        Create and manage hostel announcements.
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


<div class="row g-4">


    <!-- Create / Edit Notice -->

    <div class="col-lg-5">

        <div class="card shadow-sm">

            <div class="card-body p-4">


                <?php if ($edit_notice): ?>

                    <h5 class="fw-bold mb-3">
                        Edit Notice
                    </h5>

                <?php else: ?>

                    <h5 class="fw-bold mb-3">
                        Create Notice
                    </h5>

                <?php endif; ?>


                <form method="POST">


                    <?php if ($edit_notice): ?>

                        <input
                            type="hidden"
                            name="form_action"
                            value="edit"
                        >

                        <input
                            type="hidden"
                            name="notice_id"
                            value="<?= e(
                                $edit_notice['id']
                            ) ?>"
                        >

                    <?php else: ?>

                        <input
                            type="hidden"
                            name="form_action"
                            value="create"
                        >

                    <?php endif; ?>


                    <!-- Title -->

                    <div class="mb-3">

                        <label
                            for="title"
                            class="form-label"
                        >
                            Notice Title
                        </label>

                        <input
                            type="text"
                            id="title"
                            name="title"
                            class="form-control"
                            maxlength="150"
                            value="<?= $edit_notice
                                ? e($edit_notice['title'])
                                : '' ?>"
                            placeholder="Enter notice title"
                            required
                        >

                    </div>


                    <!-- Content -->

                    <div class="mb-3">

                        <label
                            for="content"
                            class="form-label"
                        >
                            Notice Content
                        </label>

                        <textarea
                            id="content"
                            name="content"
                            class="form-control"
                            rows="6"
                            maxlength="2000"
                            placeholder="Enter notice content"
                            required
                        ><?= $edit_notice
                            ? e($edit_notice['content'])
                            : '' ?></textarea>

                    </div>


                    <div class="d-flex gap-2">

                        <?php if ($edit_notice): ?>

                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
                                Update Notice
                            </button>

                            <a
                                href="notices.php"
                                class="btn btn-outline-secondary"
                            >
                                Cancel
                            </a>

                        <?php else: ?>

                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
                                Publish Notice
                            </button>

                        <?php endif; ?>

                    </div>


                </form>

            </div>

        </div>

    </div>


    <!-- Notice List -->

    <div class="col-lg-7">

        <div class="card shadow-sm">

            <div class="card-body p-4">


                <div
                    class="d-flex
                    justify-content-between
                    align-items-center
                    mb-3"
                >

                    <div>

                        <h5 class="fw-bold mb-1">
                            All Notices
                        </h5>

                        <p class="text-muted mb-0">
                            Manage existing hostel announcements.
                        </p>

                    </div>


                    <span class="badge bg-secondary">

                        <?= count($notices) ?>

                        Notices

                    </span>

                </div>


                <?php if (empty($notices)): ?>

                    <div class="alert alert-info mb-0">

                        No notices have been created yet.

                    </div>

                <?php else: ?>


                    <div class="table-responsive">

                        <table class="table table-hover align-middle">

                            <thead class="table-light">

                                <tr>

                                    <th>
                                        Title
                                    </th>

                                    <th>
                                        Date
                                    </th>

                                    <th>
                                        Status
                                    </th>

                                    <th>
                                        Actions
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                <?php foreach ($notices as $notice): ?>

                                    <tr>

                                        <td>

                                            <strong>

                                                <?= e(
                                                    $notice['title']
                                                ) ?>

                                            </strong>

                                            <br>

                                            <small class="text-muted">

                                                <?= e(
                                                    $notice['content']
                                                ) ?>

                                            </small>

                                        </td>


                                        <td>

                                            <?= e(
                                                $notice['created_at']
                                            ) ?>

                                        </td>


                                        <td>

                                            <?php if (
                                                $notice['status']
                                                === 'Published'
                                            ): ?>

                                                <span
                                                    class="badge
                                                    bg-success"
                                                >
                                                    Published
                                                </span>

                                            <?php else: ?>

                                                <span
                                                    class="badge
                                                    bg-secondary"
                                                >
                                                    <?= e(
                                                        $notice['status']
                                                    ) ?>
                                                </span>

                                            <?php endif; ?>

                                        </td>


                                        <td>

                                            <div class="d-flex gap-2">


                                                <!-- Edit -->

                                                <a
                                                    href="notices.php?edit=<?= e(
                                                        $notice['id']
                                                    ) ?>"
                                                    class="btn
                                                    btn-sm
                                                    btn-outline-primary"
                                                >
                                                    Edit
                                                </a>


                                                <!-- Delete -->

                                                <form
                                                    method="POST"
                                                    onsubmit="
                                                        return confirm(
                                                            'Are you sure you want to delete this notice?'
                                                        );
                                                    "
                                                >

                                                    <input
                                                        type="hidden"
                                                        name="form_action"
                                                        value="delete"
                                                    >

                                                    <input
                                                        type="hidden"
                                                        name="notice_id"
                                                        value="<?= e(
                                                            $notice['id']
                                                        ) ?>"
                                                    >

                                                    <button
                                                        type="submit"
                                                        class="btn
                                                        btn-sm
                                                        btn-outline-danger"
                                                    >
                                                        Delete
                                                    </button>

                                                </form>


                                            </div>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>


                <?php endif; ?>


            </div>

        </div>

    </div>

</div>


<?php require_once '../includes/footer.php'; ?>