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

    $allBlock = NULL;
    // Block is not available for viewing.
    if (
        !isset($queryString["BlockID"]) ||
        $queryString["BlockID"] < 1 ||
        count($allBlock = getAllBlock($conn, $_SESSION["UserID"], 0, $queryString["BlockID"])) < 1
    ) {
        header("Location: /Company/manageOrchard.php");
        exit;
    }

    $blockID = $queryString["BlockID"];
    $result = $allBlock[0];
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
            <h2>Block ID <?php
                echo($blockID);
            ?>:</h2>

            <table>
                <tr>
                    <td>Block ID</td>
                    <td><?php
                        echo($result["BlockID"]);
                    ?></td>
                </tr>

                <tr>
                    <td>Orchard ID</td>
                    <td><?php
                        echo($result["OrchardID"]);
                    ?></td>
                </tr>

                <tr>
                    <td>Total Tree</td>
                    <td><?php
                        echo(getTreeCount(
                            $conn, $_SESSION["UserID"], $result["OrchardID"], $result["BlockID"]
                        ));
                    ?></td>
                </tr>

                <tr>
                    <td>Client Purchase</td>
                    <td><?php
                        echo(getPurchaseRequestCount(
                            $conn, 1, $_SESSION["UserID"], $result["OrchardID"], $result["BlockID"]
                        ));
                    ?></td>
                </tr>
            </table>

            <form method="get" action="/Company/viewEachOrchard.php">
                <input type="hidden" name="OrchardID" value="<?php
                    echo($result["OrchardID"]);
                ?>">
                <input type="submit" value="View Related Orchard">
            </form>

            <form method="get" action="/Company/manageTree.php">
                <input type="hidden" name="SearchKey" value="<?php
                    echo($blockID);
                ?>">
                <input type="hidden" name="SearchOption" value="2">
                <input type="submit" value="View Related Trees">
            </form>
            
            <form method="get" action="/Company/managePurchase.php">
                <input type="hidden" name="SearchKey" value="<?php
                    echo($blockID);
                ?>">
                <input type="hidden" name="SearchOption" value="2">
                <input type="submit" value="View Related Purchases">
            </form>
            
            <form method="get" action="/Company/manageBlock.php">
                <input type="submit" value="Back to View All Blocks">
            </form>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
