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
                    <img src="https://png.pngtree.com/png-vector/20200124/ourmid/pngtree-client-and-designer-working-together-graphic-design-3d-isometric-illustration-perfect-png-image_2133712.jpg" id="icon" alt="User Icon" />
                    <br>
                    <button onclick="document.location='/login.php?UserType=CO'">Company</button>
                </div>

                <div class="card fadeIn second">
                    <img src="https://thumbs.dreamstime.com/b/call-center-customer-support-hotline-operator-advises-client-online-technical-vector-illustration-139728240.jpg" id="icon" alt="User Icon" />
                    <br>
                    <button onclick="document.location='/login.php?UserType=ST'">Staff</button>
                </div>

                <div class="card fadeIn third">
                    <img src="https://png.pngtree.com/png-vector/20190721/ourlarge/pngtree-business-meeting-with-client-illustration-concept-modern-flat-design-concept-png-image_1567633.jpg" id="icon" alt="User Icon" />
                    <br>
                    <button onclick="document.location='/login.php?UserType=CL'">Client</button>
                </div>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
