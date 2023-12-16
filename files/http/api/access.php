<?php
//print the contents of /var/log/nginx/acces.log
echo nl2br(file_get_contents('/var/log/nginx/access.log'));
?>