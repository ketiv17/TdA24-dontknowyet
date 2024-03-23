<?php



// This functions validates the data from the request body
function validateData($data) {

    // List of required fields and their expected types
    $requiredFields = [
        'uuid' => 'string',
        'activityName' => 'string',
        'objectives' => 'array',
        'classStructure' => 'string',
        'lengthMin' => 'integer',
        'lengthMax' => 'integer',
    ];

    // Check if all required fields are present and have the correct type
    foreach ($requiredFields as $field => $type) {
        if (!isset($data[$field]) || gettype($data[$field]) !== $type) {

            $error = [
                'code' => 400,
                'error' => 'Missing data',
                'field' => $field
            ];
            http_response_code(400);
            echo json_encode($error, JSON_UNESCAPED_UNICODE);
            die();
        }
    }

    // Check for valid uuid
    if (!preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i', $data['uuid'])) {
        $error = [
            'code' => 400,
            'error' => 'Invalid data',
            'field' => 'uuid'
        ];
        http_response_code(400);
        echo json_encode($error, JSON_UNESCAPED_UNICODE);
        die();
    }

    // Validate if minutes are within the correct range
    if ($data['lengthMin'] < 0 ||
        $data['lengthMax'] < 0 ||
        $data['lengthMin'] > $data['lengthMax']) {
        $error = [
            'code' => 400,
            'error' => 'Invalid data',
            'field' => 'lengthMin/lengthMax'
        ];
        http_response_code(400);
        echo json_encode($error, JSON_UNESCAPED_UNICODE);
        die();
    }

    // Check if all array fields are arrays
    $arrayFields = ['objectives', 'classStructure', 'edLevel', 'tools', 'homePreparation', 'instructions', 'agenda', 'links', 'gallery'];
    foreach ($arrayFields as $field) {
        if (isset($data[$field]) && !is_array($data[$field])) {
            $error = [
                'code' => 400,
                'error' => 'Invalid data',
                'field' => $field
            ];
            http_response_code(400);
            echo json_encode($error, JSON_UNESCAPED_UNICODE);
            die();
        }
    }

    // if agenda is defined, check if the duration is a positive integer
    if (isset($data['agenda'])) {
        foreach ($data['agenda'] as $agenda) {
            if (!isset($agenda['duration']) || !is_int($agenda['duration']) || $agenda['duration'] < 0) {
                $error = [
                    'code' => 400,
                    'error' => 'Invalid data',
                    'field' => 'agenda duration'
                ];
                http_response_code(400);
                echo json_encode($error, JSON_UNESCAPED_UNICODE);
                die();
            }
        }
    }

    // If we made it this far, the data is valid
    return true;
}

function getActivity($uuid = null) {
    global $conn;

    // Prepare the SQL statement
    if ($uuid == null) {
        $stmt = $conn->prepare("SELECT * FROM activities");
    } else {
        $stmt = $conn->prepare("SELECT * FROM activities WHERE uuid = ?");
        $stmt->bind_param("s", $uuid);
    }

    // Execute the SQL statement
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    // Fetch the data
    $activities = [];
    while ($activity = $result->fetch_assoc()) {
        // Convert the arrays back to arrays from JSON
        $activity['objectives'] = json_decode($activity['objectives'], true);
        $activity['edLevel'] = json_decode($activity['edLevel'], true);
        $activity['tools'] = json_decode($activity['tools'], true);

        // Get the additional data from the other tables
        $tables = ['homePreparation', 'instructions', 'agenda', 'links', 'gallery'];
        foreach ($tables as $table) {
            $stmt = $conn->prepare("SELECT * FROM $table WHERE activityId = ?");
            $stmt->bind_param("s", $activity['uuid']);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                unset($row['id']);
                unset($row['activityId']);
                if ($table == 'gallery') {
                    $activity[$table]['images'][] = $row;
                } else {
                    $activity[$table][] = $row;
                }
            }
        }

        $activities[] = $activity;
    }

    // If a specific UUID was requested, return that activity
    if ($uuid != null) {
        return $activities[0];
    }

    // Return the data as a JSON object
    return $activities;
}

// Checks if given uuid already exists
function checkUuid($uuid) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM activities WHERE uuid = ?");
    $stmt->bind_param("s", $uuid);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}