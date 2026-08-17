<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "my_website"; // તારા સ્ક્રીનશોટ મુજબ અહીં my_website સેટ કર્યું છે

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);

}
