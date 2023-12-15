<?php
//Print request method
echo 'Request method: ' . $_SERVER['REQUEST_METHOD'];

//appends the time source ip and request method to requests.log
file_put_contents('./requests.log', date('Y-m-d H:i:s') . ' ' . $_SERVER['REMOTE_ADDR'] . ' ' . $_SERVER['REQUEST_METHOD'] . "\n", FILE_APPEND);

echo "<br><br>earlier requests<br>";
echo nl2br(file_get_contents('./requests.log'));

?>