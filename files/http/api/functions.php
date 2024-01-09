<?php
// -------------- PREPARATION --------------

$servername = "resurrectiongc.live";
$username = "api";
$password = getenv('DB_PASSWORD');
$dbname = "api";

// Error reporting ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


//Check if MYSQL database is online and connects to it
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// END OF THE HEAD -------


// -------------- FUNCTIONS --------------

// Converts all strings in $data to UTF-8 