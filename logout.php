<?php

require_once 'includes/auth.php';


// Remove authentication information only

logout_user();


// Redirect to login

redirect('login.php');