<?php

/*
|--------------------------------------------------------------------------
| Temporary Session-Based Data
|--------------------------------------------------------------------------
| This prototype does not use a database.
| All data is stored temporarily in PHP sessions.
|
| A future MySQL implementation can replace these arrays with
| database queries without changing the overall module structure.
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| Users
|--------------------------------------------------------------------------
|
| role:
|   - student
|   - admin
|
| Passwords are stored using password_hash().
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['users'])) {

    $_SESSION['users'] = [

        [
            'id' => 1,
            'name' => 'Hostel Administrator',
            'email' => 'admin@hostel.com',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'role' => 'admin'
        ],

        [
            'id' => 2,
            'name' => 'John Doe',
            'email' => 'student@hostel.com',
            'password' => password_hash('student123', PASSWORD_DEFAULT),
            'role' => 'student'
        ]

    ];
}


/*
|--------------------------------------------------------------------------
| Students
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['students'])) {

    $_SESSION['students'] = [

        [
            'id' => 2,
            'user_id' => 2,
            'student_id' => 'STU-001',
            'name' => 'John Doe',
            'email' => 'student@hostel.com',
            'phone' => '01700000000',
            'department' => 'Computer Science',
            'semester' => '8th',
            'address' => 'Dhaka, Bangladesh'
        ]

    ];
}


/*
|--------------------------------------------------------------------------
| Hostel Applications
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['applications'])) {

    $_SESSION['applications'] = [];
}


/*
|--------------------------------------------------------------------------
| Rooms
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['rooms'])) {

    $_SESSION['rooms'] = [

        [
            'id' => 1,
            'room_number' => 'A-101',
            'block' => 'A',
            'capacity' => 2,
            'occupancy' => 0
        ],

        [
            'id' => 2,
            'room_number' => 'A-102',
            'block' => 'A',
            'capacity' => 2,
            'occupancy' => 1
        ],

        [
            'id' => 3,
            'room_number' => 'B-201',
            'block' => 'B',
            'capacity' => 3,
            'occupancy' => 0
        ],

        [
            'id' => 4,
            'room_number' => 'B-202',
            'block' => 'B',
            'capacity' => 2,
            'occupancy' => 2
        ]

    ];
}


/*
|--------------------------------------------------------------------------
| Room Allocations
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['allocations'])) {

    $_SESSION['allocations'] = [];
}


/*
|--------------------------------------------------------------------------
| Complaints
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['complaints'])) {

    $_SESSION['complaints'] = [];
}


/*
|--------------------------------------------------------------------------
| Notices
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['notices'])) {

    $_SESSION['notices'] = [

        [
            'id' => 1,
            'title' => 'Welcome to the Hostel',
            'content' => 'Welcome to the Smart Hostel Management System.',
            'status' => 'Published',
            'created_at' => date('Y-m-d H:i:s')
        ]

    ];
}