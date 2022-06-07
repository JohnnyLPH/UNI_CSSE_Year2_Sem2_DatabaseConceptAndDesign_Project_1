<?php
    $servername = "localhost";
    // Change username and password if needed, but remember don't commit your password :)
    $username = "root";
    $password = "";
    // Database name, change if needed.
    $dbname = "db_assignment_1";

    // Create connection.
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection.
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    global $conn;
?>
