<?php
    // Company Manage Block Page.
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dbConnection.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/loginAuthenticate.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dataManagement.php");

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

    // Check if valid OrchardID, BlockID or ClientID is provided for search, set to 0 if not.
    $orchardID = $blockID = $clientID = (
        !isset($queryString["SearchKey"]) ||
        !is_numeric($queryString["SearchKey"]) ||
        $queryString["SearchKey"] < 1
    ) ? 0: $queryString["SearchKey"];

    // Check if valid SearchOption is provided.
    $searchOption = (
        !isset($queryString["SearchOption"]) ||
        !is_numeric($queryString["SearchOption"]) ||
        $queryString["SearchOption"] < 1 ||
        $queryString["SearchOption"] > 3
    ) ? 1: $queryString["SearchOption"];

    // Search by OrchardID.
    if ($searchOption == 1) {
        $blockID = $clientID = 0;
    }
    // Search by BlockID.
    elseif ($searchOption == 2) {
        $orchardID = $clientID = 0;
    }
    // Search by ClientID.
    else {
        $orchardID = $blockID = 0;
    }

    // Return all the block & latest client.
    $allBlock = getBlockLatestClient($conn, $_SESSION["UserID"], $orchardID, $blockID, $clientID);
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
            <div class="maintheme w3-container">
                <h1>Company: Manage Block Page</h1>
            </div>
        </header>

        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Company/navigationBar.php"); ?>

        <main>
            <div class="w3-container w3-theme-d4 w3-animate-opacity">
                <h2 class="w3-center">All Blocks:</h2>

                <form id="reset-search" method="get" action="/Company/manageBlock.php"></form>

                <form class="w3-center" method="get" action="/Company/manageBlock.php">
                    <input style="width:98%" id="SearchKey" type="number" name="SearchKey" value="<?php
                        // Valid SearchKey.
                        if ($orchardID > 0) {
                            echo($orchardID);
                        }
                        elseif ($blockID > 0) {
                            echo($blockID);
                        }
                        elseif ($clientID > 0) {
                            echo($clientID);
                        }
                    ?>" placeholder="Enter Orchard/Block/Client ID" min="1" required>

                    <!-- <label for="SearchOption">Search By:</label> -->
                    <select id="SearchOption" name="SearchOption">
                        <option value="1"<?php
                            if ($searchOption == 1) {
                                echo(" selected");
                            }
                        ?>>OrchardID</option>
                        <option value="2"<?php
                            if ($searchOption == 2) {
                                echo(" selected");
                            }
                        ?>>BlockID</option>
                        <option value="3"<?php
                            if ($searchOption == 3) {
                                echo(" selected");
                            }
                        ?>>ClientID</option>
                    </select>
                    
                    <input type="submit" value="Search">

                    <input form="reset-search" type="submit" value="Reset"<?php
                        // Disable if not searching.
                        if ($orchardID + $blockID + $clientID < 1) {
                            echo(" disabled");
                        }
                    ?>>
                </form>

                <div class="w3-container w3-center" style="align-content:center;">
                    <?php if (count($allBlock) > 0): ?>
                        <table class=" w3-center w3-table-all w3-centered w3-hoverable" style="width:100%">
                            <tr>
                                <th>Block ID</th>
                                <th>Orchard ID</th>
                                <th>Total Tree</th>
                                <th>Client ID (Owner)</th>
                                <th>Total Sale</th>
                                <th>Success Client Purchase</th>
                                <th>Action</th>
                            </tr>
                            <?php foreach ($allBlock as $result): ?>
                                <tr>
                                    <td><?php
                                        echo($result["BlockID"]);
                                    ?></td>

                                    <td><?php
                                        echo($result["OrchardID"]);
                                    ?></td>

                                    <td><?php
                                        echo(getTreeCount(
                                            $conn, $_SESSION["UserID"], $result["OrchardID"], $result["BlockID"]
                                        ));
                                    ?></td>

                                    <td><?php
                                        $foundClient = false;

                                        // Valid Owner of the Block.
                                        if (
                                            $result["ClientID"] > 0 &&
                                            $result["ApprovalStatus"] == 1
                                        ) {
                                            echo($result["ClientID"]);
                                        }
                                        else {
                                            echo("None");
                                        }
                                    ?></td>

                                    <td><?php
                                        echo(getOnSaleCount(
                                            $conn, $_SESSION["UserID"], $result["OrchardID"], $result["BlockID"]
                                        ));
                                    ?></td>

                                    <td><?php
                                        echo(getPurchaseRequestCount(
                                            $conn, 1, $_SESSION["UserID"], $result["OrchardID"], $result["BlockID"]
                                        ));
                                    ?></td>
                                    
                                    <td>
                                        <form method="get" action="/Company/viewEachBlock.php">
                                            <input type="hidden" name="BlockID" value="<?php
                                                echo($result["BlockID"]);
                                            ?>">
                                            <input type="submit" value="View">
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                        <br>
                    <?php else: ?>
                        <span>* <?php
                            if ($orchardID + $blockID + $clientID < 1) {
                                echo("No block is found!");
                            }
                            elseif ($searchOption == 1) {
                                echo(
                                    "Orchard ID $orchardID is not associated with any block of " .
                                    $_SESSION["Username"] . "!"
                                );
                            }
                            elseif ($searchOption == 2) {
                                echo(
                                    "Block ID $blockID is not associated with any block of " .
                                    $_SESSION["Username"] . "!"
                                );
                            }
                            else {
                                echo(
                                    "Client ID $clientID is not associated with any block of " .
                                    $_SESSION["Username"] . "!"
                                );
                            }
                        ?> *</span>
                    <?php endif; ?>
                </div>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
