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
    else if ($tempLoginCheck == 4) {
        header("Location: /Admin/index.php");
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
        <!--<link rel="shortcut icon" href="/favicon.ico">-->
        <link rel="shortcut icon" href="https://icon-library.com/images/tree-icon/tree-icon-23.jpg">
    </head>

    <body>
        <header class="title">
            <h1 style="color:white">Group 1: DB Project</h1>
        </header>

        <main>
            <div class="wrapper fadeIn w3-theme-d5" style="padding: 10px;">
                <h3>Select User Type:</h3>
            </div>
            <div class="wrapper bgImgTree fadeIn ">
               
                <div class="card fadeIn first">
                    <img src="/img/defaults/companyIcon.jpg" id="icon" alt="User Icon" />
                    <br>
                    <button onclick="document.location='/login.php?UserType=CO'">Company</button>
                </div>

                <div class="card fadeIn second">
                    <img src="/img/defaults/staffIcon.jpg" id="icon" alt="User Icon" />
                    <br>
                    <button onclick="document.location='/login.php?UserType=ST'">Staff</button>
                </div>

                <div class="card fadeIn third">
                    <img src="/img/defaults/clientIcon.jpg" id="icon" alt="User Icon" />
                    <br>
                    <button onclick="document.location='/login.php?UserType=CL'">Client</button>
                </div>

                <div class="card fadeIn fourth">
                    <img src="/img/defaults/adminIcon.png" id="icon" alt="User Icon" />
                    <br>
                    <button onclick="document.location='/login.php?UserType=AD'">Admin</button>
                </div>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
