<?php
$host = 'localhost';    // or 127.0.0.1
$db   = 'lms';          // your database name
$user = 'root';         // your MySQL username
$pass = '';             // your MySQL password (often blank on localhost)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    // set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully"; // Optional, remove later
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
