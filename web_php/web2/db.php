<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = 'php01';

$conn = new mysqli($servername, $username, $password, $dbname);



if ($conn->connect_error) {
    die("Error connecting" . $conn->connect_error);
}






?>

