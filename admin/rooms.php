<?php

require_once '../includes/admin_auth.php';

require_admin();


$success = '';
$error = '';


// ------------------------------------------------------------
// Add Room
// ------------------------------------------------------------

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    &&
    ($_POST['form_action'] ?? '') === 'add_room'
) {

    $room_number = trim($_POST['room_number'] ?? '');
    $block = trim($_POST['block'] ?? '');
    $capacity = (int) ($_POST['capacity'] ?? 0);


    if (
        $room_number === ''
        ||
        $block === ''
        ||
        $capacity <= 0
    ) {

        $error = 'Please provide valid room information.';

    } else {

        // Check duplicate room number

        $duplicate = false;

        foreach ($_SESSION['rooms'] as $room) {

            if (
                strtolower($room['room_number'])
                === strtolower($room_number)
            ) {

                $duplicate = true;

                break;
            }
        }


        if ($duplicate) {

            $error = 'A room with this room number already exists.';

        } else {

            $room_id = 1;


            if (!empty($_SESSION['rooms'])) {

                $ids = array_column(
                    $_SESSION['rooms'],
                    'id'
                );

                $room_id = max($ids) + 1;
            }


            $_SESSION['rooms'][] = [

                'id' => $room_id,

                'room_number' => $room_number,

                'block' => $block,

                'capacity' => $capacity,

                'occupancy' => 0

            ];


            $success =
                'Room ' .
                $room_number .
                ' was added successfully.';
        }
    }
}


// ------------------------------------------------------------
// Edit Room
// ------------------------------------------------------------

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    &&
    ($_POST['form_action'] ?? '') === 'edit_room'
) {

    $room_id = (int) ($_POST['room_id'] ?? 0);

    $room_number = trim($_POST['room_number'] ?? '');
    $block = trim($_POST['block'] ?? '');
    $capacity = (int) ($_POST['capacity'] ?? 0);


    if (
        $room_id <= 0
        ||
        $room_number === ''
        ||
        $block === ''
        ||
        $capacity <= 0
    ) {

        $error = 'Please provide valid room information.';

    } else {

        // Check duplicate room number, excluding the room being edited.

        $duplicate = false;

        foreach ($_SESSION['rooms'] as $existing_room) {

            if (
                $existing_room['id'] != $room_id
                &&
                strtolower($existing_room['room_number'])
                    === strtolower($room_number)
            ) {

                $duplicate = true;

                break;
            }
        }


        if ($duplicate) {

            $error = 'A room with this room number already exists.';

        } else {

            $room_found = false;


            foreach ($_SESSION['rooms'] as &$room) {

            if ($room['id'] == $room_id) {

                $room_found = true;


                // Capacity cannot be smaller than current occupancy

                if ($capacity < $room['occupancy']) {

                    $error =
                        'Capacity cannot be lower than current occupancy.';

                } else {

                    $room['room_number'] = $room_number;

                    $room['block'] = $block;

                    $room['capacity'] = $capacity;


                    $success =
                        'Room information updated successfully.';
                }


                break;
            }
        }


            unset($room);


            if (!$room_found && $error === '') {

                $error = 'Room not found.';

            } elseif ($room_found && $error === '') {

                // Keep allocation snapshots consistent with the room record.

                foreach (
                    $_SESSION['allocations']
                    as &$allocation
                ) {

                    if ($allocation['room_id'] == $room_id) {

                        $allocation['room_number'] = $room_number;

                        $allocation['block'] = $block;

                    }
                }

                unset($allocation);
            }
        }
    }
}


// ------------------------------------------------------------
// Delete Room
// ------------------------------------------------------------

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    &&
    ($_POST['form_action'] ?? '') === 'delete_room'
) {

    $room_id = (int) ($_POST['room_id'] ?? 0);


    if ($room_id <= 0) {

        $error = 'Invalid room selected.';

    } else {

        $room_found = false;

        $room_has_allocation = false;


        foreach ($_SESSION['rooms'] as $room) {

            if ($room['id'] == $room_id) {

                $room_found = true;


                if ($room['occupancy'] > 0) {

                    $room_has_allocation = true;
                }


                break;
            }
        }


        if (!$room_found) {

            $error = 'Room not found.';

        } elseif ($room_has_allocation) {

            $error =
                'A room with current occupants cannot be deleted.';

        } else {

            foreach ($_SESSION['rooms'] as $index => $room) {

                if ($room['id'] == $room_id) {

                    unset($_SESSION['rooms'][$index]);

                    break;
                }
            }


            $_SESSION['rooms'] =
                array_values($_SESSION['rooms']);


            $success = 'Room deleted successfully.';
        }
    }
}


