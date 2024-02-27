<?php
// FILEPATH: /ketiv17/TdA24-dontknowyet/files/http/api/calendar/free/index.php

// Assuming you have a database connection established
// Replace the placeholders with your actual database credentials
include '../../functions.php';

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get the UUID and date from the request
    $uuid = $_POST["uuid"];
    $date = $_POST["date"];

    // Prepare the SQL query to retrieve the lecturer's appointments for the given date
    $sql = "SELECT * FROM calendar WHERE lecturer_uuid = '$uuid' AND DATE(from) = '$date'";

    // Execute the query
    $result = $conn->query($sql);

    // Check if any appointments exist for the given date
    if ($result->num_rows > 0) {
        // Fetch the appointments
        $appointments = $result->fetch_all(MYSQLI_ASSOC);

        // Calculate the available time slots
        $availableSlots = [];
        $startHour = 8;
        $endHour = 16;

        // Iterate over each hour from the start to end hour
        for ($hour = $startHour; $hour <= $endHour; $hour++) {
            // Check if the hour is available
            $isAvailable = true;

            // Iterate over each appointment
            foreach ($appointments as $appointment) {
                $appointmentHour = (int) date("H", strtotime($appointment["from"]));

                // Check if the appointment overlaps with the current hour
                if ($appointmentHour === $hour) {
                    $isAvailable = false;
                    break;
                }
            }

            // If the hour is available, add it to the available slots
            if ($isAvailable) {
                $availableSlots[] = $hour;
            }
        }

        // Return the available time slots as JSON
        echo json_encode($availableSlots);
    } else {
        // No appointments found for the given date
        echo "No appointments found for the given date.";
    }
} else {
    // Invalid request method
    echo "Invalid request method. Only POST requests are allowed.";
}

// Close the database connection
$conn->close();
?>
