<?php

// -----Database connection-----

$servername = "tda-mysql-do-user-15726163-0.c.db.ondigitalocean.com";
$username = "api";
$password = getenv('DB_PASSWORD');
$dbname = "api_grf";
$port = 25060;

// Error reporting --- 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


//Check if MYSQL database is online and connects to it
$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}