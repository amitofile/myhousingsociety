<?php

if ($_SERVER['REQUEST_METHOD'] == 'GET' && realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
  header('HTTP/1.0 404 Not Found', TRUE, 404);
  die("<h1>Not Found</h1><p>The requested URL was not found on this server.</p>");
}

$servername = "localhost";
$username = "root";
$password = "<password>";
$dbname = "mysociety001";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

//echo "Connected successfully";



