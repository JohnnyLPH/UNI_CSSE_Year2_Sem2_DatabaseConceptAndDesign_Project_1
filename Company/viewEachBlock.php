<?php
    // Company Manage Orchard Page.
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

    $allBlock = NULL;
    // Block is not available for viewing.
    if (
        !isset($queryString["BlockID"]) ||
        !is_numeric($queryString["BlockID"]) ||
        $queryString["BlockID"] < 1 ||
        count($allBlock = getBlockLatestClient($conn, $_SESSION["UserID"], 0, $queryString["BlockID"])) < 1
    ) {
        header("Location: /Company/manageBlock.php");
        exit;
    }

    $blockID = $queryString["BlockID"];
    $result = $allBlock[0];

    $clientID = $clientName = "None";
    // Valid Owner of the Block.
    if (
        $result["ClientID"] > 0 &&
        $result["ApprovalStatus"] == 1
    ) {
        $clientID = $result["ClientID"];
        $clientName = $result["RealName"];
    }

    $allPurchaseRequest = getAllPurchaseRequest($conn, -1, $_SESSION["UserID"], 0, $queryString["BlockID"]);
    $allOnSale = getAllOnSale($conn, $_SESSION["UserID"], 0, $queryString["BlockID"]);

    $treeCount = getTreeCount(
        $conn, $_SESSION["UserID"], $result["OrchardID"], $result["BlockID"]
    );
    // $totalPurchaseCount = getPurchaseRequestCount(
    //     $conn, -1, $_SESSION["UserID"], $result["OrchardID"], $result["BlockID"]
    // );
    $totalPurchaseCount = count($allPurchaseRequest);
    $successPurchaseCount = getPurchaseRequestCount(
        $conn, 1, $_SESSION["UserID"], $result["OrchardID"], $result["BlockID"]
    );
    $totalOnSaleCount = count($allOnSale);
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

        <script src=".../css/stickynav.js"></script>
    </head>

    <body>
        <header>
            <div class="maintheme w3-container">
                <h1>Company: Manage Block Page</h1>
            </div>
        </header>

        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Company/navigationBar.php"); ?>

        <main>
            <div class="w3-row">
                <div class="w3-container w3-quarter w3-sidebar w3-bar-block w3-theme-d5" style="width:25%;">
                    <br>
                    <form method="get" action="/Company/viewEachOrchard.php">
                        <input type="hidden" name="OrchardID" value="<?php
                            echo($result["OrchardID"]);
                        ?>">
                        <input class="fullW" type="submit" value="View Related Orchard">
                    </form>

                    <?php if ($treeCount > 0): ?>
                        <form method="get" action="/Company/manageTree.php">
                            <input type="hidden" name="SearchKey" value="<?php
                                echo($blockID);
                            ?>">
                            <input type="hidden" name="SearchOption" value="2">
                            <input class="fullW" type="submit" value="View Related Trees">
                        </form>
                    <?php endif; ?>
                    
                    <?php if ($totalPurchaseCount > 0): ?>
                        <form method="get" action="/Company/managePurchase.php">
                            <input type="hidden" name="SearchKey" value="<?php
                                echo($blockID);
                            ?>">
                            <input type="hidden" name="SearchOption" value="2">
                            <input class="fullW" type="submit" value="View Related Purchases">
                        </form>
                    <?php endif; ?>
                    
                    <form method="get" action="/Company/manageBlock.php">
                        <input class="fullW" type="submit" value="Back to View All Blocks">
                    </form>
                </div>
                <div class="w3-container w3-threequarter w3-theme-d4 w3-animate-left" style="margin-left:25%; padding-bottom:2%;">
                    <h2>Block ID <?php
                        echo($blockID);
                    ?>:</h2>

                    <table class=" w3-center w3-table-all w3-hoverable" style="width:100%">
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
                                echo($treeCount);
                            ?></td>
                        </tr>

                        <tr>
                            <td>Client ID (Owner)</td>
                            <td><?php
                                echo($clientID);
                            ?></td>
                        </tr>

                        <tr>
                            <td>Client Name (Owner)</td>
                            <td><?php
                                echo($clientName);
                            ?></td>
                        </tr>

                        <tr>
                            <td>Total Sale</td>
                            <td><?php
                                echo($totalOnSaleCount);
                            ?></td>
                        </tr>

                        <tr>
                            <td>Total Purchase Request</td>
                            <td><?php
                                echo($totalPurchaseCount);
                            ?></td>
                        </tr>

                        <tr>
                            <td>Success Client Purchase</td>
                            <td><?php
                                echo($successPurchaseCount);
                            ?></td>
                        </tr>
                    </table>

                    <h3>Purchase Request:</h3>
                    <?php if (count($allPurchaseRequest) > 0): ?>
                        <table class=" w3-center w3-table-all w3-centered w3-hoverable" style="width:100%">
                            <tr>
                                <th>Request ID</th>
                                <th>Sale ID</th>
                                <th>Client ID</th>
                                <th>Request Date</th>
                                <th>Request Price (RM)</th>
                                <th>Approval Status</th>
                                <th>Action</th>
                            </tr>
                            <?php foreach ($allPurchaseRequest as $result): ?>
                                <tr>
                                    <td><?php
                                        echo($result["RequestID"]);
                                    ?></td>

                                    <td><?php
                                        echo($result["SaleID"]);
                                    ?></td>

                                    <td><?php
                                        echo($result["ClientID"]);
                                    ?></td>

                                    <td><?php
                                        echo($result["RequestDate"]);
                                    ?></td>

                                    <td><?php
                                        echo($result["RequestPrice"]);
                                    ?></td>

                                    <td><?php
                                        echo(getApprovalStatusStr($result["ApprovalStatus"]));
                                    ?></td>

                                    <td>
                                        <form method="get" action="/Company/viewEachPurchase.php">
                                            <input type="hidden" name="RequestID" value="<?php
                                                echo($result["RequestID"]);
                                            ?>">
                                            <input type="submit" value="View">
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    <?php else: ?>
                        <span>* No Purchase Request for Block ID <?php
                            echo($blockID);
                        ?>! *</span>
                    <?php endif; ?>

                    <h3>On Sale History:</h3>
                    <?php if (count($allOnSale) > 0): ?>
                        <table class=" w3-center w3-table-all w3-centered w3-hoverable" style="width:100%">
                            <tr>
                                <th>Sale ID</th>
                                <th>Client ID (Seller)</th>
                                <th>Sale Date</th>
                                <th>Sale Price (RM)</th>
                            </tr>
                            <?php foreach ($allOnSale as $result): ?>
                                <tr>
                                    <td><?php
                                        echo($result["SaleID"]);
                                    ?></td>

                                    <td><?php
                                        echo(empty($result["SellerID"]) ? "None": $result["SellerID"]);
                                    ?></td>

                                    <td><?php
                                        echo($result["SaleDate"]);
                                    ?></td>

                                    <td><?php
                                        echo($result["SalePrice"]);
                                    ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    <?php else: ?>
                        <span>* No On Sale History for Block ID <?php
                            echo($blockID);
                        ?>! *</span>
                    <?php endif; ?>
                </div>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
