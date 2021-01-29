<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Count # of uploaded files in array
echo json_encode($_FILES) . ' here josh';

$total = count($_FILES);

$errors = array();

for ($i = 0; $i < $total; $i++) {
    if (0 < $_FILES['file_' . $i]['error']) {
        array_push($error, $_FILES['file_' . $i]['name']);
    } else {
        move_uploaded_file($_FILES['file_' . $i]['tmp_name'], './uploads/' . $_FILES['file_' . $i]['name']);
    }
}

$response['status'] = 'success';
$response['message'] = 'message';
$response['data'] = $errors;
exit(json_encode($response));
