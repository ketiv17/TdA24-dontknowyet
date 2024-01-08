<?php
// Database connection details
$servername = "resurrectiongc.live";
$username = "api";
$password = getenv('DB_PASSWORD');
$dbname = "api";

//Check if MYSQL database is online and connects to it
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Fetch all tags from the database
$query = "SELECT * FROM tags";
$result = mysqli_query($connection, $query);

// Check if any tags were found
if (mysqli_num_rows($result) > 0) {
    // Create an array to store the tags
    $tags = array();

    // Loop through the result set and add each tag to the array
    while ($row = mysqli_fetch_assoc($result)) {
        $tags[] = $row['tag_name'];
    }

    // Return the tags as JSON
    header('Content-Type: application/json');
    echo json_encode($tags);
} else {
    // No tags found
    echo "No tags found.";
}

// Close the database connection
mysqli_close($connection);
?>