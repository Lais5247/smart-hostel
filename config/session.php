<?php

// Start PHP session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load sample/temporary data
require_once __DIR__ . '/../data/data.php';