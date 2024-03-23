<?php
require '../ChatGPT.php';
require '../../functions.php';
require '../../dbconnect.php';

header('Content-Type: application/json');

// Retrieving the request body and decoding it
$request_body = file_get_contents('php://input');
$data = json_decode($request_body, true);

// Validating request
if (!isset($data['prompt'])) {
    $error = [
        'code' => 400,
        'error' => 'Prompt is required',
    ];
    http_response_code(400);
    echo json_encode($error, JSON_UNESCAPED_UNICODE);
    die();
}

// Initialize ChatGPT
$chatGPT = new ChatGPT();

// Create a text request
$relevantData = $chatGPT->retrieveMostRelevant($data['prompt']);

// Example data: [{"uuid":"e3b0c442-5b8b-47ba-9d8a-e4d3d2f3f965","relevant":"70%","reason":"Fotbal je vhodný pro větší skupiny a vyžaduje minimální vybavení"},{"uuid":"e3b0c442-5b8b-47ba-9d8a-e4d3d2f3f967","relevant":"20%","reason":"Tato hra je vhodná pro menší skupiny a může být zábavným způsobem, jak zapojit žáky do aktivního učení."},{"uuid":"e3b0c442-5b8b-47ba-9d8a-e4d3d2f3f978","relevant":"10%","reason":"Tato hra může být vhodná pro menší skupiny, ale vyžaduje specifické znalosti v oblasti chemie, které by mohly být omezující pro všechny žáky ve třídě."}]
// The data are already decoded and sorted by relevance

// return 404 if % relevancy is too low
if ($relevantData[0]['relevant'] < 10) {
    $error = [
        'code' => 404,
        'error' => 'No relevant activities found',
    ];
    http_response_code(404);
    echo json_encode($error, JSON_UNESCAPED_UNICODE);
    die();
}


$activities = array_map(function($item) {
    $activity = json_decode(getActivity($item['uuid']), true); // Decode the JSON string into an associative array
    $activity['reason'] = $item['reason'];
    return $activity;
}, $relevantData);

echo json_encode($activities, JSON_UNESCAPED_UNICODE);