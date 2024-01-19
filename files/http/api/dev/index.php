<!DOCTYPE html>
<html>
<body>

<h2>Manage Users and Tags</h2>

<form action="" method="post">
  <input type="submit" name="post_user" value="POST User">
  <input type="submit" name="delete_users" value="Delete All Users">
  <input type="submit" name="delete_tags" value="Delete All Tags">
</form>

</body>
</html>

<?php

// Include the functions and the database connection file
include '../functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['post_user'])) {
        // POST user code
        $url = 'http://7cad0da12da55785.app.tourdeapp.cz/api/lecturers';
        $data = '{
          "title_before": "Mgr.",
          "first_name": "Petra",
          "middle_name": "Swil",
          "last_name": "Plachá",
          "title_after": "MBA",
          "picture_url": "https://picsum.photos/200",
          "location": "Brno",
          "claim": "Bez dobré prezentace je i nejlepší myšlenka k ničemu.",
          "bio": "<b>Formátovaný text</b> s <i>bezpečnými</i> tagy.",
          "tags": [
            {
              "name": "Marketing"
            }
          ],
          "price_per_hour": 720,
          "contact": {
            "telephone_numbers": [
              "+123 777 338 111"
            ],
            "emails": [
              "user@example.com"
            ]
          }
        }';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        $response = curl_exec($curl);
        curl_close($curl);
    } elseif (isset($_POST['delete_users'])) {
        // Delete all users code
        $conn->query("DELETE FROM users");
    } elseif (isset($_POST['delete_tags'])) {
        // Delete all tags code
        $conn->query("DELETE FROM tag_list");
    }

    $conn->close();
}