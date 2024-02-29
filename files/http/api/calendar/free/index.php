<?php
// Assuming you have a database connection established
$authDisabled = true;

include '../../functions.php';

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Get the raw POST data
    $rawData = file_get_contents("php://input");

    // Decode the JSON data
    $data = json_decode($rawData, true);

    $data['uuid'] = '00695f7b-bb48-400b-8074-60ed947b3299';
    $data['date'] = '2024-02-15';

    // Check if the required parameters are present in the data
    if (isset($data["uuid"]) && isset($data["date"])) {
        // Get the UUID and date from the data

        //$uuid = $data["uuid"];
        //$date = $data["date"];

        $uuid = '00695f7b-bb48-400b-8074-60ed947b3299';
        $date = '2024-02-15';


        // Prepare the SQL query to retrieve the lecturer's appointments for the given date
        $sql = "SELECT * FROM calendar WHERE lecturer_uuid = '$uuid' AND DATE(`from`) = '$date'";

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
                    $availableSlots[] = str_pad($hour, 2, "0", STR_PAD_LEFT) . ":00";
                }
            }

            // Prepare the final result
            $result = [
                'date' => $date,
                'availableSlots' => $availableSlots
            ];

            // Return the result as JSON
            echo json_encode($result);
        } else {
            // No appointments found for the given date
            echo json_encode(['date' => $date, 'availableSlots' => ["08:00","09:00","10:00","11:00","12:00","13:00","14:00","15:00","16:00"]]);
        }
    } else {
        // Required parameters are missing
        echo "Required parameters are missing.";
    }

    // Close the database connection
    $conn->close();
} else {
    // Invalid request method
    echo "Invalid request method.";
}
?>