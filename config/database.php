<?php
$host = 'localhost';
$dbName = 'daaweeye';
$dbUser = 'root';
$dbPass = '';

$mysqli = new mysqli($host, $dbUser, $dbPass, $dbName);
if ($mysqli->connect_errno) {
    die('Database connection failed: ' . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');
