<?php
// Include the file containing the getCalendar function
require_once '../functions.php';

// Define the API endpoint
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Call the getCalendar function to retrieve all calendar appointments
    $appointments = returnCalendar();

    // Return the appointments as JSON response
    header('Content-Type: application/json');
    echo json_encode($appointments);
} else {
    // Handle unsupported HTTP methods
    http_response_code(405);
    echo 'Method Not Allowed';
}

logApiRequest();