<?php
// Logout API endpoint
// Restarts the session and unsets all session variables
$authDisabled = true;
include '../../functions.php';

    session_start();
    session_unset();
    session_destroy();
    session_start();
    session_regenerate_id();
    http_response_code(200);
    echo json_encode(array("message" => "Logged out successfully"));

    logApiRequest();
