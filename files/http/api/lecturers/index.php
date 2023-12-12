<?php

//Head of the document ----------------------------------------------

$servername = "resurrectiongc.live";
$username = "api";
$password = "Ahoj-Jaksemas5";
$dbname = "api";

// Error reporting ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Validate and sanitize input
$uuid = isset($_GET['uuid']) && !empty(filter_input(INPUT_GET, 'uuid', FILTER_SANITIZE_FULL_SPECIAL_CHARS))
  ? filter_input(INPUT_GET, 'uuid', FILTER_SANITIZE_FULL_SPECIAL_CHARS)
  : null;

//Setting request method when not set
if (!isset($_SERVER['REQUEST_METHOD'])) {
    $_SERVER['REQUEST_METHOD'] = 'GET';
}

//Shows current REQUEST_METHOD at the top of the document
/*if (isset($_SERVER['REQUEST_METHOD'])) {
    echo 'Request method: ' . $_SERVER['REQUEST_METHOD'];
} else {
    echo 'No request method set';
} */

$conn = new mysqli($servername, $username, $password, $dbname);

//Check if MYSQL database is online
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if ($uuid !== null) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE uuid = ?");
        $stmt->bind_param("s", $uuid);
        $stmt->execute();
        $result = $stmt->get_result();

        if (mysqli_num_rows($result) === 0) {
            http_response_code(404);
            echo json_encode(['error' => 'No user found with this UUID']);
            exit;
        }
    } else {
        $result = mysqli_query($conn, "SELECT * FROM users");
    }

    $data = [];

    while($row = $result->fetch_assoc()) {
        $user = [
            "uuid" => $row["uuid"],
            "first_name" => $row["first_name"],
            "last_name" => $row["last_name"],
            "title_before" => $row["title_before"],
            "middle_name" => $row["middle_name"],
            "title_after" => $row["title_after"],
            "picture_url" => $row["picture_url"],
            "location" => $row["location"],
            "claim" => $row["claim"],
            "bio" => $row["bio"],
            "price_per_hour" => $row["price_per_hour"],
            "tags" => [],
            "contact" => [
                "telephone_numbers" => [],
                "emails" => [],
            ],
        ];
        $tagsSql = "SELECT * FROM tags WHERE uuid = '" . $row["uuid"] . "'";
        $tagsResult = mysqli_query($conn, $tagsSql);
        while($tagRow = $tagsResult->fetch_assoc()) {
            $user["tags"][] = [
                "uuid" => $tagRow["uuid"],
                "name" => $tagRow["name"],
            ];
        }

        $telephoneNumbersSql = "SELECT * FROM telephone_numbers WHERE uuid = '" . $row["uuid"] . "'";
        $telephoneNumbersResult = mysqli_query($conn, $telephoneNumbersSql);
        while($telephoneNumberRow = $telephoneNumbersResult->fetch_assoc()) {
            $user["contact"]["telephone_numbers"][] = $telephoneNumberRow["number"];
        }

        $emailsSql = "SELECT * FROM emails WHERE uuid = '" . $row["uuid"] . "'";
        $emailsResult = mysqli_query($conn, $emailsSql);
        while($emailRow = $emailsResult->fetch_assoc()) {
            $user["contact"]["emails"][] = $emailRow["email"];
        }

        $data[] = $user;
        http_response_code(200);
    }

    $response = [
        "data" => $data,
    ];
    convertToUtf8AndPrint($user);
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    // Prepare a SELECT statement to check if the UUID already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE uuid = ?");
    $stmt->bind_param("s", $data["uuid"]);
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    // Check if any rows were returned
    if ($result->num_rows > 0) {
        http_response_code(400);
        echo json_encode(['error' => 'User with this UUID already exists']);
        exit;
    }
    if ($data) {
        $stmt = $conn->prepare("INSERT INTO users (uuid, first_name, last_name, title_before, middle_name, title_after, picture_url, location, claim, bio, price_per_hour) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssssi", $data['uuid'], $data['first_name'], $data['last_name'], $data['title_before'], $data['middle_name'], $data['title_after'], $data['picture_url'], $data['location'], $data['claim'], $data['bio'], $data['price_per_hour']);
        $stmt->execute();

        foreach ($data['contact']['telephone_numbers'] as $telephone) {
            $stmt = $conn->prepare("INSERT INTO telephone_numbers (uuid, number) VALUES (?, ?)");
            $stmt->bind_param("ss", $data['uuid'], $telephone);
            $stmt->execute();
        }

        foreach ($data['contact']['emails'] as $email) {
            $stmt = $conn->prepare("INSERT INTO emails (uuid, email) VALUES (?, ?)");
            $stmt->bind_param("ss", $data['uuid'], $email);
            $stmt->execute();
        }

        foreach ($data['tags'] as $tag) {
            $stmt = $conn->prepare("INSERT INTO tags (uuid, name) VALUES (?, ?)");
            $stmt->bind_param("ss", $tag['uuid'], $tag['name']);
            $stmt->execute();
        }

        http_response_code(200);
        convertToUtf8AndPrint($data); // Return the newly created lecturer
    } else {
        http_response_code(400);
        $response = ['error' => 'Invalid request'];
        convertToUtf8AndPrint($response);
    }
}
elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

    if ($uuid !== null) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE uuid = ?");
        $stmt->bind_param("s", $uuid);
        $stmt->execute();
        $result = $stmt->get_result();

        if (mysqli_num_rows($result) === 0) {
            http_response_code(404);
            echo json_encode(['error' => 'No user found with this UUID']);
            exit;
        }
    }
    else {
        http_response_code(400);
        echo json_encode(["error" => "No UUID provied to delete"]);
    }

    // Prepare a DELETE statement
    $stmt = $conn->prepare("DELETE FROM users WHERE uuid = ?");

    // Bind the uuid to the statement
    $stmt->bind_param("s", $uuid);

    // Execute the statement
    $stmt->execute();

    // Check if any rows were deleted
    if ($stmt->affected_rows > 0) {
        echo "Row deleted successfully";
        http_response_code(200);
    } else {
        echo "No row with specified UUID found";
        http_response_code(404);
    }
}

$conn->close();
?>
<?php
function convertToUtf8AndPrint($data) {
    // Convert all strings in $data to UTF-8
    array_walk_recursive($data, function (&$item, $key) {
        if (is_string($item)) {
            $item = mb_convert_encoding($item, 'UTF-8', 'auto');
        }
    });

    // Set the Content-Type header to application/json
    header('Content-Type: application/json');

    // Encode $data to JSON and print it
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
}
?>