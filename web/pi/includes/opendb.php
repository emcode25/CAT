<?php
//Define Access credentials
$servername = "localhost";
$username = "USERNAME";
$password = "PASSWORD";
$dbname = "cat";
//connection string
$conn = new mysqli($servername, $username, $password, $dbname);
//check for successful connection
if ($conn->connect_error)
{
    die("Connection failed" . mysqli_connect_error());
}
$driver = new mysqli_driver();
$driver->report_mode = MYSQLI_REPORT_ALL ^ MYSQLI_REPORT_INDEX;
?>
