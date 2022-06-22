<?php
    // For connecting to DB.
    $serverName = "localhost";
    // Change username and password if needed, but remember DON'T COMMIT YOUR PASSWORD :)
    $username = "root";
    $password = "";
    // Database name, change if needed.
    $dbName = "db_assignment_1";

    // Create connection.
    $conn = new mysqli($serverName, $username, $password, $dbName);

    // Check connection.
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    GLOBAL $conn;
?>
