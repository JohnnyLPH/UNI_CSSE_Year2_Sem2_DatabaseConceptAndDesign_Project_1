<?php
    // Staff Registration Page.
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dbConnection.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/loginAuthenticate.php");

    $tempLoginCheck = checkLogin($conn);
    // Logged in.
    if ($tempLoginCheck != 0) {
        header("Location: /index.php");
        exit;
    }

    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Staff: Registration Page</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">
        
        <link rel="stylesheet" href="/css/main.css">
        <link rel="shortcut icon" href="/favicon.ico">
    </head>

    <body>
        <header>
            <h1>Staff: Registration Page</h1>
        </header>

        <main>
            <a href="/login.php?UserType=ST">Back to Login</a><br>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
