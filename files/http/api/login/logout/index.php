<?php
// Logout API endpoint
// Restarts the session and unsets all session variables

session_start();
session_unset();
session_destroy();
