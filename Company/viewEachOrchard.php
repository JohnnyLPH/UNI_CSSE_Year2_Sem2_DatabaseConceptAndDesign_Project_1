<?php
    // Company Manage Orchard Page.
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

    $allOrchard = NULL;
    // Orchard is not available for viewing.
    if (
        !isset($queryString["OrchardID"]) ||
        $queryString["OrchardID"] < 1 ||
        count($allOrchard = getAllOrchard($conn, $_SESSION["UserID"], $queryString["OrchardID"])) < 1
    ) {
        header("Location: /Company/manageOrchard.php");
        exit;
    }

    $orchardID = $queryString["OrchardID"];
    $result = $allOrchard[0];
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Company: Manage Orchard Page</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">
        
        <link rel="stylesheet" href="/css/main.css">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <!--<link rel="shortcut icon" href="/favicon.ico">-->
        <link rel="shortcut icon" href="https://icon-library.com/images/tree-icon/tree-icon-23.jpg">
    </head>

    <body>
        <header>
            <h1>Company: Manage Orchard Page</h1>
        </header>

        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Company/navigationBar.php"); ?>

        <main>
            <h2>Orchard ID <?php
                echo($orchardID);
            ?>:</h2>

            <table>
                <tr>
                    <td>OrchardID</td>
                    <td><?php
                        echo($result["OrchardID"]);
                    ?></td>
                </tr>
                <tr>
                    <td>Address</td>
                    <td><?php
                        echo($result["Address"]);
                    ?></td>
                </tr>
                <tr>
                    <td>Latitude</td>
                    <td><?php
                        echo($result["Latitude"]);
                    ?></td>
                </tr>
                <tr>
                    <td>Longitude</td>
                    <td><?php
                        echo($result["Longitude"]);
                    ?></td>
                </tr>
                <tr>
                    <td>CompanyID</td>
                    <td><?php
                        echo($result["CompanyID"]);
                    ?></td>
                </tr>
            </table>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
