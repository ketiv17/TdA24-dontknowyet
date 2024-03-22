<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once "ChatGPT.php";

// Decode POST json
$promt = json_decode(file_get_contents('php://input'), true)['message'] ?? 'tell';

if (empty($promt)) {
    echo json_encode(['error' => 'message is required']);
    exit();
}

$ai = new ChatGPT();
echo $ai->createTextRequest($promt)['data'] ?? 'ERROR!';