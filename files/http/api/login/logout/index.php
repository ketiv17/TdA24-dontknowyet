<?php
// Logout API endpoint
// Restarts the session and unsets all session variables

if (session_status() != PHP_SESSION_NONE) {
    session_unset();
    session_destroy();
    session_start();
    session_regenerate_id();
    http_response_code(200);
    echo json_encode(array("message" => "Logged out successfully"));
} else {
    http_response_code(400);
    echo json_encode(array("message" => "No session to log out of"));
}
