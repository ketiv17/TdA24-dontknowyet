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

// Path: files/http/api/lecturers/functions.php


// Converts all strings in $data to UTF-8
function convertToUtf8AndPrint($data) {
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

//Function that is returning the data of a given UUID, if no uuid porvided it returns all users data
function returnUUIDdata($uuid = null) {
    global $conn;

    if ($uuid !== null) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE uuid = ?");
        $stmt->bind_param("s", $uuid);
        $stmt->execute();
        $result = $stmt->get_result();

        if (mysqli_num_rows($result) === 0) {
            http_response_code(404);
            convertToUtf8AndPrint(["code" => 404, "message" => "User not found"]);
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
            "price_per_hour" => intval($row["price_per_hour"]), // Converts price_per_hour to integer because database req. returns a string
            "tags" => [],
            "contact" => [
                "telephone_numbers" => [],
                "emails" => [],
            ],
        ];

    // Handling tags
    if (isset($row["tags"]) && $row["tags"] !== null) {
        $tags = json_decode($row["tags"], true);
        if ($tags !== null) {
            foreach ($tags as $tag) {
                if (is_array($tag)) {
                    $tag = implode(", ", $tag); // Convert array to string
                }
                $tagQuery = "SELECT * FROM tag_list WHERE name = '$tag'";
                $tagResult = mysqli_query($conn, $tagQuery);
                if ($tagResult !== false) {
                    $tagRow = mysqli_fetch_assoc($tagResult);
                    if ($tagRow !== null) {
                        $user["tags"][] = [
                            "uuid" => $tagRow["uuid"],
                            "name" => $tagRow["name"],
                            "color" => $tagRow["color"],
                        ];
                    }
                } else {
                    // Handle error - query failed
                    echo "Error: " . mysqli_error($conn);
                    http_response_code(500);
                    convertToUtf8AndPrint(["code" => 500, "message" => "Database server error"]);
                    exit;
                }
            }
        }
        if (empty($user["tags"])) {
            $user["tags"] = null;
        }
    } else {
        $user["tags"] = null;
    }

    // Handling emails
    if (isset($row["emails"]) && $row["emails"] !== null) {
        $user["contact"]["emails"] = json_decode($row["emails"], true);
        if (empty($user["contact"]["emails"])) {
            $user["contact"]["emails"] = null;
        }
    } else {
        $user["contact"]["emails"] = null;
    }

    // Handling numbers
    if (isset($row["numbers"]) && $row["numbers"] !== null) {
        $user["contact"]["telephone_numbers"] = json_decode($row["numbers"], true);
        if (empty($user["contact"]["telephone_numbers"])) {
            $user["contact"]["telephone_numbers"] = null;
        }
    } else {
        $user["contact"]["telephone_numbers"] = null;
    }

        $data[] = $user;
        } 

        // Converting to object if request is for one user only
        if ($uuid !== null) {
            $data = $data[0];
    }
    return $data;
}

//Checks if given uuid exsits in database
function UUIDCheck($uuid = null) {
    if ($uuid === null) {
        return false;
    }
    global $conn;

    $stmt = $conn->prepare("SELECT 1 FROM users WHERE uuid = ?");
    $stmt->bind_param("s", $uuid);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->num_rows > 0;
}

//Generates new UUID with no external libraries
function generateUuidV4() {
    do {
        $uuid = sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), // 32 bits for "time_low"
            mt_rand(0, 0xffff),                     // 16 bits for "time_mid"
            mt_rand(0, 0x0fff) | 0x4000,            // 16 bits for "time_hi_and_version", four most significant bits holds version number 4
            mt_rand(0, 0x3fff) | 0x8000,            // 16 bits, 8 bits for "clk_seq_hi_res", 8 bits for "clk_seq_low", two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff) // 48 bits for "node"
        );
    } while (UUIDCheck($uuid));

    return $uuid;
}

//Checks if first and last names are present in $data body
function RequiedFieldsCheck($data) {
    $requiredFields = ['first_name', 'last_name'];

    foreach ($requiredFields as $field) {
        if (!isset($data[$field])) {
            http_response_code(400);
            convertToUtf8AndPrint(['code' => "400", 'message' => 'Required field ' . $field . ' does not exist']);
            exit;
        }
    }

    return true;
}

function generateHexColor() {
    $color = '#';
    for ($i = 0; $i < 6; $i++) {
        $color .= dechex(rand(0, 15));
    }
    return $color;
}



// Updating user tags, inserting newly created tags into tag_list
function UpdateTags($data, $useruuid) {
    global $conn;

    // Null check
    if (!isset($data["tags"]) || empty($data["tags"]) || is_null($data["tags"])) {
        return;
    }
    // Check if the tags are in the correct format
    if (!is_array($data["tags"])) {
        http_response_code(400);
        convertToUtf8AndPrint(["code" => 400, "message" => "Invalid tag format"]);
        exit;
    }

    $tagUuids = [];

    foreach ($data["tags"] as $tag) {
        $uuid = isset($tag['uuid']) ? $tag['uuid'] : generateUuidV4();
        // Check if the tag already exists in the database
        $stmt = $conn->prepare("SELECT uuid FROM tag_list WHERE name = ? OR uuid = ?");
        $stmt->bind_param("ss", $tag['name'], $uuid);
        $stmt->execute();
        $result = $stmt->get_result();
        // If the tag doesn't exist, insert it into the tag_list database
        if ($result->num_rows === 0) {
            $stmt = $conn->prepare("INSERT INTO tag_list (uuid, name, color) VALUES (?, ?, ?)");
            $hexColor = isset($tag['color']) && !empty($tag['color']) && preg_match('/^#([a-f0-9]{6}|[a-f0-9]{3})$/i', $tag['color']) ? $tag['color'] : generateHexColor();
            $stmt->bind_param("sss", $uuid, $tag['name'], $hexColor);
            $stmt->execute();
        }
        $tagUuids[] = $uuid;
    }

    // Updating the user's tags
    // Encoding tags to json
    $jsontags = json_encode($tagUuids);

    $stmt = $conn->prepare("UPDATE users SET tags = ? WHERE uuid = ?");
    $stmt->bind_param("ss", $jsontags, $useruuid);
    $stmt->execute();
}