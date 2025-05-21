<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "photo_gallery";

    $conn = mysqli_connect($host, $username, $password, $database);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    mysqli_set_charset($conn, "utf8mb4");
?>