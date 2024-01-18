<?php

// Set the URL for the POST request
$url = 'http://example.com/api/lecturers';

// Set the POST data
$data = '{
  "title_before": "Mgr.",
  "first_name": "Petra",
  "middle_name": "Swil",
  "last_name": "Plachá",
  "title_after": "MBA",
  "picture_url": "https://picsum.photos/200",
  "location": "Brno",
  "claim": "Bez dobré prezentace je i nejlepší myšlenka k ničemu.",
  "bio": "<b>Formátovaný text</b> s <i>bezpečnými</i> tagy.",
  "tags": [
    {
      "name": "Marketing"
    }
  ],
  "price_per_hour": 720,
  "contact": {
    "telephone_numbers": [
      "+123 777 338 111"
    ],
    "emails": [
      "user@example.com"
    ]
  }
}';

// Create a new cURL resource
$curl = curl_init();

// Set the cURL options
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

// Execute the POST request
$response = curl_exec($curl);

// Close the cURL resource
curl_close($curl);

// Handle the response
if ($response === false) {
    echo 'Error: ' . curl_error($curl);
    http_response_code(500);
} else {
    echo 'Response: ' . $response;
    http_response_code(201);
}
