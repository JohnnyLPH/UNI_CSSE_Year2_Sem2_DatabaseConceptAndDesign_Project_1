<?php
    // Company Home Page.
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dbConnection.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/loginAuthenticate.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dataManagement.php");

    $tempLoginCheck = checkLogin($conn);
    // Not logged in as Company.
    if ($tempLoginCheck != 1) {
        header("Location: /index.php");
        exit;
    }

    $totalOrchard = getOrchardCount($conn, $_SESSION["UserID"]);
    $totalBlock = getBlockCount($conn, $_SESSION["UserID"]);
    $totalTree = getTreeCount($conn, $_SESSION["UserID"]);
    $totalPurchase = getPurchaseRequestCount($conn, 1, $_SESSION["UserID"]);

    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Company: Home Page</title>
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
                <h1>Company: Home Page</h1>
            </div>
        </header>

        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Company/navigationBar.php"); ?>

        <main>
            <div class="w3-row">
                <div class="w3-container w3-quarter w3-sidebar w3-bar-block w3-theme-d5" style="width:25%">
                    <h2 class="w3-bar-item" >Welcome, <?php
                        echo($_SESSION["Username"]);
                    ?></h2>
                </div>
                
                <div class="wrapper w3-container w3-threequarter w3-theme-d4" style="margin-left:25%;">
                    <h2>Your Summary:</h2>
                </div>

                <div class="w3-container w3-threequarter wrapper bgImgTree w3-animate-left" style="margin-left:25%;">                        
                    <div class='data-value card fadeIn first'>
                        <div class='data-group'>
                            <img src="/img/defaults/orchardIcon.png" id="icon" alt="User Icon" />
                            <br>
                            <span class='overall-data'><?php
                                echo($totalOrchard);
                            ?></span>
                            <span class='data-title'>Total Orchard Owned</span>
                        </div>
                    </div>
                    <div class='data-value card fadeIn second'>
                        <div class='data-group'>
                            <img src="/img/defaults/blockIcon.jpg" id="icon" alt="User Icon" />
                            <br>
                            <span class='overall-data'><?php
                                echo($totalBlock);
                            ?></span>
                            <span class='data-title'>Total Block Owned</span>
                        </div>
                    </div>
                    <div class='data-value card fadeIn third'>
                        <div class='data-group'>
                            <img src="/img/defaults/treeIcon.jpg" id="icon" alt="User Icon" />
                            <br>
                            <span class='overall-data'><?php
                                echo($totalTree);
                            ?></span>
                            <span class='data-title'>Total Tree Planted</span>
                        </div>
                    </div>
                    <div class='data-value card fadeIn fourth'>
                        <div class='data-group'>
                            <img src="/img/defaults/purchaseIcon.png" id="icon" alt="User Icon" />
                            <br>
                            <span class='overall-data'><?php
                                echo($totalPurchase);
                            ?></span>
                            <span class='data-title'>Success Client Purchase</span>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
