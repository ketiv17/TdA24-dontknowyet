<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once "ChatGPT.php";
header('Content-Type: application/json');

// Decode POST json
$request_body = file_get_contents('php://input');
$ai = new ChatGPT();
echo $ai->generateActivityDescription($request_body)['data'] ?? 'ERROR!';