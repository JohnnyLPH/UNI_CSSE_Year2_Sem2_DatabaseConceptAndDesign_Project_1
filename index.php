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
        <header class="title">
            <h1>Group 1: DB Project</h1>
            <h2>Tree Profiling Management System</h2>
        </header>

        <main>
            <div class="wrapper fadeIn ">
                <h2>Select User Type:</h2>
            </div>
            <div class="wrapper bgImgTree fadeIn ">
               
                <div class="card fadeIn first">
                    <img src="https://icon-library.com/images/username-icon/username-icon-11.jpg" id="icon" alt="User Icon" />
                    <br>
                    <button onclick="document.location='/login.php?UserType=CO'">Company</button>
                </div>

                <div class="card fadeIn second">
                    <img src="https://icon-library.com/images/username-icon/username-icon-11.jpg" id="icon" alt="User Icon" />
                    <br>
                    <button onclick="document.location='/login.php?UserType=ST'">Staff</button>
                </div>

                <div class="card fadeIn third">
                    <img src="https://icon-library.com/images/username-icon/username-icon-11.jpg" id="icon" alt="User Icon" />
                    <br>
                    <button onclick="document.location='/login.php?UserType=CL'">Client</button>
                </div>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
