<?php
  // list contents of /vat/log/nginx/acces.log
  $file = '/var/log/nginx/access.log';
  echo nl2br(file_get_contents($file));