<?php
// Return appointments only for the current user
include '../../../functions.php';

// Get the current user
if (!isset($_SESSION['uuid'])) {
    http_response_code(403);
    echo 'You are not logged in';
    exit;
}
$uuid = $_SESSION['uuid'];

// Get the appointments
$stmt = $conn->prepare("SELECT * FROM calendar WHERE lecturer_uuid = ? ORDER BY `from` ASC");
        $stmt->bind_param("s", $uuid);
        $stmt->execute();
        $result = $stmt->get_result();
        $appointments = [];

        while ($row = $result->fetch_assoc()) {
            $date = date('Y-m-d', strtotime($row["from"]));

            if (!isset($appointments[$date])) {
                $appointments[$date] = [];
            }

            $appointments[$date][] = [
                "meet_id" => $row["meet_id"],
                "lecturer_uuid" => $row["lecturer_uuid"],
                "guest_firstname" => $row["guest_firstname"],
                "guest_lastname" => $row["guest_lastname"],
                "guest_email" => $row["guest_email"],
                "guest_number" => $row["guest_number"],
                "from" => $row["from"],
                "to" => $row["to"],
                "description" => $row["description"],
            ];
        }
    
        echo json_encode($appointments);