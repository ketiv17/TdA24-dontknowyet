<?php


//@fretka I MODIFIED LINE 247, check afterwards and revert it - ketiv17

//Head of the document ----------------------------------------------

$servername = "resurrectiongc.live";
$username = "api";
$password = getenv('DB_PASSWORD');
$dbname = "api";

// Error reporting ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//logs the time source ip and request method to requests.log
file_put_contents('./requests.log', date('Y-m-d H:i:s') . ' ' . $_SERVER['REMOTE_ADDR'] . ' ' . $_SERVER['REQUEST_METHOD'] . "\n", FILE_APPEND);


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


// ---------------- Handling HTTP API requests ------------------

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    convertToUtf8AndPrint(returnUUIDdata($uuid));
}

elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    // Check for required fields (first_name, last_name)
    // Returns 400 if missing
    RequiedFieldsCheck($data);

    // Generate UUID if not provided
    if (!isset($data["uuid"])) {
        $data["uuid"] = generateUuidV4();
    }

    // Check for already existing UUID
    if (UUIDCheck($data["uuid"])) {
        http_response_code(400);
        convertToUtf8AndPrint(['error' => 'User with this UUID already exists']);
        exit;
    }

    // Check if fields are missing and if so, set them to null
    $fields = ['first_name', 'last_name', 'title_before', 'middle_name', 'title_after', 'picture_url', 'location', 'claim', 'bio', 'price_per_hour'];
    foreach ($fields as $field) {
        if (!isset($data[$field])) {
            $data[$field] = null;
        }
    }
    
    //Inserting all the data to database
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

        //Handling tags
        foreach ($data['tags'] as $tag) {
            //Checks if given tag already exists
            $stmt = $conn->prepare("SELECT * FROM tag_list WHERE tag_name = ?");
            $stmt->bind_param("s", $tag["name"]);
            $stmt->execute();
            $taguuid = $stmt->get_result();

            // If tag doesn't exist, create it and generate UUID
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
        convertToUtf8AndPrint(['code' => "400", 'message' => 'No data provided']);
    }
}
elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

    if (!UUIDCheck($uuid)) {
        http_response_code(404);
        convertToUtf8AndPrint(["code" => 404, "message" => "No UUID found"]);
        exit;
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
        convertToUtf8AndPrint(["code" => 200, "message" => "User deleted"]);
    } else {
        http_response_code(404);
        convertToUtf8AndPrint(["code" => 404, "message" => "User not found"]);
    }
}

elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);

    // Check for required fields (first_name, last_name)
    RequiedFieldsCheck($data);

    if (!UUIDCheck($uuid)) {
        http_response_code(404);
        convertToUtf8AndPrint(["code" => 404, "message" => "No UUID found"]);
        exit;
    }

    if ($data) {
        $query = "UPDATE users SET ";
        $params = [];

        foreach ($data as $key => $value) {
            if ($value !== null && $key != 'contact' && $key != 'tags') {
                $query .= "$key = ?, ";
                $params[] = $value;
            }
        }

        $query = rtrim($query, ", ");
        $query .= " WHERE uuid = ?";
        $params[] = $uuid;

        $stmt = $conn->prepare($query);
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);
        $stmt->execute();


        //Only deleting only the fields that are present in $data body
        if (isset($data['contact']['telephone_numbers'])) {
            $stmt = $conn->prepare("DELETE FROM telephone_numbers WHERE uuid = ?");
            $stmt->bind_param("s", $uuid);
            $stmt->execute();

            foreach ($data['contact']['telephone_numbers'] as $telephone) {
                $stmt = $conn->prepare("INSERT INTO telephone_numbers (uuid, number) VALUES (?, ?)");
                $stmt->bind_param("ss", $uuid, $telephone);
                $stmt->execute();
            }
        }

        if (isset($data['contact']['emails'])) {
            $stmt = $conn->prepare("DELETE FROM emails WHERE uuid = ?");
            $stmt->bind_param("s", $uuid);
            $stmt->execute();

            foreach ($data['contact']['emails'] as $email) {
                $stmt = $conn->prepare("INSERT INTO emails (uuid, email) VALUES (?, ?)");
                $stmt->bind_param("ss", $uuid, $email);
                $stmt->execute();
            }
        }

        if (isset($data['tags'])) {
            $stmt = $conn->prepare("DELETE FROM tags WHERE user_uuid = ?");
            $stmt->bind_param("s", $uuid);
            $stmt->execute();

            foreach ($data['tags'] as $tag) {
                $stmt = $conn->prepare("SELECT * FROM tag_list WHERE tag_name = ?");
                $stmt->bind_param("s", $tag["name"]);
                $stmt->execute();
                $taguuid = $stmt->get_result();

                if (mysqli_num_rows($taguuid) === 0) {
                    $taguuid = generateUuidV4();
                    $stmt = $conn->prepare("INSERT INTO tag_list (tag_name, tag_uuid) VALUES (?, ?)");
                    $stmt->bind_param("ss", $tag["name"], $taguuid);
                    $stmt->execute();
                } else {
                    $taguuid = $taguuid->fetch_assoc()["tag_uuid"];
                }

                $stmt = $conn->prepare("INSERT INTO tags (user_uuid, tag_name, tag_uuid) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $uuid, $tag["name"], $taguuid);
                $stmt->execute();
            }
        }
    } else {
        http_response_code(400);
        convertToUtf8AndPrint(['code' => "400", 'message' => 'No data provided']);
    }
    convertToUtf8AndPrint(returnUUIDdata($uuid)); // Return edited lecturer
}

$conn->close();





// -------------- FUNCTIONS --------------

// Converts all strings in $data to UTF-8
function convertToUtf8AndPrint($data) {
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

//Function that is returning the data of a given UUID, if no uuid porvided it returns all users data
function returnUUIDdata($uuid) {
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

    //Handling tags
    $tagsSql = "SELECT * FROM tags WHERE user_uuid = '" . $row["uuid"] . "'";
    $tagsResult = mysqli_query($conn, $tagsSql);
    while($tagRow = $tagsResult->fetch_assoc()) {
        $user["tags"][] = [
            "uuid" => $tagRow["tag_uuid"],
            "name" => $tagRow["tag_name"],
        ];
    }

    //Handling numbers
    $telephoneNumbersSql = "SELECT * FROM telephone_numbers WHERE uuid = '" . $row["uuid"] . "'";
    $telephoneNumbersResult = mysqli_query($conn, $telephoneNumbersSql);
    while($telephoneNumberRow = $telephoneNumbersResult->fetch_assoc()) {
        $user["contact"]["telephone_numbers"][] = $telephoneNumberRow["number"];
    }

    //Handling emails
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
?>