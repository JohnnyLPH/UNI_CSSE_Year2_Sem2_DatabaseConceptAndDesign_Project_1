<?php
    // Admin Manage Purchase Page.
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dbConnection.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/loginAuthenticate.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dataRetrieval.php");

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

    // Check if valid CompanyID, OrchardID, BlockID, RequestID, SaleID, ClientID is provided, set to 0 if not.
    $companyID = $orchardID = $blockID = $requestID = $saleID = $clientID = (
        !isset($queryString["SearchKey"]) ||
        !is_numeric($queryString["SearchKey"]) ||
        $queryString["SearchKey"] < 1
    ) ? 0: $queryString["SearchKey"];

    // Check if valid SearchOption is provided.
    $searchOption = (
        !isset($queryString["SearchOption"]) ||
        !is_numeric($queryString["SearchOption"]) ||
        $queryString["SearchOption"] < 1 ||
        $queryString["SearchOption"] > 6
    ) ? 1: $queryString["SearchOption"];

    // Search by CompanyID.
    if ($searchOption == 1) {
        $orchardID = $blockID = $requestID = $saleID = $clientID = 0;
    }
    // Search by OrchardID.
    elseif ($searchOption == 2) {
        $companyID = $blockID = $requestID = $saleID = $clientID = 0;
    }
    // Search by BlockID.
    elseif ($searchOption == 3) {
        $companyID = $orchardID = $requestID = $saleID = $clientID = 0;
    }
    // Search by RequestID.
    elseif ($searchOption == 4) {
        $companyID = $orchardID = $blockID = $saleID = $clientID = 0;
    }
    // Search by SaleID.
    elseif ($searchOption == 5) {
        $companyID = $orchardID = $blockID = $requestID = $clientID = 0;
    }
    // Search by ClientID.
    else {
        $companyID = $orchardID = $blockID = $requestID = $saleID = 0;
    }

    // Return all the purchase request.
    $allPurchaseRequest = getAllPurchaseRequest(
        $conn, -1, $companyID, $orchardID, $blockID, $requestID, $saleID, $clientID, true
    );
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Admin: Manage Purchase Page</title>
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
                <h1>Admin: Manage Purchase Page</h1>
            </div>
        </header>

        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Admin/navigationBar.php"); ?>

        <main>
            <div class="w3-container w3-theme-d4 w3-animate-opacity">
                <h2 class="w3-center">All Purchase Requests:</h2>

                <form id="reset-search" method="get" action="/Admin/managePurchase.php"></form>

                <form class="w3-center" method="get" action="/Admin/managePurchase.php">
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
                        elseif ($requestID > 0) {
                            echo($requestID);
                        }
                        elseif ($saleID > 0) {
                            echo($saleID);
                        }
                        elseif ($clientID > 0) {
                            echo($clientID);
                        }
                    ?>" placeholder="Enter Company/Orchard/Block/Request/Sale/Client ID" min="1" required>

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
                        ?>>RequestID</option>
                        <option value="5"<?php
                            if ($searchOption == 5) {
                                echo(" selected");
                            }
                        ?>>SaleID</option>
                        <option value="6"<?php
                            if ($searchOption == 6) {
                                echo(" selected");
                            }
                        ?>>ClientID</option>
                    </select>
                    
                    <input type="submit" value="Search">

                    <input form="reset-search" type="submit" value="Reset"<?php
                        // Disable if not searching.
                        if ($companyID + $orchardID + $blockID + $requestID + $saleID + $clientID < 1) {
                            echo(" disabled");
                        }
                    ?>>
                </form>
                
                <div class="w3-container w3-center" style="align-content:center;">
                    <?php if (count($allPurchaseRequest) > 0): ?>
                        <table class=" w3-center w3-table-all w3-centered w3-hoverable" style="width:100%">
                            <tr>
                                <th>Request ID</th>
                                <th>Block ID</th>
                                <th>Orchard ID</th>
                                <th>Company ID</th>
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
                                        echo($result["BlockID"]);
                                    ?></td>

                                    <td><?php
                                        echo($result["OrchardID"]);
                                    ?></td>

                                    <td><?php
                                        echo($result["CompanyID"]);
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
                        <br>
                    <?php else: ?>
                        <span>* <?php
                            if ($companyID + $orchardID + $blockID + $requestID + $saleID + $clientID < 1) {
                                echo("No purchase request is found!");
                            }
                            elseif ($searchOption == 1) {
                                echo(
                                    "Company ID $companyID is not associated with any purchase request!");
                            }
                            elseif ($searchOption == 2) {
                                echo(
                                    "Orchard ID $orchardID is not associated with any purchase request!");
                            }
                            elseif ($searchOption == 3) {
                                echo(
                                    "Block ID $blockID is not associated with any purchase request!");
                            }
                            elseif ($searchOption == 4) {
                                echo(
                                    "Request ID $requestID is not associated with any purchase request!");
                            }
                            elseif ($searchOption == 5) {
                                echo(
                                    "Sale ID $saleID is not associated with any purchase request!");
                            }
                            else {
                                echo(
                                    "Client ID $clientID is not associated with any purchase request!");
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
