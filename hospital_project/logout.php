<?php
// Destroys the active session token parameters and forces return routing to login terminal
session_start();
session_unset();
session_destroy();
header("Location: hospital_login.php");
exit();
?>