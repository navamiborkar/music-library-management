<?php
$servername = "localhost";
$username = "navami";
$password = "navami";
$db_name = "music_system"; // Check that this database exists

$conn = new mysqli($servername, $username, $password, $db_name, 3305);  // Make sure port matches

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "";
?>
