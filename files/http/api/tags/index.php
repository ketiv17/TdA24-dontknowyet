<?php

include '../functions.php';

//returns all tags from database
global $conn;

$result = mysqli_query($conn, "SELECT * FROM tag_list"); 

$data = [];

while($row = $result->fetch_assoc()) {
    $tag = [
        "uuid" => $row["uuid"],
        "name" => $row["name"],
        "color" => $row["color"],
    ];

    $data[] = $tag;
    http_response_code(200);
    } 

    // If $data is an array with a single element, convert it to an object
    if (is_array($data) && count($data) === 1) {
        $data = $data[0];
}

?>