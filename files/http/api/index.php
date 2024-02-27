<?php

// Create a JSON object
$data = array(
  "greeting" => "Welcome to the API",
  "authorize" => "Please authorize to use the API",
  "test" => getenv('TDA_API_PASS'),
);

// Encode the JSON object
$json = json_encode($data);

// Set the HTTP headers
header('Content-Type: application/json');

// Send the JSON data
echo $json;
