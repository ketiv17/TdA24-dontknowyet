<?php

// Include the functions and the database connection file
include 'functions.php';

// Validating and setting a uuid from /lecturers/:uuid to a variable
$uuid = isset($_GET['uuid']) && !empty($_GET['uuid']) && preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i', $_GET['uuid'])
  ? $_GET['uuid']
  : null;


// Handling request methods ---

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    returnUUIDdata($uuid);
    }   

elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    // Check if required fields are present
    RequiedFieldsCheck($data);

    // Generate a new UUID
    if (!isset($data['uuid']) || !UUIDCheck($data['uuid'])) {
        $data['uuid'] = generateUuidV4();
    }

        // Insert the new user into the database
        $tags = implode(", ", $data['tags']); // Convert the tags array into a string
        $stmt = $conn->prepare("INSERT INTO users (uuid, first_name, last_name, title_before, middle_name, title_after, picture_url, location, claim, bio, price_per_hour, tags) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssssss", $uuid, $data['first_name'], $data['last_name'], $data['title_before'], $data['middle_name'], $data['title_after'], $data['picture_url'], $data['location'], $data['claim'], $data['bio'], $data['price_per_hour'], $tags);
        $stmt->execute();

        // Insert tags into the database
        foreach ($data['tags'] as $tag) {
            // Check if the tag already exists in the database
            $stmt = $conn->prepare("SELECT 1 FROM tags WHERE tag_uuid = ?");
            $stmt->bind_param("s", $tag['uuid']);
            $stmt->execute();
            $result = $stmt->get_result();
        // If the tag doesn't exist, insert it into the tag_list database
        if ($result->num_rows === 0) {
            $stmt = $conn->prepare("INSERT INTO tag_list (tag_uuid, tag_name, tag_color) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", generateUuidV4(), $tag['name'], generateHexColor());
            $stmt->execute();
            }
        }

    // Insert telephone numbers into the database
    foreach ($data['contact']['telephone_numbers'] as $number) {
        $stmt = $conn->prepare("INSERT INTO telephone_numbers (uuid, number) VALUES (?, ?)");
        $stmt->bind_param("ss", $uuid, $number);
        $stmt->execute();
    }

    // Insert emails into the database
    foreach ($data['contact']['emails'] as $email) {
        $stmt = $conn->prepare("INSERT INTO emails (uuid, email) VALUES (?, ?)");
        $stmt->bind_param("ss", $uuid, $email);
        $stmt->execute();
    }

    // Return the new user's data
    convertToUtf8AndPrint(returnUUIDdata($uuid));
}



























// Path: files/http/api/lecturers/functions.php


// Converts all strings in $data to UTF-8
function convertToUtf8AndPrint($data) {
    array_walk_recursive($data, function (&$item, $key) {
        if (is_string($item)) {
            $item = mb_convert_encoding($item, 'UTF-8', 'auto');
        }
    });

    // If $data is an array with a single element, convert it to an object
    if (is_array($data) && count($data) === 1) {
        $data = $data[0];
    }

    // Set the Content-Type header to application/json
    header('Content-Type: application/json');

    // Encode $data to JSON and print it
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
}

//Function that is returning the data of a given UUID, if no uuid porvided it returns all users data
function returnUUIDdata($uuid) {
    global $conn;

    if ($uuid !== null) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE uuid = ?");
        $stmt->bind_param("s", $uuid);
        $stmt->execute();
        $result = $stmt->get_result();

        if (mysqli_num_rows($result) === 0) {
            http_response_code(404);
            convertToUtf8AndPrint(["code" => 404, "message" => "User not found"]);
            exit;
        }

    } else { 
        $result = mysqli_query($conn, "SELECT * FROM users"); 
    }

$data = [];

