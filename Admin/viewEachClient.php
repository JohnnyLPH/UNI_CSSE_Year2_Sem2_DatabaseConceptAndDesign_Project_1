<?php
    // Admin Manage Client Page.
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dbConnection.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/loginAuthenticate.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/inputValidation.php");
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

    $allClient = NULL;
    // Client is not available for viewing.
    if (
        !isset($queryString["ClientID"]) ||
        !is_numeric($queryString["ClientID"]) ||
        $queryString["ClientID"] < 1 ||
        count($allClient = getAllClient($conn, $queryString["ClientID"])) < 1
    ) {
        header("Location: /Admin/manageClient.php");
        exit;
    }

    $clientID = $queryString["ClientID"];
    $result = $allClient[0];

    $allPurchaseRequest = getAllPurchaseRequest($conn, -1, 0, 0, 0, 0, 0, $queryString["ClientID"]);
    $allOnSale = getAllOnSale($conn, 0, 0, 0, 0, $queryString["ClientID"]);

    $blockCount = count(getBlockLatestClient($conn, 0, 0, 0, $result["UserID"]));
    // $totalPurchaseCount = getPurchaseRequestCount($conn, -1, 0, 0, 0, 0, $result["UserID"]);
    $totalPurchaseCount = count($allPurchaseRequest);
    // $totalSaleCount = getOnSaleCount($conn, 0, 0, 0, $result["UserID"]);
    $totalSaleCount = count($allOnSale);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Admin: Manage Client Page</title>
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
                <h1>Admin: Manage Client Page</h1>
            </div>
        </header>

        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Admin/navigationBar.php"); ?>

        <main>
            <div class="w3-row">
                <div class="w3-container w3-threequarter w3-theme-d4 w3-animate-left" style="margin-left:25%; padding-bottom:2%; padding-top:2%;">
                    <div class="w3-container">
                        <h2>Client ID <?php
                            echo($clientID);
                        ?>:</h2> 

                        <table class="w3-table-all">
                            <tr>
                                <td>Client ID</td>
                                <td><?php
                                    echo($result["UserID"]);
                                ?></td>
                            </tr>

                            <tr>
                                <td>Username</td>
                                <td><?php
                                    echo($result["Username"]);
                                ?></td>
                            </tr>

                            <tr>
                                <td>Email</td>
                                <td><?php
                                    echo($result["Email"]);
                                ?></td>
                            </tr>

                            <tr>
                                <td>Client Name</td>
                                <td><?php
                                    echo($result["RealName"]);
                                ?></td>
                            </tr>

                            <tr>
                                <td>Address</td>
                                <td><?php
                                    echo($result["Address"]);
                                ?></td>
                            </tr>

                            <tr>
                                <td>Country</td>
                                <td><?php
                                    echo($result["Country"]);
                                ?></td>
                            </tr>

                            <tr>
                                <td>Profile Picture</td>
                                <td><img id="icon" src="<?php
                                    echo(cleanInput($result["Photo"]));
                                ?>" alt="* ClientID <?php
                                    echo($result["UserID"]);
                                ?> img *" width="350"></td>

                            </tr>

                            <tr>
                                <td>Total Block Owned</td>
                                <td><?php
                                    echo($blockCount);
                                ?></td>
                            </tr>

                            <tr>
                                <td>Total Purchase Request</td>
                                <td><?php
                                    echo($totalPurchaseCount);
                                ?></td>
                            </tr>

                            <tr>
                                <td>Total Sale</td>
                                <td><?php
                                    echo($totalSaleCount);
                                ?></td>
                            </tr>
                        </table>

                        <h3>Purchase Request:</h3>
                        <?php if (count($allPurchaseRequest) > 0): ?>
                            <table class=" w3-center w3-table-all w3-centered w3-hoverable" style="width:100%">
                                <tr>
                                    <th>Request ID</th>
                                    <th>Sale ID</th>
                                    <th>Block ID</th>
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
                                            echo($result["BlockID"]);
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
                                            <form method="get" action="/Admin/viewEachPurchase.php">
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
                            <span>* No Purchase Request for Client ID <?php
                                echo($clientID);
                            ?>! *</span>
                        <?php endif; ?>

                        <h3>Sale History of Owned Blocks:</h3>
                        <?php if (count($allOnSale) > 0): ?>
                            <table class=" w3-center w3-table-all w3-centered w3-hoverable" style="width:100%">
                                <tr>
                                    <th>Sale ID</th>
                                    <th>Block ID</th>
                                    <th>Sale Date</th>
                                    <th>Sale Price (RM)</th>
                                </tr>
                                <?php foreach ($allOnSale as $result): ?>
                                    <tr>
                                        <td><?php
                                            echo($result["SaleID"]);
                                        ?></td>

                                        <td><?php
                                            echo($result["BlockID"]);
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
                            <span>* No Block Sale History for Client ID <?php
                                echo($clientID);
                            ?>! *</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="w3-container w3-quarter w3-sidebar w3-bar-block w3-theme-d5" style="width:25%;">
                    <br>
                    <form method="get" action="/Admin/editClient.php">
                        <input type="hidden" name="ClientID" value="<?php
                            echo($clientID);
                        ?>">
                        <input class="fullW" type="submit" value="Edit Client ID <?php
                            echo($clientID);
                        ?>" style="max-width:100%;">
                    </form>

                    <form method="get" action="/Admin/deleteClient.php">
                        <input type="hidden" name="ClientID" value="<?php
                            echo($clientID);
                        ?>">
                        <input class="fullW" type="submit" value="*** Delete Client ID <?php
                            echo($clientID);
                        ?> ***" style="max-width:100%;">
                    </form>

                    <?php if ($blockCount > 0): ?>
                        <form method="get" action="/Admin/manageBlock.php">
                            <input type="hidden" name="SearchKey" value="<?php
                                echo($clientID);
                            ?>">
                            <input type="hidden" name="SearchOption" value="4">
                            <input class="fullW" type="submit" value="View Related Blocks">
                        </form>
                    <?php endif; ?>

                    <?php if ($totalPurchaseCount > 0): ?>
                        <form method="get" action="/Admin/managePurchase.php">
                            <input type="hidden" name="SearchKey" value="<?php
                                echo($clientID);
                            ?>">
                            <input type="hidden" name="SearchOption" value="6">
                            <input class="fullW" type="submit" value="View Related Purchases">
                        </form>
                    <?php endif; ?>
                    
                    <form method="get" action="/Admin/manageClient.php">
                        <input class="fullW" type="submit" value="Back to View All Clients">
                    </form>
                </div>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
