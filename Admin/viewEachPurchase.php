<?php
    // Admin Manage PurchaseRequest Page.
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

    $allPurchaseRequest = NULL;
    // PurchaseRequest is not available for viewing.
    if (
        !isset($queryString["RequestID"]) ||
        !is_numeric($queryString["RequestID"]) ||
        $queryString["RequestID"] < 1 ||
        count($allPurchaseRequest = getAllPurchaseRequest(
            $conn, -1, 0, 0, 0, $queryString["RequestID"]
        )) < 1
    ) {
        header("Location: /Admin/managePurchase.php");
        exit;
    }

    $requestID = $queryString["RequestID"];
    $result = $allPurchaseRequest[0];
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
            <div class="w3-row">
                <div class="w3-container w3-quarter w3-sidebar w3-bar-block w3-theme-d5" style="width:25%;">
                    <br>
                    <?php if ($result["ApprovalStatus"] == 0): ?>
                        <form method="get" action="/Admin/editPurchase.php">
                            <input type="hidden" name="RequestID" value="<?php
                                echo($requestID);
                            ?>">
                            <input class="fullW" type="submit" value="Update Approval Status">
                        </form>
                    <?php endif; ?>

                    <form method="get" action="/Admin/viewEachCompany.php">
                        <input type="hidden" name="CompanyID" value="<?php
                            echo($result["CompanyID"]);
                        ?>">
                        <input class="fullW" type="submit" value="View Related Company">
                    </form>

                    <form method="get" action="/Admin/viewEachOrchard.php">
                        <input type="hidden" name="OrchardID" value="<?php
                            echo($result["OrchardID"]);
                        ?>">
                        <input class="fullW" type="submit" value="View Related Orchard">
                    </form>

                    <form method="get" action="/Admin/viewEachBlock.php">
                        <input type="hidden" name="BlockID" value="<?php
                            echo($result["BlockID"]);
                        ?>">
                        <input class="fullW" type="submit" value="View Related Block">
                    </form>

                    <form method="get" action="/Admin/managePurchase.php">
                        <input class="fullW" type="submit" value="Back to View All Purchases">
                    </form>
                </div>

                <div class="w3-container w3-threequarter w3-theme-d4 w3-animate-left" style="margin-left:25%; padding-bottom:2%;">
                    <h2>Purchase Request ID <?php
                        echo($requestID);
                    ?>:</h2>

                    <table class=" w3-center w3-table-all" style="width:100%">
                        <tr>
                            <td>Request ID</td>
                            <td><?php
                                echo($result["RequestID"]);
                            ?></td>
                        </tr>

                        <tr>
                            <td>Client ID</td>
                            <td><?php
                                echo($result["ClientID"]);
                            ?></td>
                        </tr>

                        <tr>
                            <td>Client Name</td>
                            <td><?php
                                echo($result["RealName"]);
                            ?></td>
                        </tr>

                        <tr>
                            <td>Request Date</td>
                            <td><?php
                                echo($result["RequestDate"]);
                            ?></td>
                        </tr>

                        <tr>
                            <td>Request Price (RM)</td>
                            <td><?php
                                echo($result["RequestPrice"]);
                            ?></td>
                        </tr>

                        <tr>
                            <td>Admin ID</td>
                            <td><?php
                                echo((empty($result["AdminID"])) ? "None": $result["AdminID"]);
                            ?></td>
                        </tr>

                        <tr>
                            <td>Approval Status</td>
                            <td><?php
                                echo(getApprovalStatusStr($result["ApprovalStatus"]));
                            ?></td>
                        </tr>
                    </table>

                    <h3>Target Sale:</h3>

                    <table class=" w3-center w3-table-all" style="width:100%">
                        <tr>
                            <td>Sale ID</td>
                            <td><?php
                                echo($result["SaleID"]);
                            ?></td>
                        </tr>

                        <tr>
                            <td>Client ID (Seller)</td>
                            <td><?php
                                echo(empty($result["SellerID"]) ? "None": $result["SellerID"]);
                            ?></td>
                        </tr>

                        <tr>
                            <td>Sale Date</td>
                            <td><?php
                                echo($result["SaleDate"]);
                            ?></td>
                        </tr>

                        <tr>
                            <td>Sale Price (RM)</td>
                            <td><?php
                                echo($result["SalePrice"]);
                            ?></td>
                        </tr>

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
                            <td>Company ID</td>
                            <td><?php
                                echo($result["CompanyID"]);
                            ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
