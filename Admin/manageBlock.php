<?php
    // Admin Manage Block Page.
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dbConnection.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/loginAuthenticate.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dataManagement.php");

    $tempLoginCheck = checkLogin($conn);
    // Not logged in as Admin.
    if ($tempLoginCheck != 4) {
        header("Location: /index.php");
        exit;
    }

    $queryString = array();

    if (isset($_SERVER['QUERY_STRING'])) {
        parse_str($_SERVER['QUERY_STRING'], $queryString);
    }

    // Check if valid CompanyID, OrchardID, BlockID or ClientID is provided for search, set to 0 if not.
    $companyID = $orchardID = $blockID = $clientID = (
        !isset($queryString["SearchKey"]) ||
        !is_numeric($queryString["SearchKey"]) ||
        $queryString["SearchKey"] < 1
    ) ? 0: $queryString["SearchKey"];

    // Check if valid SearchOption is provided.
    $searchOption = (
        !isset($queryString["SearchOption"]) ||
        !is_numeric($queryString["SearchOption"]) ||
        $queryString["SearchOption"] < 1 ||
        $queryString["SearchOption"] > 4
    ) ? 1: $queryString["SearchOption"];

    // Search by CompanyID.
    if ($searchOption == 1) {
        $orchardID = $blockID = $clientID = 0;
    }
    // Search by OrchardID.
    elseif ($searchOption == 2) {
        $companyID = $blockID = $clientID = 0;
    }
    // Search by BlockID.
    elseif ($searchOption == 3) {
        $companyID = $orchardID = $clientID = 0;
    }
    // Search by ClientID.
    else {
        $companyID = $orchardID = $blockID = 0;
    }

    // Return all the block & latest client.
    $allBlock = getBlockLatestClient($conn, $companyID, $orchardID, $blockID, $clientID);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Admin: Manage Block Page</title>
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
                <h1>Admin: Manage Block Page &#128027;</h1>
            </div>
        </header>

        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Admin/navigationBar.php"); ?>

        <main>
            <div class="w3-container w3-theme-d4 w3-animate-opacity">
                <h2 class="w3-center">All Blocks:</h2>

                <form id="reset-search" method="get" action="/Admin/manageBlock.php"></form>
                <form id="add-block" method="get" action="/Admin/addBlock.php"></form>

                <form class="w3-center" method="get" action="/Admin/manageBlock.php">
                    <input style="width:98%" id="SearchKey" type="number" name="SearchKey" value="<?php
                        // Valid SearchKey.
                        if ($companyID > 0) {
                            echo($companyID);
                        }
                        elseif ($orchardID > 0) {
                            echo($orchardID);
                        }
                        elseif ($blockID > 0) {
                            echo($blockID);
                        }
                        elseif ($clientID > 0) {
                            echo($clientID);
                        }
                    ?>" placeholder="Enter Company/Orchard/Block/Client ID" min="1" required>

                    <!-- <label for="SearchOption">Search By:</label> -->
                    <select id="SearchOption" name="SearchOption">
                        <option value="1"<?php
                            if ($searchOption == 1) {
                                echo(" selected");
                            }
                        ?>>CompanyID</option>
                        <option value="2"<?php
                            if ($searchOption == 2) {
                                echo(" selected");
                            }
                        ?>>OrchardID</option>
                        <option value="3"<?php
                            if ($searchOption == 3) {
                                echo(" selected");
                            }
                        ?>>BlockID</option>
                        <option value="4"<?php
                            if ($searchOption == 4) {
                                echo(" selected");
                            }
                        ?>>ClientID</option>
                    </select>
                    
                    <input type="submit" value="Search">

                    <input form="reset-search" type="submit" value="Reset"<?php
                        // Disable if not searching.
                        if ($companyID + $orchardID + $blockID + $clientID < 1) {
                            echo(" disabled");
                        }
                    ?>>

                    <input form="add-block" type="submit" value="Add Block">
                </form>

                <div class="w3-container w3-center" style="align-content:center;">
                    <?php if (count($allBlock) > 0): ?>
                        <table class=" w3-center w3-table-all w3-centered w3-hoverable" style="width:100%">
                            <tr>
                                <th>Block ID</th>
                                <th>Orchard ID</th>
                                <th>Company ID</th>
                                <th>Total Tree</th>
                                <th>Client ID (Owner)</th>
                                <th>Total Sale</th>
                                <th>Success Client Purchase</th>
                                <th>Action &#129488;</th>
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
                                        echo($result["CompanyID"]);
                                    ?></td>

                                    <td><?php
                                        echo(getTreeCount(
                                            $conn, $result["CompanyID"], $result["OrchardID"], $result["BlockID"]
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
                                            $conn, 0, 0, $result["BlockID"]
                                        ));
                                    ?></td>

                                    <td><?php
                                        echo(getPurchaseRequestCount(
                                            $conn, 1, $result["CompanyID"], $result["OrchardID"], $result["BlockID"]
                                        ));
                                    ?></td>
                                    
                                    <td>
                                        <form method="get" action="/Admin/viewEachBlock.php">
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
                            if ($companyID + $orchardID + $blockID + $clientID < 1) {
                                echo("No block is found!");
                            }
                            elseif ($searchOption == 1) {
                                echo(
                                    "Company ID $companyID is not associated with any block!"
                                );
                            }
                            elseif ($searchOption == 2) {
                                echo(
                                    "Orchard ID $orchardID is not associated with any block!"
                                );
                            }
                            elseif ($searchOption == 3) {
                                echo(
                                    "Block ID $blockID is not associated with any block!"
                                );
                            }
                            else {
                                echo(
                                    "Client ID $clientID is not associated with any block!"
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
