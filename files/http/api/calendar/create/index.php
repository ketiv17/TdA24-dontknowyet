<?php
// index.php

// Database connection
$authDisabled = true;
include('../../functions.php');

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get POST data
    $meet_id = $_POST['meet_id'];
    $lecturer_uuid = $_POST['lecturer_uuid'];
    $guest_firstname = $_POST['guest_firstname'];
    $guest_lastname = $_POST['guest_lastname'];
    $guest_email = $_POST['guest_email'];
    $guest_number = $_POST['guest_number'];
    $from = $_POST['from'];
    $to = $_POST['to'];
    $description = $_POST['description'];

    // Check if lecturer_uuid is a valid UUID
    if (!preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i', $lecturer_uuid)) {
        http_response_code(400);
        die('Error: lecturer_uuid is not a valid UUID.');
    }

    // Check if guest_firstname and guest_lastname are valid names
    if (!preg_match("/^[a-zA-Z-' ]*$/", $guest_firstname)) {
        http_response_code(400);
        die('Error: guest_firstname is not a valid name.');
    }
    if (!preg_match("/^[a-zA-Z-' ]*$/", $guest_lastname)) {
        http_response_code(400);
        die('Error: guest_lastname is not a valid name.');
    }

    // Check if guest_email is a valid email
    if (!filter_var($guest_email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        die('Error: guest_email is not a valid email address.');
    }

    // Check if guest_number is a valid number
    if (!preg_match('/^[0-9]+$/', $guest_number)) {
        http_response_code(400);
        die('Error: guest_number is not a valid number.');
    }

    // Check if from and to are valid dates
    $fromDate = DateTime::createFromFormat('Y-m-d H:i:s', $from);
    $toDate = DateTime::createFromFormat('Y-m-d H:i:s', $to);
    if (!$fromDate || $fromDate->format('Y-m-d H:i:s') !== $from) {
        http_response_code(400);
        die('Error: from is not a valid datetime.');
    }
    if (!$toDate || $toDate->format('Y-m-d H:i:s') !== $to) {
        http_response_code(400);
        die('Error: to is not a valid datetime.');
    }

    // Check if email is valid
    if (!filter_var($guest_email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        die('Error: guest_email is not a valid email address.');
    }

    // If you reach here, all checks have passed


    // Get POST data
    $meet_id = $_POST['meet_id'];
    $lecturer_uuid = $_POST['lecturer_uuid'];
    $guest_firstname = $_POST['guest_firstname'];
    $guest_lastname = $_POST['guest_lastname'];
    $guest_email = $_POST['guest_email'];
    $guest_number = $_POST['guest_number'];
    $from = $_POST['from'];
    $to = $_POST['to'];
    $description = $_POST['description'];

    // Prepare SQL statement
    $stmt = $conn->prepare("INSERT INTO calendar (meet_id, lecturer_uuid, guest_firstname, guest_lastname, guest_email, guest_number, from, to, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssss", $meet_id, $lecturer_uuid, $guest_firstname, $guest_lastname, $guest_email, $guest_number, $from, $to, $description);

    // Execute SQL statement
    if ($stmt->execute()) {
        http_response_code(201);
        echo "Appointment created successfully";
    } else {
        http_response_code(500);
        echo "Error: " . $stmt->error;
    }
} else {
    http_response_code(405);
    echo "Invalid request method";
}

logApiRequest($_POST);