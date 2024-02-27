<?php

session_start();

include '../../functions.php';

logApiRequest();

if (isset($_SESSION['uuid'])) {
    $uuid = $_SESSION['uuid'];
    $response = array("uuid" => $uuid);
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    http_response_code(401);
    echo "Unauthorized";
}
