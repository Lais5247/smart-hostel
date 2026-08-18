<?php

require_once '../includes/admin_auth.php';

require_admin();


// ------------------------------------------------------------
// Dashboard Statistics
// ------------------------------------------------------------

$total_students = count($_SESSION['students']);

$total_rooms = count($_SESSION['rooms']);

$available_rooms = 0;

foreach ($_SESSION['rooms'] as $room) {

    if ($room['occupancy'] < $room['capacity']) {
        $available_rooms++;
    }

}


$pending_applications = 0;

foreach ($_SESSION['applications'] as $application) {

    if ($application['status'] === 'Pending') {
        $pending_applications++;
    }

}


$pending_complaints = 0;

foreach ($_SESSION['complaints'] as $complaint) {

    if ($complaint['status'] === 'Pending') {
        $pending_complaints++;
    }

}


$page_title = 'Admin Dashboard';

require_once '../includes/header.php';

?>


<div class="mb-4">

    <h2 class="fw-bold mb-1">
        Hostel Administrator Dashboard
    </h2>

    <p class="text-muted mb-0">
        Overview of the Smart Hostel Management System.
    </p>

</div>


<!-- Dashboard Statistics -->

<div class="row g-4 mb-4">


    <!-- Students -->

    <div class="col-md-6 col-lg">

        <div class="card shadow-sm dashboard-card h-100">

            <div class="card-body">

                <h6 class="text-muted">
                    Total Students
                </h6>

                <h2 class="fw-bold mb-0">
                    <?= $total_students ?>
                </h2>

            </div>

        </div>

    </div>


    <!-- Rooms -->

    <div class="col-md-6 col-lg">

        <div class="card shadow-sm dashboard-card h-100">

            <div class="card-body">

                <h6 class="text-muted">
                    Total Rooms
                </h6>

                <h2 class="fw-bold mb-0">
                    <?= $total_rooms ?>
                </h2>

            </div>

        </div>

    </div>


    <!-- Available Rooms -->

    <div class="col-md-6 col-lg">

        <div class="card shadow-sm dashboard-card h-100">

            <div class="card-body">

                <h6 class="text-muted">
                    Available Rooms
                </h6>

                <h2 class="fw-bold text-success mb-0">
                    <?= $available_rooms ?>
                </h2>

            </div>

        </div>

    </div>


    <!-- Pending Applications -->

    <div class="col-md-6 col-lg">

        <div class="card shadow-sm dashboard-card h-100">

            <div class="card-body">

                <h6 class="text-muted">
                    Pending Applications
                </h6>

                <h2 class="fw-bold text-warning mb-0">
                    <?= $pending_applications ?>
                </h2>

            </div>

        </div>

    </div>


    <!-- Pending Complaints -->

    <div class="col-md-6 col-lg">

        <div class="card shadow-sm dashboard-card h-100">

            <div class="card-body">

                <h6 class="text-muted">
                    Pending Complaints
                </h6>

                <h2 class="fw-bold text-danger mb-0">
                    <?= $pending_complaints ?>
                </h2>

            </div>

        </div>

    </div>

</div>


<!-- Quick Actions -->

<div class="row g-4">


    <div class="col-md-6 col-lg-4">

        <div class="card shadow-sm h-100">

            <div class="card-body">

                <h5 class="fw-bold">
                    Applications
                </h5>

                <p class="text-muted">
                    Review student hostel applications
                    and approve or reject them.
                </p>

                <a
                    href="applications.php"
                    class="btn btn-primary"
                >
                    Manage Applications
                </a>

            </div>

        </div>

    </div>


    <div class="col-md-6 col-lg-4">

        <div class="card shadow-sm h-100">

            <div class="card-body">

                <h5 class="fw-bold">
                    Room Management
                </h5>

                <p class="text-muted">
                    Manage hostel rooms and allocate
                    rooms to approved students.
                </p>

                <a
                    href="rooms.php"
                    class="btn btn-primary"
                >
                    Manage Rooms
                </a>

            </div>

        </div>

    </div>


    <div class="col-md-6 col-lg-4">

        <div class="card shadow-sm h-100">

            <div class="card-body">

                <h5 class="fw-bold">
                    Complaints
                </h5>

                <p class="text-muted">
                    Review student maintenance complaints
                    and update their status.
                </p>

                <a
                    href="complaints.php"
                    class="btn btn-primary"
                >
                    Manage Complaints
                </a>

            </div>

        </div>

    </div>


    <div class="col-md-6 col-lg-4">

        <div class="card shadow-sm h-100">

            <div class="card-body">

                <h5 class="fw-bold">
                    Students
                </h5>

                <p class="text-muted">
                    View student information and profiles.
                </p>

                <a
                    href="students.php"
                    class="btn btn-outline-primary"
                >
                    View Students
                </a>

            </div>

        </div>

    </div>


    <div class="col-md-6 col-lg-4">

        <div class="card shadow-sm h-100">

            <div class="card-body">

                <h5 class="fw-bold">
                    Notices
                </h5>

                <p class="text-muted">
                    Create and publish hostel notices.
                </p>

                <a
                    href="notices.php"
                    class="btn btn-outline-primary"
                >
                    Manage Notices
                </a>

            </div>

        </div>

    </div>

</div>


<?php require_once '../includes/footer.php'; ?>