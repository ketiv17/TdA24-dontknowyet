<?php
// api.php

// Set the path to the file you want to serve
$file_path = 'secret.json';

// Check if the file exists
if (file_exists($file_path)) {
    // Set the appropriate headers for file download
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
    header('Content-Length: ' . filesize($file_path));

    // Read the file and output it to the browser
    readfile($file_path);
} else {
    // File not found
    http_response_code(404);
    echo 'File not found.';
}
?>