// ------------------------------------------------------------
// Room Allocation
// ------------------------------------------------------------

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    &&
    ($_POST['form_action'] ?? '') === 'allocate_room'
) {

    $student_id = (int) ($_POST['student_id'] ?? 0);

    $room_id = (int) ($_POST['room_id'] ?? 0);


    if ($student_id <= 0 || $room_id <= 0) {

        $error =
            'Please select both a student and a room.';

    } else {

        $selected_student = null;
        $selected_room = null;


        // ----------------------------------------------------
        // Find student
        // ----------------------------------------------------

        foreach ($_SESSION['students'] as $student) {

            if ($student['id'] == $student_id) {

                $selected_student = $student;

                break;
            }
        }


        // ----------------------------------------------------
        // Find room
        // ----------------------------------------------------

        foreach ($_SESSION['rooms'] as $room) {

            if ($room['id'] == $room_id) {

                $selected_room = $room;

                break;
            }
        }


        if (!$selected_student) {

            $error = 'Student not found.';

        } elseif (!$selected_room) {

            $error = 'Room not found.';

        } else {

            // ------------------------------------------------
            // Check approved application
            // ------------------------------------------------

            $approved = false;


            foreach (
                $_SESSION['applications']
                as $application
            ) {

                if (
                    $application['student_id']
                    == $student_id
                    &&
                    $application['status']
                    === 'Approved'
                ) {

                    $approved = true;

                    break;
                }
            }


            if (!$approved) {

                $error =
                    'Only students with approved hostel applications can receive a room.';

            } else {

                // --------------------------------------------
                // Check existing allocation
                // --------------------------------------------

                $already_allocated = false;


                foreach (
                    $_SESSION['allocations']
                    as $allocation
                ) {

                    if (
                        $allocation['student_id']
                        == $student_id
                    ) {

                        $already_allocated = true;

                        break;
                    }
                }


                if ($already_allocated) {

                    $error =
                        'This student already has an assigned room.';

                } elseif (
                    $selected_room['occupancy']
                    >=
                    $selected_room['capacity']
                ) {

                    $error =
                        'This room is already full.';

                } else {

                    // ----------------------------------------
                    // Create allocation
                    // ----------------------------------------

                    $allocation_id = 1;


                    if (!empty($_SESSION['allocations'])) {

                        $ids = array_column(
                            $_SESSION['allocations'],
                            'id'
                        );

                        $allocation_id =
                            max($ids) + 1;
                    }


                    $_SESSION['allocations'][] = [

                        'id' => $allocation_id,

                        'student_id' =>
                            $selected_student['id'],

                        'student_name' =>
                            $selected_student['name'],

                        'student_id_number' =>
                            $selected_student['student_id'],

                        'room_id' =>
                            $selected_room['id'],

                        'room_number' =>
                            $selected_room['room_number'],

                        'block' =>
                            $selected_room['block'],

                        'allocated_at' =>
                            date('Y-m-d H:i:s')

                    ];


                    // ----------------------------------------
                    // Update room occupancy
                    // ----------------------------------------

                    foreach (
                        $_SESSION['rooms']
                        as &$room
                    ) {

                        if (
                            $room['id']
                            ==
                            $selected_room['id']
                        ) {

                            $room['occupancy']++;

                            break;
                        }
                    }


                    unset($room);


                    $success =
                        'Room ' .
                        $selected_room['room_number'] .
                        ' has been assigned to ' .
                        $selected_student['name'] .
                        '.';
                }
            }
        }
    }
}


// ------------------------------------------------------------
// Get approved students without room allocation
// ------------------------------------------------------------

$eligible_students = [];


foreach ($_SESSION['students'] as $student) {

    $approved = false;


    foreach (
        $_SESSION['applications']
        as $application
    ) {

        if (
            $application['student_id']
            == $student['id']
            &&
            $application['status']
            === 'Approved'
        ) {

            $approved = true;

            break;
        }
    }


    if (!$approved) {
        continue;
    }


    // Check whether student already has room

    $has_room = false;


    foreach (
        $_SESSION['allocations']
        as $allocation
    ) {

        if (
            $allocation['student_id']
            == $student['id']
        ) {

            $has_room = true;

            break;
        }
    }


    if (!$has_room) {

        $eligible_students[] = $student;
    }
}


// ------------------------------------------------------------
// Available rooms
// ------------------------------------------------------------

$available_rooms = [];


foreach ($_SESSION['rooms'] as $room) {

    if ($room['occupancy'] < $room['capacity']) {

        $available_rooms[] = $room;
    }
}


$page_title = 'Room Management';

require_once '../includes/header.php';

?>


