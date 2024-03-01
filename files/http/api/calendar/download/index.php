<?php

include '../../functions.php';

// Start session
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(array("message" => "Method not allowed"));
    exit;
}
// Check if user is logged in
if (!isset($_SESSION['uuid'])) {
    http_response_code(401);
    echo json_encode(array("message" => "Unauthorized"));
    exit;
}

logApiRequest($_SESSION['uuid']);

// Get all appointments
$appointments = returnCalendar(null, $_SESSION['uuid']);

// Generate iCalendar file
$ics = generateICalendar($appointments);

// Set headers
header('Content-Type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename="calendar.ics"');

// Output the iCalendar file
echo $ics;




// Generate iCalendar file
function generateICalendar($appointments) {
    $ics = "BEGIN:VCALENDAR\r\n";
    $ics .= "VERSION:2.0\r\n";
    $ics .= "PRODID:-//Teacher Digital Agency//NONSGML Calendar//EN\r\n";

    foreach ($appointments as $date => $appointmentsByDate) {
        foreach ($appointmentsByDate as $appointment) {
            $ics .= "BEGIN:VEVENT\r\n";
            $ics .= "UID:" . $appointment["meet_id"] . "\r\n";
            $ics .= "DTSTART:" . date('Ymd\THis', strtotime($appointment['from'])) . "\r\n";
            $ics .= "DTEND:" . date('Ymd\THis', strtotime($appointment['to'])) . "\r\n";
            $ics .= "SUMMARY:TdA metting with " . $appointment['guest_firstname'] . "\r\n";
            $ics .= "DESCRIPTION:" . $appointment['description'] . "\r\n";
            $ics .= "END:VEVENT\r\n";
        }
    }

    $ics .= "END:VCALENDAR\r\n";

    return $ics;
}