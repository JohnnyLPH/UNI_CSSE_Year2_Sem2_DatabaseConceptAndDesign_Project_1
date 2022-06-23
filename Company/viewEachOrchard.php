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
                    <td>Orchard ID</td>
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
                    <td>Company ID</td>
                    <td><?php
                        echo($result["CompanyID"]);
                    ?></td>
                </tr>

                <tr>
                    <td>Total Block</td>
                    <td><?php
                        echo(getBlockCount($conn, $_SESSION["UserID"], $result["OrchardID"]));
                    ?></td>
                </tr>

                <tr>
                    <td>Total Tree</td>
                    <td><?php
                        echo(getTreeCount($conn, $_SESSION["UserID"], $result["OrchardID"]));
                    ?></td>
                </tr>

                <tr>
                    <td>Client Purchase</td>
                    <td><?php
                        echo(getPurchaseRequestCount($conn, 1, $_SESSION["UserID"], $result["OrchardID"]));
                    ?></td>
                </tr>
            </table>

            <form method="get" action="/Company/manageBlock.php">
                <input type="hidden" name="OrchardID" value="<?php
                    echo($orchardID);
                ?>">
                <input type="submit" value="View Related Blocks">
            </form>

            <form method="get" action="/Company/manageTree.php">
                <input type="hidden" name="OrchardID" value="<?php
                    echo($orchardID);
                ?>">
                <input type="submit" value="View Related Trees">
            </form>
            
            <form method="get" action="/Company/managePurchase.php">
                <input type="hidden" name="OrchardID" value="<?php
                    echo($orchardID);
                ?>">
                <input type="submit" value="View Related Purchases">
            </form>
            
            <form method="get" action="/Company/manageOrchard.php">
                <input type="submit" value="Back to View All Orchards">
            </form>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
