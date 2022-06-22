<?php
    // Company Home Page.
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dbConnection.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/loginAuthenticate.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dataRetrieval.php");

    $tempLoginCheck = checkLogin($conn);
    // Not logged in as Company.
    if ($tempLoginCheck != 1) {
        header("Location: /index.php");
        exit;
    }

    $totalOrchard = $totalBlock = $totalTree = $totalPurchase = "";

    $totalOrchard = getCompanyOrchardCount($conn, $_SESSION["UserID"]);

    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Company: Home Page</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">
        
        <link rel="stylesheet" href="/css/main.css">
        <link rel="shortcut icon" href="/favicon.ico">
    </head>

    <body>
        <header>
            <h1>Company: Home Page</h1>
        </header>

        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Company/navigationBar.php"); ?>

        <main>
            <h2>Logged In: <?php
                echo($_SESSION["Username"]);
            ?></h2>

            <div class="main-content">
                <div class="report-content">
                    <div class='data-value'>
                        <div class='data-group'>
                            <span class='overall-data'><?php
                                echo($totalOrchard);
                            ?></span>
                            <span class='data-title'>Total Orchard Owned</span>
                        </div>
                    </div>
                    <div class='data-value'>
                        <div class='data-group'>
                            <span class='overall-data'><?php
                                echo("2");
                            ?></span>
                            <span class='data-title'>Total Block Owned</span>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
