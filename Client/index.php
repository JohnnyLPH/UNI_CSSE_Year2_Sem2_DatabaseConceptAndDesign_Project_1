<?php
    // Client Home Page.
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dbConnection.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/loginAuthenticate.php");

    $tempLoginCheck = checkLogin($conn);
    // Not logged in as Client.
    if ($tempLoginCheck != 3) {
        header("Location: /index.php");
        exit;
    }

    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Client: Home Page</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">
        
        <!-- <link rel="stylesheet" href="/css/main.css"> -->
        <!-- <link rel="shortcut icon" href="/favicon.ico"> -->
    </head>

    <body>
        <header>
            <h1>Client: Home Page</h1>
        </header>

        <main>
            <a href="/logout.php">Log Out</a><br>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
