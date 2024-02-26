<?php
// index.php

// Database connection
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