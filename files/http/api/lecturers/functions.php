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


// Validating and setting a uuid from /lecturers/:uuid to a variable
$uuid = isset($_GET['uuid']) && !empty($_GET['uuid']) && preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i', $_GET['uuid'])
  ? $_GET['uuid']
  : null;

//Check if MYSQL database is online and connects to it
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// END OF THE HEAD -------


// -------------- FUNCTIONS --------------

// Converts all strings in $data to UTF-8 