<?php

include '../functions.php';
include '../dbconnect.php';

// return error if the user dont exists
if ($uuid != null) {
    if (!checkUuid($uuid)) {
        $error = [
            'code' => 404,
            'error' => 'UUID not found',
        ];
        http_response_code(404);
        echo json_encode($error);
        die();
    }
    getActivity($uuid);
  }else{
getActivity();
  }