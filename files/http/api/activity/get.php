<?php

include '../functions.php';
include '../dbconnect.php';

// Usage
$activity = getActivity($uuid); // Get all activities
echo json_encode($activity, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);