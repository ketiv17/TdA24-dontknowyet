<?php

// Script for handling POST requests to the API (creating a new activity)
include '../functions.php';
include '../dbconnect.php';

// Retrieving the request body and decoding it
$request_body = file_get_contents('php://input');
$data = json_decode($request_body, true);

// Validating the data -----
validateData($data);

if (checkUuid($data['uuid']))
{
    $error = [
        'code' => 400,
        'error' => 'UUID already exists',
    ];
    echo json_encode($error, JSON_UNESCAPED_UNICODE);
    die();
}

// Assigning the data to variables -----
$uuid = $data['uuid'];
$activityName = $data['activityName'];
$description = $data['description'];
$objectives = $data['objectives']; // This is an array
$classStructure = $data['classStructure'];
$lengthMin = $data['lengthMin'];
$lengthMax = $data['lengthMax'];
$edLevel = $data['edLevel']; // This is an array
$tools = $data['tools']; // This is an array
$homePreparation = $data['homePreparation']; // This is an array of associative arrays
$instructions = $data['instructions']; // This is an array of associative arrays
$agenda = $data['agenda']; // This is an array of associative arrays
$links = $data['links']; // This is an array of associative arrays
$gallery = $data['gallery']; // This is an array of associative arrays


// Encode the arrays to JSON -----
$objectives = json_encode($objectives, JSON_UNESCAPED_UNICODE);
$edLevel = json_encode($edLevel, JSON_UNESCAPED_UNICODE);
$tools = json_encode($tools, JSON_UNESCAPED_UNICODE);


// Preparing the SQL statement -----
$stmt = $conn -> prepare("INSERT INTO activities (uuid, activityName, description, objectives, classStructure, lengthMin, lengthMax, edLevel, tools) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt -> bind_param("sssssssss", $uuid, $activityName, $description, $objectives, $classStructure, $lengthMin, $lengthMax, $edLevel, $tools);

// Executing the SQL statement -----
$stmt -> execute();



// Inserting the remaining data into the database (homePreparation, instructions, agenda, links, gallery)

// Get the ID of the inserted activity
$activityId = $uuid;

// Insert the homePreparation data
foreach ($data['homePreparation'] as $preparation) {
    $stmt = $conn->prepare("INSERT INTO homePreparation (activityId, title, warn, note) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $activityId, $preparation['title'], $preparation['warn'], $preparation['note']);
    $stmt->execute();
}

// Insert the instructions data
foreach ($data['instructions'] as $instruction) {
    $stmt = $conn->prepare("INSERT INTO instructions (activityId, title, warn, note) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $activityId, $instruction['title'], $instruction['warn'], $instruction['note']);
    $stmt->execute();
}

// Insert the agenda data
foreach ($data['agenda'] as $agenda) {
    $stmt = $conn->prepare("INSERT INTO agenda (activityId, duration, title, description) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $activityId, $agenda['duration'], $agenda['title'], $agenda['description']);
    $stmt->execute();
}

// Insert the links data
foreach ($data['links'] as $link) {
    $stmt = $conn->prepare("INSERT INTO links (activityId, title, url) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $activityId, $link['title'], $link['url']);
    $stmt->execute();
}

// Insert the gallery data
foreach ($data['gallery'] as $gallery) {
    foreach ($gallery['images'] as $image) {
        $stmt = $conn->prepare("INSERT INTO gallery (activityId, title, lowRes, highRes) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $activityId, $gallery['title'], $image['lowRes'], $image['highRes']);
        $stmt->execute();
    }
}

// Returning a success message -----
echo json_encode(getActivity($uuid), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

/*
// Closing the connection -----
$conn -> close();
$stmt -> close();

// Create a short description for the activity by AI

// If you're using PHP-FPM, this function can be used to send the response to the client and continue executing the script
if (function_exists('fastcgi_finish_request')) {
    fastcgi_finish_request();
}

// Ignore user aborts and allow the script to run forever
ignore_user_abort(true);
set_time_limit(0);

// Here you can call the function that creates the short description
createAiDescription($request_body); */