<?php
    // Company Manage Block Page.
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dbConnection.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/loginAuthenticate.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dataRetrieval.php");

    $tempLoginCheck = checkLogin($conn);
    // Not logged in as Company.
    if ($tempLoginCheck != 1) {
        header("Location: /index.php");
        exit;
    }

    $queryString = array();

    if (isset($_SERVER['QUERY_STRING'])) {
        parse_str($_SERVER['QUERY_STRING'], $queryString);
    }

    // Check if valid OrchardID is provided for search, set to 0 if not.
    $orchardID = (!isset($queryString["SearchKey"]) || $queryString["SearchKey"] < 1) ? 0: $queryString["SearchKey"];
    // Check if valid BlockID is provided for search, set to 0 if not.
    $blockID = (!isset($queryString["SearchKey"]) || $queryString["SearchKey"] < 1) ? 0: $queryString["SearchKey"];

    // Return all the block.
    $allBlock = getAllBlock($conn, $_SESSION["UserID"], $orchardID, $blockID);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Company: Manage Block Page</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">
        
        <link rel="stylesheet" href="/css/main.css">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <!--<link rel="shortcut icon" href="/favicon.ico">-->
        <link rel="shortcut icon" href="https://icon-library.com/images/tree-icon/tree-icon-23.jpg">
    </head>

    <body>
        <header>
            <h1>Company: Manage Block Page</h1>
        </header>

        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Company/navigationBar.php"); ?>

        <main>
            <h2>Block:</h2>

        </main>

        <footer>
            
        </footer>
    </body>
</html>
