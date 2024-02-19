<?php
// Include the database connection file and the functions file
include '../functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the JSON data from the request body
    $requestBody = file_get_contents('php://input');
    $data = json_decode($requestBody, true);

        // Check if the name and password are not empty
        if (empty($data['email']) || empty($data['password'])) {
            // Invalid credentials, return error response
            http_response_code(400);
            $response = array("success" => false, "message" => "Password or email can't be empty");
            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
        }

    // Extract the name and password from the JSON data
    $email = $data['email'];
    $password = $data['password'];

    // Prepare the SQL statement to get the user with the given name
    $stmt = $conn->prepare("SELECT * FROM users WHERE master_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a matching user is found
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verify the password with the hashed password stored in the database
        if (password_verify($password, $user['hash'])) {
            // Valid credentials, return success response
            http_response_code(200);
            $response = array("success" => true, "message" => "Login successful");
        } else {
            // Invalid credentials, return error response
            http_response_code(401);
            $response = array("success" => false, "message" => "Invalid credentials");
        }
    } else {
        // Invalid credentials, return error response
        $response = array("success" => false, "message" => "Invalid credentials");
    }

    // Set the response headers
    header('Content-Type: application/json');

    // Send the response as JSON
    echo json_encode($response);

    // Close the database connection
    $stmt->close();
    $conn->close();
}

else {
    // Invalid request method, return error response
    $response = array("success" => false, "message" => "Invalid request method");
    header('Content-Type: application/json');
    echo json_encode($response);
}