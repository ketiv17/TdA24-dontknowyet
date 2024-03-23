<?php

// Script for handling DELETE requests to the API (deleting an existing activity)
include '../functions.php';
include '../dbconnect.php';

// Retrieving the request body and decoding it
$request_body = file_get_contents('php://input');
$data = json_decode($request_body, true);

if ($uuid == null) {
    $error = [
        'code' => 400,
        'error' => 'UUID is required',
    ];
    echo json_encode($error, JSON_UNESCAPED_UNICODE);
    die();

}

if (!checkUuid($uuid))
{
    $error = [
        'code' => 404,
        'error' => 'Activity not found',
    ];
    echo json_encode($error, JSON_UNESCAPED_UNICODE);
    die();
}

// Preparing the SQL statement
$stmt = $conn -> prepare("DELETE FROM activities WHERE uuid = ?");
$stmt -> bind_param("s", $uuid);

// Executing the SQL statement
$stmt -> execute();

// Deleting the remaining data from the database (homePreparation, instructions, agenda, links, gallery)

// Delete the homePreparation data
$stmt = $conn->prepare("DELETE FROM homePreparation WHERE activityId = ?");
$stmt->bind_param("s", $uuid);
$stmt->execute();

// Delete the instructions data
$stmt = $conn->prepare("DELETE FROM instructions WHERE activityId = ?");
$stmt->bind_param("s", $uuid);
$stmt->execute();

// Delete the agenda data
$stmt = $conn->prepare("DELETE FROM agenda WHERE activityId = ?");
$stmt->bind_param("s", $uuid);
$stmt->execute();

// Delete the links data
$stmt = $conn->prepare("DELETE FROM links WHERE activityId = ?");
$stmt->bind_param("s", $uuid);
$stmt->execute();

// Delete the gallery data
$stmt = $conn->prepare("DELETE FROM gallery WHERE activityId = ?");
$stmt->bind_param("s", $uuid);
$stmt->execute();

// Returning a success message
$success = [
    'code' => 200,
    'message' => 'Activity deleted successfully',
];