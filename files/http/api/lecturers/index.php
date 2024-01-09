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
        $stmt = $conn->prepare("SELECT 1 FROM users WHERE uuid = ?");
        $stmt->bind_param("s", $data['uuid']);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            http_response_code(409);
            convertToUtf8AndPrint(["code" => 409, "message" => "User with this UUID already exists"]);
            exit;
        }
    }

    // Check if each field is set in the $data array, if not, set it to null
    $tags = isset($data['tags']) && !is_null($data['tags']) && is_array($data['tags'])  ? implode(", ", array_column($data['tags'], 'name')) : null;
    $emails = isset($data['contact']['emails']) && !is_null($data['contact']['emails']) && is_array($data['contact']['emails']) ? implode(", ", $data['contact']['emails']) : null;
    $numbers = isset($data['contact']['telephone_numbers']) && !is_null($data['contact']['telephone_numbers']) && is_array($data['contact']['telephone_numbers']) ? implode(", ", $data['contact']['telephone_numbers']) : null;

    // Assign the values to variables
    $uuid = $data['uuid'] ?? null;
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

    $stmt = $conn->prepare("INSERT INTO users (uuid, first_name, last_name, title_before, middle_name, title_after, picture_url, location, claim, bio, price_per_hour, tags, emails, numbers) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssssssss", $uuid, $first_name, $last_name, $title_before, $middle_name, $title_after, $picture_url, $location, $claim, $bio, $price_per_hour, $tags, $emails, $numbers);
    $stmt->execute();

    // Insert tags into the database
    if (is_array($data['tags'])) {
        foreach ($data['tags'] as $tag) {
            // Check if the tag already exists in the database
            $stmt = $conn->prepare("SELECT 1 FROM tag_list WHERE uuid = ? OR name = ?");
            $stmt->bind_param("ss", $tag['uuid'], $tag['name']);
            $stmt->execute();
            $result = $stmt->get_result();
            // If the tag doesn't exist, insert it into the tag_list database
            if ($result->num_rows === 0) {
                $stmt = $conn->prepare("INSERT INTO tag_list (uuid, name, color) VALUES (?, ?, ?)");
                $uuidV4 = generateUuidV4();
                $hexColor = generateHexColor();
                $stmt->bind_param("sss", $uuidV4, $tag['name'], $hexColor);
                $stmt->execute();
            }
        }
    }

    // Return the new user's data
    convertToUtf8AndPrint(returnUUIDdata($uuid));
}

elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);

    // Check if the user exists
    if (!UUIDCheck($uuid)) {
        http_response_code(404);
        convertToUtf8AndPrint(["code" => 404, "message" => "User not found"]);
        exit;
    }

    // Check if each field is set in the $data array, if not, set it to null
    $tags = isset($data['tags']) && !is_null($data['tags']) && is_array($data['tags'])  ? implode(", ", array_column($data['tags'], 'name')) : null;
    $emails = isset($data['contact']['emails']) && !is_null($data['contact']['emails']) && is_array($data['contact']['emails']) ? implode(", ", $data['contact']['emails']) : null;
    $numbers = isset($data['contact']['telephone_numbers']) && !is_null($data['contact']['telephone_numbers']) && is_array($data['contact']['telephone_numbers']) ? implode(", ", $data['contact']['telephone_numbers']) : null;

    // Assign the values to variables
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

    $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, title_before = ?, middle_name = ?, title_after = ?, picture_url = ?, location = ?, claim = ?, bio = ?, price_per_hour = ?, tags = ?, emails = ?, numbers = ? WHERE uuid = ?");
    $stmt->bind_param("ssssssssssssss", $first_name, $last_name, $title_before, $middle_name, $title_after, $picture_url, $location, $claim, $bio, $price_per_hour, $tags, $emails, $numbers, $uuid);
    $stmt->execute();

    // Insert tags into the database
    if (is_array($data['tags'])) {
        foreach ($data['tags'] as $tag) {
            // Check if the tag already exists in the database
            $stmt = $conn->prepare("SELECT 1 FROM tag_list WHERE uuid = ?");
            $stmt->bind_param("s", $tag['uuid']);
            $stmt->execute();
            $result = $stmt->get_result();
            // If the tag doesn't exist, insert it into the tag_list database
            if ($result->num_rows === 0) {
                $stmt = $conn->prepare("INSERT INTO tag_list (uuid, name, color) VALUES (?, ?, ?)");
                $uuidV4 = generateUuidV4();
                $hexColor = generateHexColor();
                $stmt->bind_param("sss", $uuidV4, $tag['name'], $hexColor);
                $stmt->execute();
            }
        }
    }

    // Return the new user's data
    convertToUtf8AndPrint(returnUUIDdata($uuid));
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


else {
    http_response_code(405);
    convertToUtf8AndPrint(["code" => 405, "message" => "Method not allowed"]);
    exit;
}