<div class="mb-4">

    <h2 class="fw-bold mb-1">
        Room Management
    </h2>

    <p class="text-muted mb-0">
        Manage hostel rooms and allocate rooms to approved students.
    </p>

</div>


<!-- Alerts -->

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


<!-- Room Statistics -->

<div class="row g-4 mb-4">

    <div class="col-md-4">

        <div class="card shadow-sm">

            <div class="card-body">

                <h6 class="text-muted">
                    Total Rooms
                </h6>

                <h3 class="fw-bold mb-0">
                    <?= count($_SESSION['rooms']) ?>
                </h3>

            </div>

        </div>

    </div>


    <div class="col-md-4">

        <div class="card shadow-sm">

            <div class="card-body">

                <h6 class="text-muted">
                    Available Rooms
                </h6>

                <h3 class="fw-bold text-success mb-0">
                    <?= count($available_rooms) ?>
                </h3>

            </div>

        </div>

    </div>


    <div class="col-md-4">

        <div class="card shadow-sm">

            <div class="card-body">

                <h6 class="text-muted">
                    Eligible Students
                </h6>

                <h3 class="fw-bold text-primary mb-0">
                    <?= count($eligible_students) ?>
                </h3>

            </div>

        </div>

    </div>

</div>


<!-- Add Room -->

<div class="card shadow-sm mb-4">

    <div class="card-body">

        <h5 class="fw-bold mb-3">
            Add New Room
        </h5>


        <form method="POST">

            <input
                type="hidden"
                name="form_action"
                value="add_room"
            >


            <div class="row g-3">


                <div class="col-md-4">

                    <label class="form-label">
                        Room Number
                    </label>

                    <input
                        type="text"
                        name="room_number"
                        class="form-control"
                        placeholder="Example: A-103"
                        required
                    >

                </div>


                <div class="col-md-4">

                    <label class="form-label">
                        Block
                    </label>

                    <select
                        name="block"
                        class="form-select"
                        required
                    >

                        <option value="">
                            Select block
                        </option>

                        <option value="A">
                            Block A
                        </option>

                        <option value="B">
                            Block B
                        </option>

                    </select>

                </div>


                <div class="col-md-4">

                    <label class="form-label">
                        Capacity
                    </label>

                    <input
                        type="number"
                        name="capacity"
                        class="form-control"
                        min="1"
                        max="10"
                        required
                    >

                </div>


            </div>


            <button
                type="submit"
                class="btn btn-primary mt-3"
            >
                Add Room
            </button>

        </form>

    </div>

</div>


<!-- Room Allocation -->

<div class="card shadow-sm mb-4">

    <div class="card-body">

        <h5 class="fw-bold mb-1">
            Allocate Room
        </h5>

        <p class="text-muted">
            Only approved students without an existing allocation
            are shown.
        </p>


        <?php if (empty($eligible_students)): ?>

            <div class="alert alert-info mb-0">

                There are currently no approved students waiting
                for room allocation.

            </div>

        <?php elseif (empty($available_rooms)): ?>

            <div class="alert alert-warning mb-0">

                There are no rooms with available capacity.

            </div>

        <?php else: ?>

            <form method="POST">

                <input
                    type="hidden"
                    name="form_action"
                    value="allocate_room"
                >


                <div class="row g-3">


                    <div class="col-md-6">

                        <label class="form-label">
                            Student
                        </label>

                        <select
                            name="student_id"
                            class="form-select"
                            required
                        >

                            <option value="">
                                Select approved student
                            </option>


                            <?php foreach (
                                $eligible_students
                                as $student
                            ): ?>

                                <option
                                    value="<?= e($student['id']) ?>"
                                >

                                    <?= e($student['name']) ?>

                                    -
                                    <?= e($student['student_id']) ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>


                    <div class="col-md-6">

                        <label class="form-label">
                            Room
                        </label>

                        <select
                            name="room_id"
                            class="form-select"
                            required
                        >

                            <option value="">
                                Select available room
                            </option>


                            <?php foreach (
                                $available_rooms
                                as $room
                            ): ?>

                                <option
                                    value="<?= e($room['id']) ?>"
                                >

                                    <?= e($room['room_number']) ?>

                                    -
                                    Block <?= e($room['block']) ?>

                                    -

                                    <?= e($room['occupancy']) ?>
                                    /
                                    <?= e($room['capacity']) ?>

                                    occupied

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>


                </div>


                <button
                    type="submit"
                    class="btn btn-success mt-3"
                >

                    Assign Room

                </button>

            </form>

        <?php endif; ?>

    </div>

</div>


<!-- Rooms Table -->

