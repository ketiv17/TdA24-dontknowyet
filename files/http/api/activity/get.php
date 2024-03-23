<?php

include '../functions.php';
include '../dbconnect.php';

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

// Usage
$activity = getActivity($uuid); // Get all activities
echo json_encode($activity, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);