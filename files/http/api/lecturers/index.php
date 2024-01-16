<?php

// Include the functions and the database connection file
include '../functions.php';

// Validating and setting a uuid from /lecturers/:uuid to a variable
$uuid = isset($_GET['uuid']) && !empty($_GET['uuid']) && preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i', $_GET['uuid'])
  ? $_GET['uuid']
  : null;


// Handling request methods ---

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    convertToUtf8AndPrint(returnUUIDdata($uuid));
    }   

elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    // Check if required fields are present
    RequiedFieldsCheck($data);

    // Generate a new UUID
    if (!isset($data['uuid']) || !UUIDCheck($data['uuid'])) {
        $data['uuid'] = generateUuidV4();
    }
    else {
        //Check if user already exists
        if (UUIDCheck($data['uuid'])) {
            http_response_code(409);
            convertToUtf8AndPrint(["code" => 409, "message" => "User with this UUID already exists"]);
            exit;
        }
    }
    
    $stmt = $conn->prepare("INSERT INTO users (uuid, first_name, last_name, title_before, middle_name, title_after, picture_url, location, claim, bio, price_per_hour, emails, numbers) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "sssssssssssss", 
        $data['uuid'] ?? null,
        $data['first_name'] ?? null,
        $data['last_name'] ?? null,
        $data['title_before'] ?? null,
        $data['middle_name'] ?? null,
        $data['title_after'] ?? null,
        $data['picture_url'] ?? null,
        $data['location'] ?? null,
        $data['claim'] ?? null,
        $data['bio'] ?? null,
        $data['price_per_hour'] ?? null,
        isset($data['contact']['emails']) && !is_null($data['contact']['emails']) && is_array($data['contact']['emails']) ? json_encode($data['contact']['emails']) : null,
        isset($data['contact']['telephone_numbers']) && !is_null($data['contact']['telephone_numbers']) && is_array($data['contact']['telephone_numbers']) ? json_encode($data['contact']['telephone_numbers']) : null
    );
    $stmt->execute();

    // Insert tags into the database and register new ones
    UpdateTags($data, $uuid);

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

    $updateFields = [];
    $updateValues = [];
    
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
        $updateValues[] = $data['uuid'];
        $stmt->bind_param(str_repeat('s', count($updateValues)), ...$updateValues);
        $stmt->execute();
    }
    
    // Insert tags into the database and register new ones
    if (isset($data['tags'])) {
        UpdateTags($data, $data['uuid']);
    }

    // Return the new user's data
    convertToUtf8AndPrint(returnUUIDdata($uuid));
    http_response_code(200);
}

elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Check if the user exists
    if (!UUIDCheck($uuid)) {
        http_response_code(404);
        convertToUtf8AndPrint(["code" => 404, "message" => "User not found"]);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM users WHERE uuid = ?");
    $stmt->bind_param("s", $uuid);
    $stmt->execute();

    http_response_code(204);
    convertToUtf8AndPrint(null);
}

// Return the allowed methods and other endpoints
elseif ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Max-Age: 86400");
    convertToUtf8AndPrint(null);
    http_response_code(200);
}


else {
    http_response_code(405);
    convertToUtf8AndPrint(["code" => 405, "message" => "Method not allowed"]);
    exit;
}