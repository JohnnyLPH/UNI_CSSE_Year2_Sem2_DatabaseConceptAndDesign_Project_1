<?php
    // Staff Home Page.
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dbConnection.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/loginAuthenticate.php");

    $tempLoginCheck = checkLogin($conn);
    // Not logged in as Staff.
    if ($tempLoginCheck != 2) {
        header("Location: /index.php");
        exit;
    }

    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Staff: Home Page</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">
        
        <link rel="stylesheet" href="/css/main.css">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <!--<link rel="shortcut icon" href="/favicon.ico">-->
        <link rel="shortcut icon" href="https://icon-library.com/images/tree-icon/tree-icon-23.jpg">
    </head>

    <body>
        <header>
            <div class="maintheme w3-container">
                <h1>Staff: Home Page</h1>
            </div>           
        </header>

        <?php include($_SERVER['DOCUMENT_ROOT'] . "/staff/navigationBar.php"); ?>
        
        <main>
            <div class="w3-row">
                <div class="w3-container w3-quarter w3-sidebar w3-bar-block w3-theme-d5" style="width:25%">
                    <h2 class="w3-bar-item" >Welcome, <?php
                        echo($_SESSION["Username"]);
                    ?></h2>
                </div>

                <div class="wrapper w3-container w3-threequarter w3-theme-d4" style="margin-left:25%;">
                    <h2>What you need to know</h2>
                </div>

                <div class="w3-container w3-threequarter wrapper bgImgTree w3-animate-left" style="margin-left:25%;">
                    <div class='data-value card fadeIn first'>
                        <div class='data-group'>
                            <img class="oriImg" src="https://media.slidesgo.com/storage/21309973/conversions/12-parts-types-of-trees-for-education-thumb.jpg" id="icon" alt="User Icon" />
                        </div>
                    </div>
                    <div class='data-value card fadeIn second'>
                        <div class='data-group'>
                            <img class="oriImg" src="https://media.slidesgo.com/storage/21310025/conversions/32-parts-types-of-trees-for-education-thumb.jpg" id="icon" alt="User Icon" />
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