<div class="card shadow-sm mb-4">

    <div class="card-body">

        <h5 class="fw-bold mb-3">
            Hostel Rooms
        </h5>


        <?php if (empty($_SESSION['rooms'])): ?>

            <div class="alert alert-info mb-0">
                No rooms have been created.
            </div>

        <?php else: ?>

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-light">

                        <tr>

                            <th>
                                Room
                            </th>

                            <th>
                                Block
                            </th>

                            <th>
                                Capacity
                            </th>

                            <th>
                                Occupancy
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
                            $_SESSION['rooms']
                            as $room
                        ): ?>

                            <tr>

                                <td>
                                    <strong>
                                        <?= e($room['room_number']) ?>
                                    </strong>
                                </td>


                                <td>
                                    Block <?= e($room['block']) ?>
                                </td>


                                <td>
                                    <?= e($room['capacity']) ?>
                                </td>


                                <td>

                                    <?= e($room['occupancy']) ?>

                                    /

                                    <?= e($room['capacity']) ?>

                                </td>


                                <td>

                                    <?php if (
                                        $room['occupancy']
                                        >=
                                        $room['capacity']
                                    ): ?>

                                        <span class="badge bg-danger">
                                            Full
                                        </span>

                                    <?php else: ?>

                                        <span class="badge bg-success">
                                            Available
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <td>

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editRoom<?= e($room['id']) ?>"
                                    >
                                        Edit
                                    </button>

                                    <?php if ($room['occupancy'] == 0): ?>

                                        <form
                                            method="POST"
                                            class="d-inline"
                                        >

                                            <input
                                                type="hidden"
                                                name="form_action"
                                                value="delete_room"
                                            >

                                            <input
                                                type="hidden"
                                                name="room_id"
                                                value="<?= e($room['id']) ?>"
                                            >

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Delete this room?');"
                                            >
                                                Delete
                                            </button>

                                        </form>

                                    <?php else: ?>

                                        <span class="text-muted small ms-1">
                                            Occupied
                                        </span>

                                    <?php endif; ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php endif; ?>

    </div>

</div>


<?php foreach ($_SESSION['rooms'] as $room): ?>

    <div
        class="modal fade"
        id="editRoom<?= e($room['id']) ?>"
        tabindex="-1"
        aria-hidden="true"
    >

        <div class="modal-dialog">

            <div class="modal-content">

                <form method="POST">

                    <div class="modal-header">

                        <h5 class="modal-title">
                            Edit Room
                        </h5>

                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                        ></button>

                    </div>

                    <div class="modal-body">

                        <input
                            type="hidden"
                            name="form_action"
                            value="edit_room"
                        >

                        <input
                            type="hidden"
                            name="room_id"
                            value="<?= e($room['id']) ?>"
                        >

                        <div class="mb-3">

                            <label class="form-label">
                                Room Number
                            </label>

                            <input
                                type="text"
                                name="room_number"
                                class="form-control"
                                value="<?= e($room['room_number']) ?>"
                                required
                            >

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Block
                            </label>

                            <select
                                name="block"
                                class="form-select"
                                required
                            >

                                <option
                                    value="A"
                                    <?= $room['block'] === 'A'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Block A
                                </option>

                                <option
                                    value="B"
                                    <?= $room['block'] === 'B'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Block B
                                </option>

                            </select>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Capacity
                            </label>

                            <input
                                type="number"
                                name="capacity"
                                class="form-control"
                                min="<?= e($room['occupancy']) ?>"
                                max="10"
                                value="<?= e($room['capacity']) ?>"
                                required
                            >

                            <div class="form-text">

                                Current occupancy:
                                <?= e($room['occupancy']) ?>

                            </div>

                        </div>

                    </div>

                    <div class="modal-footer">

                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                        >
                            Cancel
                        </button>

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            Save Changes
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

<?php endforeach; ?>


<!-- Existing Allocations -->

<div class="card shadow-sm">

    <div class="card-body">

        <h5 class="fw-bold mb-3">
            Current Room Allocations
        </h5>


        <?php if (empty($_SESSION['allocations'])): ?>

            <div class="alert alert-info mb-0">

                No room allocations have been made yet.

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
                                Room
                            </th>

                            <th>
                                Block
                            </th>

                            <th>
                                Allocated At
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php foreach (
                            $_SESSION['allocations']
                            as $allocation
                        ): ?>

                            <tr>

                                <td>
                                    <?= e($allocation['student_name']) ?>
                                </td>

                                <td>
                                    <?= e($allocation['student_id_number']) ?>
                                </td>

                                <td>
                                    <strong>
                                        <?= e($allocation['room_number']) ?>
                                    </strong>
                                </td>

                                <td>
                                    Block <?= e($allocation['block']) ?>
                                </td>

                                <td>
                                    <?= e($allocation['allocated_at']) ?>
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