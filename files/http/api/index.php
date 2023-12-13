<?php

// Create a JSON object
$data = array(
  "secret" => "The cake is a lie"
);

// Encode the JSON object
$json = json_encode($data);

// Set the HTTP headers
header('Content-Type: application/json');

// Send the JSON data
echo $json;
