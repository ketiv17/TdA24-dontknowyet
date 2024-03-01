<?php
// index.php

// Database connection
$authDisabled = true;
include('../../functions.php');

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    // Check if all required data are set
    $requiredFields = ['lecturer_uuid', 'guest_firstname', 'guest_lastname', 'guest_email', 'guest_number', 'time', 'agreement'];
    foreach ($requiredFields as $field) {
        if (!isset($data[$field])) {
            http_response_code(400);
            die("Error: Missing required data - {$field}.");
        }
    }

    // Get POST data
    $lecturer_uuid = $data['lecturer_uuid'];
    $guest_firstname = $data['guest_firstname'];
    $guest_lastname = $data['guest_lastname'];
    $guest_email = $data['guest_email'];
    $guest_number = $data['guest_number'];
    $agreement = $data['agreement'];
    $description = isset($data['description']) ? $data['description'] : NULL;

    logApiRequest(json_encode($data));

    // Validate if agreement is true
    if ($agreement !== true) {
        http_response_code(400);
        die('Error: You must accept the agreement (GDPR).');
    }
    // Validate if time is in the correct format
    if (!preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/', $data['time'])) {
        http_response_code(400);
        die('Chyba: Čas není nastaven správně');
    }
    // Validate if time is full hour
    if (!preg_match('/^(\d{4})-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01]) ([01][0-9]|2[0-3]):00$/', $data['time'])) {
        http_response_code(400);
        die('Error: Appointment must start at full hour (HH:00).');
    }
    // Validate if appointment is in the future and from 8:00 to 16:00
    if (strtotime($data['time']) < time() || date('H', strtotime($data['time'])) < 8 || date('H', strtotime($data['time'])) >= 17) {
        http_response_code(400);
        die('Error: Appointment must be in the future and between 8:00 and 16:00.');
    }

   // Split time into from and to
    $from = $data['time'];
    $to = date('Y-m-d H:i', strtotime($from . ' +1 hour'));   

    // Check if lecturer_uuid is a valid UUID
    if (!preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i', $lecturer_uuid)) {
        http_response_code(400);
        die('Error: lecturer_uuid is not a valid UUID.');
    }

    // Check if guest_firstname and guest_lastname are valid names
    if (!preg_match("/^[a-zA-Z-' ěščřžýáíéúůďťňĚŠČŘŽÝÁÍÉÚŮĎŤŇ]*$/u", $guest_firstname)) {
        http_response_code(400);
        die('Chyba: Jméno není platné.');
    }
    if (!preg_match("/^[a-zA-Z-' ěščřžýáíéúůďťňĚŠČŘŽÝÁÍÉÚŮĎŤŇ]*$/u", $guest_lastname)) {
        http_response_code(400);
        die('Chyb: Příjmení není platné.');
    }

    // Check if guest_email is a valid email
    if (!filter_var($guest_email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        die('Chyba: Email není platný, zkontrolujte @ a .');
    }

    // Check if guest_number is a valid number and add country code if missing

    // If the number already has the country code, add it to the processed numbers
    if (!preg_match('/^\+420[0-9]{9}$/', $guest_number)) {
        // If the number does not have country code, add +420
        if (preg_match('/^[0-9]{9}$/', $guest_number)) {
            $guest_number = '+420' . $guest_number;
        }
        // If the number doesn't match either pattern, return false
        else {
            http_response_code(400);
            die('Chyba: Telefonní číslo není platné.');
        }
    }

    // Check if from and to are valid dates
    $fromDate = DateTime::createFromFormat('Y-m-d H:i', $from);
    $toDate = DateTime::createFromFormat('Y-m-d H:i', $to);
    if (!$fromDate || $fromDate->format('Y-m-d H:i') !== $from) {
        http_response_code(400);
        die('Error: from is not a valid datetime.');
    }
    if (!$toDate || $toDate->format('Y-m-d H:i') !== $to) {
        http_response_code(400);
        die('Error: to is not a valid datetime.');
    }

    // Check if the lecturer is available at the given time
    $sql = "SELECT * FROM calendar WHERE lecturer_uuid = ? AND `from` < ? AND `to` > ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $lecturer_uuid, $to, $from);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        http_response_code(400);
        die('Error: The lecturer is not available at the given time.');
    }


    // Prepare SQL statement
    $stmt = $conn->prepare("INSERT INTO calendar (lecturer_uuid, guest_firstname, guest_lastname, guest_email, guest_number, `from`, `to`, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $lecturer_uuid, $guest_firstname, $guest_lastname, $guest_email, $guest_number, $from, $to, $description);

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