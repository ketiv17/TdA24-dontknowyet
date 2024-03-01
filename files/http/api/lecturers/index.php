<?php

// Include the functions and the database connection file
include '../functions.php';

// Validating and setting a uuid from /lecturers/:uuid to a variable
$uuid = isset($_GET['uuid']) && !empty($_GET['uuid']) && preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i', $_GET['uuid'])
  ? $_GET['uuid']
  : null;


// Handling request methods ---

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    logApiRequest();
    if ($uuid !== null) {
        // If a UUID is provided, return the lecturer as an object
        http_response_code(200);
        convertToUtf8AndPrint(returnUUIDdata($uuid));
    } else {
        // If no UUID is provided, return all lecturers as an array
        http_response_code(200);
        convertToUtf8AndPrint(returnUUIDdata(null));
    }
}   

elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    logApiRequest(json_encode($data));

    // Remove dangerous tags from bio
    if (isset($data['bio'])) {
        $data['bio'] = removeDangerousTags($data['bio']);
    }

    // Check if required fields are present (first_name and last_name)
    RequiedFieldsCheck($data);

    // Generate a new UUID if not present
    if (!isset($data['uuid']) || !UUIDCheck($data['uuid'])) {
        $data['uuid'] = generateUuidV4();
    }
    else {
        // Check if user already exists
        if (UUIDCheck($data['uuid'])) {
            http_response_code(409);
            convertToUtf8AndPrint(["code" => 409, "message" => "User with this UUID already exists"]);
            exit;
        }
    }
    // Check if the username is unique
    if (usernameCheck($data['username'])) {
        http_response_code(409);
        convertToUtf8AndPrint(["code" => 409, "message" => "Username already exists"]);
        exit;
    }
    
    // Validate phone all phone numbers
    $validatedNumbers = validatePhoneNumbers($data['contact']['telephone_numbers']);
    $data['contact']['telephone_numbers'] = $validatedNumbers !== false ? $validatedNumbers : null;

    if ($validatedNumbers !== false) {
        $data['contact']['telephone_numbers'] = $validatedNumbers;
    } else {
        $data['contact']['telephone_numbers'] = null;
    }
    // Insert the user into the database
    $stmt = $conn->prepare("INSERT INTO users (uuid, username, first_name, last_name, title_before, middle_name, title_after, picture_url, location, claim, bio, price_per_hour, emails, numbers, hash) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $uuid = $data['uuid'] ?? null;
    $username = $data['username'] ?? null;
    $first_name = $data['first_name'] ?? null;
    $last_name = $data['last_name'] ?? null;
    $title_before = $data['title_before'] ?? null;
    $middle_name = $data['middle_name'] ?? null;
    $title_after = $data['title_after'] ?? null;
    $picture_url = $data['picture_url'] ?? null;
    $location = $data['location'] ?? null;
    $claim = $data['claim'] ?? null;
    $bio = $data['bio'] ?? null;
    $price_per_hour = $data['price_per_hour'] ?? null;
    $emails = isset($data['contact']['emails']) && !is_null($data['contact']['emails']) && is_array($data['contact']['emails']) ? json_encode($data['contact']['emails']) : null;
    $numbers = isset($data['contact']['telephone_numbers']) && !is_null($data['contact']['telephone_numbers']) && is_array($data['contact']['telephone_numbers']) ? json_encode($data['contact']['telephone_numbers']) : null;

    $hash = password_hash($data['password'], PASSWORD_DEFAULT);

    $stmt->bind_param(
        "sssssssssssssss", 
        $uuid,
        $username,
        $first_name,
        $last_name,
        $title_before,
        $middle_name,
        $title_after,
        $picture_url,
        $location,
        $claim,
        $bio,
        $price_per_hour,
        $emails,
        $numbers,
        $hash
    );
    $stmt->execute();

    // Insert tags into the database and register new ones
    UpdateTags($data, $uuid, "POST");

    // Return the new user's data
    http_response_code(201);
    convertToUtf8AndPrint(returnUUIDdata($uuid));
}

elseif ($_SERVER["REQUEST_METHOD"] === "PUT") {
    // Check if the user exists
    if (!UUIDCheck($uuid)) {
        http_response_code(404);
        convertToUtf8AndPrint(["code" => 404, "message" => "User not found"]);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    logApiRequest(json_encode($data));

    // Remove dangerous tags from bio
    if (isset($data['bio'])) {
        $data['bio'] = removeDangerousTags($data['bio']);
    }

    // deny changing the username and password
    if (isset($data['username']) || isset($data['password'])) {
        http_response_code(400);
        convertToUtf8AndPrint(["code" => 400, "message" => "Username and password cannot be changed"]);
        exit;
    }

    // Validate and process telephone numbers
    if (isset($data['contact']['telephone_numbers'])) {
        $validatedNumbers = validatePhoneNumbers($data['contact']['telephone_numbers']);
        $data['contact']['telephone_numbers'] = $validatedNumbers !== false ? $validatedNumbers : null;
    }

    $updateFields = [];
    $updateValues = [];

    // Update the user in the database
    // Updating only the fields that are present in the request
    foreach ($data as $key => $value) {
        if ($key === 'contact') {
            if (isset($value['emails'])) {
                $updateFields[] = 'emails = ?';
                $updateValues[] = json_encode($value['emails']);
            }
            if (isset($value['telephone_numbers'])) {
                $updateFields[] = 'numbers = ?';
                $updateValues[] = json_encode($value['telephone_numbers']);
            }
        } else if ($key !== 'tags') {
            $updateFields[] = $key . ' = ?';
            $updateValues[] = $value;
        }
    }
    
    if (!empty($updateFields)) {
        $stmt = $conn->prepare("UPDATE users SET " . implode(', ', $updateFields) . " WHERE uuid = ?");
        $updateValues[] = $uuid;
        $stmt->bind_param(str_repeat('s', count($updateValues)), ...$updateValues);
        $stmt->execute();
    }
    
    // Insert tags into the database and register new ones (if present)
    if (isset($data['tags'])) {
        UpdateTags($data, $uuid, "PUT");
    }

    // Return the updated user's data
    http_response_code(200);
    convertToUtf8AndPrint(returnUUIDdata($uuid));
}

elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    logApiRequest();

    // Check if the user exists
    if (!UUIDCheck($uuid)) {
        http_response_code(404);
        convertToUtf8AndPrint(["code" => 404, "message" => "User not found"]);
        exit;
    }

    // Delete the user from the database
    $stmt = $conn->prepare("DELETE FROM users WHERE uuid = ?");
    $stmt->bind_param("s", $uuid);
    $stmt->execute();

    // Return a success message
    http_response_code(200);
    convertToUtf8AndPrint(["code" => 200, "message" => "User deleted"]);
}

// Return the allowed methods and other endpoints
elseif ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Max-Age: 86400");
    http_response_code(200);
    convertToUtf8AndPrint(["code" => 200, "message" => "Allowed methods: GET, POST, PUT, DELETE, OPTIONS"]);
}

// Return an error if the method is not allowed
else {
    http_response_code(405);
    convertToUtf8AndPrint(["code" => 405, "message" => "Method not allowed"]);
    exit;
}