while($row = $result->fetch_assoc()) {
    $user = [
        "uuid" => $row["uuid"],
        "first_name" => $row["first_name"],
        "last_name" => $row["last_name"],
        "title_before" => $row["title_before"],
        "middle_name" => $row["middle_name"],
        "title_after" => $row["title_after"],
        "picture_url" => $row["picture_url"],
        "location" => $row["location"],
        "claim" => $row["claim"],
        "bio" => $row["bio"],
        "price_per_hour" => intval($row["price_per_hour"]), // Converts price_per_hour to integer because database req. returns a string
        "tags" => [],
        "contact" => [
            "telephone_numbers" => [],
            "emails" => [],
        ],
    ];

    //Handling tags
    $tagsSql = "SELECT * FROM tags WHERE user_uuid = '" . $row["uuid"] . "'";
    $tagsResult = mysqli_query($conn, $tagsSql);
    while($tagRow = $tagsResult->fetch_assoc()) {
        $user["tags"][] = [
            "uuid" => $tagRow["tag_uuid"],
            "name" => $tagRow["tag_name"],
        ];
    }

    //Handling numbers
    $telephoneNumbersSql = "SELECT * FROM telephone_numbers WHERE uuid = '" . $row["uuid"] . "'";
    $telephoneNumbersResult = mysqli_query($conn, $telephoneNumbersSql);
    while($telephoneNumberRow = $telephoneNumbersResult->fetch_assoc()) {
        $user["contact"]["telephone_numbers"][] = $telephoneNumberRow["number"];
    }

    //Handling emails
    $emailsSql = "SELECT * FROM emails WHERE uuid = '" . $row["uuid"] . "'";
    $emailsResult = mysqli_query($conn, $emailsSql);
    while($emailRow = $emailsResult->fetch_assoc()) {
        $user["contact"]["emails"][] = $emailRow["email"];
    }

    $data[] = $user;
    http_response_code(200);
}
        if (count($data) === 1) {
            // If there's only one user, return it as an object, not an array
            return $data[0];
        } else {
            // If there's more than one user, return them as an array
            return $data;
        }
}

//Checks if given uuid exsits in database
function UUIDCheck($uuid = null) {
    if ($uuid === null) {
        return false;
    }
    global $conn;

    $stmt = $conn->prepare("SELECT 1 FROM users WHERE uuid = ?");
    $stmt->bind_param("s", $uuid);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->num_rows > 0;
}

//Generates new UUID with no external libraries
function generateUuidV4() {
    do {
        $uuid = sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), // 32 bits for "time_low"
            mt_rand(0, 0xffff),                     // 16 bits for "time_mid"
            mt_rand(0, 0x0fff) | 0x4000,            // 16 bits for "time_hi_and_version", four most significant bits holds version number 4
            mt_rand(0, 0x3fff) | 0x8000,            // 16 bits, 8 bits for "clk_seq_hi_res", 8 bits for "clk_seq_low", two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff) // 48 bits for "node"
        );
    } while (UUIDCheck($uuid));

    return $uuid;
}

//Checks if first and last names are present in $data body
function RequiedFieldsCheck($data) {
    $requiredFields = ['first_name', 'last_name'];

    foreach ($requiredFields as $field) {
        if (!isset($data[$field])) {
            http_response_code(400);
            convertToUtf8AndPrint(['code' => "400", 'message' => 'Required field ' . $field . ' does not exist']);
            exit;
        }
    }

    return true;
}

function generateHexColor() {
    $color = '#';
    for ($i = 0; $i < 6; $i++) {
        $color .= dechex(rand(0, 15));
    }
    return $color;
}

function ReturnUserTags($data, $connection) {
    $tags = explode(', ', $data['tags']);
    $decodedTags = [];

    foreach ($tags as $tag) {
        // Query the table with tags to get the tag name based on the UUID
        $query = "SELECT name FROM tags WHERE uuid = '$tag'";
        $result = mysqli_query($connection, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $decodedTags[] = $row['name'];
        }
    }

    return $decodedTags;
}