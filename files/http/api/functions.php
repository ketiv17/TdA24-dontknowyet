<?php



// This functions validates the data from the request body
function validateData($data) {
    // Check for valid uuid
    if (!preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i', $data['uuid'])) {
        $error = [
            'code' => 400,
            'error' => 'Invalid data',
            'field' => 'uuid'
        ];
        return $error;
    }

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
                'error' => 'Invalid data',
                'field' => $field
            ];
            return $error;
        }
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
        return $error;
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
            return $error;
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
                return $error;
            }
        }
    }

    // If we made it this far, the data is valid
    return true;
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