<?php

// Create a JSON object
$data = array(
  "greeting" => "Welcome to the API! :)",
);

// Encode the JSON object
$json = json_encode($data);

// Set the HTTP headers
header('Content-Type: application/json');

// Send the JSON data
echo $json;
