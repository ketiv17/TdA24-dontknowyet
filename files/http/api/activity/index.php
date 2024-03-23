<?php

// Validating and setting a uuid from /lecturers/:uuid to a variable
$uuid = isset($_GET['uuid']) && !empty($_GET['uuid']) && preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i', $_GET['uuid'])
  ? $_GET['uuid']
  : null;

  header('Content-Type: application/json; charset=utf-8');


  // Handling request methods ----

  // Shows all activites or a specific activity
  if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    include 'get.php';

  }

// Creates a new activity
  elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {

    include 'post.php';

  }

  // Deletes an activity
  elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

    include 'delete.php';

  }