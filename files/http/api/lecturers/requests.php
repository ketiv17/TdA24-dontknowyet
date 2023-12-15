<?php
//returns the contens of logs.txt with \n replaced with <br>
echo nl2br(file_get_contents('./requests.log'));
?>