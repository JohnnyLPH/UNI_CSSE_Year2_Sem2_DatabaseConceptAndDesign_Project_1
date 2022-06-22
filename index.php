<?php
    // System Home Page: Ask to choose user type.
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dbConnection.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/loginAuthenticate.php");

    $tempLoginCheck = checkLogin($conn);
    if ($tempLoginCheck == 1) {
        header("Location: /Company/index.php");
        exit;
    }
    else if ($tempLoginCheck == 2) {
        header("Location: /Staff/index.php");
        exit;
    }
    else if ($tempLoginCheck == 3) {
        header("Location: /Client/index.php");
        exit;
    }

    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>System: Home Page</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">
        
        <link rel="stylesheet" href="/css/main.css">
        <link rel="shortcut icon" href="/favicon.ico">
    </head>

    <body>
        <header>
            <h1>Group 1: DB Project</h1>
        </header>

        <main>
            <h2>Tree Profiling Management System</h2>

            <div class="main-content">
                <p>
                    Select User Type:
                </p>
                <a href="/login.php?UserType=CO">Company</a><br>
                <a href="/login.php?UserType=ST">Staff</a><br>
                <a href="/login.php?UserType=CL">Client</a><br>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
