<?php

//Head of the document ----------------------------------------------

$servername = "resurrectiongc.live";
$username = "api";
$password = getenv('DB_PASSWORD');
$dbname = "api";

// Error reporting ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Validate and sanitize input
$uuid = isset($_GET['uuid']) && !empty($_GET['uuid']) && preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i', $_GET['uuid'])
  ? $_GET['uuid']
  : null;

//Setting request method when not set
/*
if (!isset($_SERVER['REQUEST_METHOD'])) {
    $_SERVER['REQUEST_METHOD'] = 'GET';
} */

header('Content-Type: application/json; charset=utf-8');
//Shows current REQUEST_METHOD at the top of the document
if (isset($_SERVER['REQUEST_METHOD'])) {
    echo 'Request method: ' . $_SERVER['REQUEST_METHOD'];
} else {
    echo 'No request method set';
}

$conn = new mysqli($servername, $username, $password, $dbname);

//Check if MYSQL database is online
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    //echo "Právě jsi použil metodu GET";
    convertToUtf8AndPrint(returnUUIDdata($uuid));
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    // Generate UUID if not provided
    if (!isset($data["uuid"])) {
        $data["uuid"] = generateUuidV4();
    }

    // Check for already existing UUID
    if (UUIDCheck($data["uuid"])) {
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
            // Check for tag UUID
            $stmt = $conn->prepare("SELECT * FROM tag_list WHERE tag_name = ?");
            $stmt->bind_param("s", $tag["name"]);
            $stmt->execute();
            $taguuid = $stmt->get_result();

            // If tag doesn't exist, create it
            if (mysqli_num_rows($taguuid) === 0) {
                $taguuid = generateUuidV4();
                $stmt = $conn->prepare("INSERT INTO tag_list (tag_name, tag_uuid) VALUES (?, ?)");
                $stmt->bind_param("ss", $tag["name"], $taguuid);
                $stmt->execute();
            } else {
                $taguuid = $taguuid->fetch_assoc()["tag_uuid"];
            }
            
            // Inserting into tags table
            $stmt = $conn->prepare("INSERT INTO tags (user_uuid, tag_name, tag_uuid) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $data["uuid"], $tag["name"], $taguuid);
            $stmt->execute();
        }
        
        convertToUtf8AndPrint(returnUUIDdata($data['uuid'])); // Return the newly created lecturer
        http_response_code(200);

    } else {
        http_response_code(400);
        echo json_encode(['code' => "400", 'message' => 'No data provided']);
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
            echo json_encode(["code" => 404, "message" => "User not found"]);
            exit;
        }
    }
    else {
        http_response_code(400);
        echo json_encode(["code" => 404, "message" => "No UUID provided"]);
    }

    // Prepare a DELETE statement
    $stmt = $conn->prepare("DELETE FROM users WHERE uuid = ?");
    $stmt->bind_param("s", $uuid);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM tags WHERE uuid = ?");
    $stmt->bind_param("s", $uuid);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM telephone_numbers WHERE uuid = ?");
    $stmt->bind_param("s", $uuid);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM emails WHERE uuid = ?");
    $stmt->bind_param("s", $uuid);
    $stmt->execute();

    // Check if any rows were deleted
    if ($stmt->affected_rows > 0) {
        http_response_code(200);
        echo json_encode(["code" => 200, "message" => "User deleted"]);
    } else {
        http_response_code(404);
        echo json_encode(["code" => 404, "message" => "User not found"]);
    }
}

$conn->close();

function convertToUtf8AndPrint($data) {
    // Convert all strings in $data to UTF-8
    array_walk_recursive($data, function (&$item, $key) {
        if (is_string($item)) {
            $item = mb_convert_encoding($item, 'UTF-8', 'auto');
        }
    });

    // If $data is an array with a single element, convert it to an object
    if (is_array($data) && count($data) === 1) {
        $data = $data[0];
    }

    // Set the Content-Type header to application/json
    header('Content-Type: application/json');

    // Encode $data to JSON and print it
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
}

function returnUUIDdata($uuid) {
    global $conn;

    if ($uuid !== null) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE uuid = ?");
        $stmt->bind_param("s", $uuid);
        $stmt->execute();
        $result = $stmt->get_result();

        if (mysqli_num_rows($result) === 0) {
            http_response_code(404);
            echo json_encode(["code" => 404, "message" => "User not found"]);
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
    $tagsSql = "SELECT * FROM tags WHERE user_uuid = '" . $row["uuid"] . "'";
    $tagsResult = mysqli_query($conn, $tagsSql);
    while($tagRow = $tagsResult->fetch_assoc()) {
        $user["tags"][] = [
            "uuid" => $tagRow["tag_uuid"],
            "name" => $tagRow["tag_name"],
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
        if (count($data) === 1) {
            // If there's only one user, return it as an object, not an array
            return $data[0];
        } else {
            // If there's more than one user, return them as an array
            return $data;
        }
}

function UUIDCheck($uuid) {
    global $conn;

    $stmt = $conn->prepare("SELECT 1 FROM users WHERE uuid = ?");
    $stmt->bind_param("s", $uuid);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->num_rows > 0;
}

function generateUuidV4() {
    do {
        $uuid = sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), // 32 bits for "time_low"
            mt_rand(0, 0xffff), // 16 bits for "time_mid"
            mt_rand(0, 0x0fff) | 0x4000, // 16 bits for "time_hi_and_version", four most significant bits holds version number 4
            mt_rand(0, 0x3fff) | 0x8000, // 16 bits, 8 bits for "clk_seq_hi_res", 8 bits for "clk_seq_low", two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff) // 48 bits for "node"
        );
    } while (UUIDCheck($uuid));

    return $uuid;
}
?>