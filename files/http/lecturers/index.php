<?php
$servername = "resurrectiongc.live";
$username = "api";
$password = "Ahoj-Jaksemas5";
$dbname = "api";

//Shows current REQUEST_METHOD at the top of the document
/*if (isset($_SERVER['REQUEST_METHOD'])) {
    echo 'Request method: ' . $_SERVER['REQUEST_METHOD'];
} else {
    echo 'No request method set';
} */

$conn = new mysqli($servername, $username, $password, $dbname);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $uuid = isset($_GET['uuid']) ? $_GET['uuid'] : null;

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
            "first_name" => $row["first_name"],
            "last_name" => $row["last_name"],
            "uuid" => $row["uuid"],
            "title_before" => $row["title_before"],
            "middle_name" => $row["middle_name"],
            "title_after" => $row["title_after"],
            "picture_url" => $row["picture_url"],
            "location" => $row["location"],
            "claim" => $row["claim"],
            "bio" => $row["bio"],
            "price_per_hour" => $row["price_per_hour"],
            "contact" => [
                "telephone_numbers" => isset($row["telephone"]) ? [$row["telephone"]] : [],
                "emails" => isset($row["email"]) ? [$row["email"]] : [],
            ],
        ];
        $tagsSql = "SELECT * FROM tags WHERE uuid = " . $row["uuid"];
        $tagsResult = mysqli_query($conn, $tagsSql);
        while($tagRow = $tagsResult->fetch_assoc()) {
            $user["tags"][] = [
                "uuid" => $tagRow["uuid"],
                "name" => $tagRow["name"],
            ];
        }

        $data[] = $user;
    }

    echo json_encode($data, JSON_UNESCAPED_UNICODE);
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
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, uuid, title_before, middle_name, title_after, picture_url, location, claim, bio, price_per_hour) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssssi", $data['first_name'], $data['last_name'], $data['uuid'], $data['title_before'], $data['middle_name'], $data['title_after'], $data['picture_url'], $data['location'], $data['claim'], $data['bio'], $data['price_per_hour']);
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

        http_response_code(201);
        echo json_encode(['message' => 'User created successfully']);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request']);
    }
}
elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

    $uuid = isset($_GET['uuid']) ? $_GET['uuid'] : null;

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
        echo json_encode(["error" => "No UUID provied to delete"])
    }

    // Prepare a DELETE statement
    $stmt = $conn->prepare("DELETE FROM users WHERE uuid = ?");

    // Bind the id to the statement
    $stmt->bind_param("i", $id);

    // Execute the statement
    $stmt->execute();

    // Check if any rows were deleted
    if ($stmt->affected_rows > 0) {
        echo "Row deleted successfully";
    } else {
        echo "No row with id = 10 found";
    }
}

$conn->close();
?>