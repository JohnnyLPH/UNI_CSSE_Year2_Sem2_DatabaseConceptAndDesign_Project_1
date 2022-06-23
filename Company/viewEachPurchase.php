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

    $allPurchaseRequest = NULL;
    // PurchaseRequest is not available for viewing.
    if (
        !isset($queryString["RequestID"]) ||
        $queryString["RequestID"] < 1 ||
        count($allPurchaseRequest = getAllPurchaseRequest(
            $conn, -1, $_SESSION["UserID"], 0, 0, $queryString["RequestID"]
        )) < 1
    ) {
        header("Location: /Company/managePurchase.php");
        exit;
    }

    $requestID = $queryString["RequestID"];
    $result = $allPurchaseRequest[0];
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Company: Manage Purchase Page</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">
        
        <link rel="stylesheet" href="/css/main.css">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <!--<link rel="shortcut icon" href="/favicon.ico">-->
        <link rel="shortcut icon" href="https://icon-library.com/images/tree-icon/tree-icon-23.jpg">
    </head>

    <body>
        <header>
            <h1>Company: Manage Purchase Page</h1>
        </header>

        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Company/navigationBar.php"); ?>

        <main>
            <div class="w3-container w3-theme-d4 w3-animate-opacity">
                <br>
                <h2 class="w3-center">Purchase Request ID <?php
                    echo($requestID);
                ?>:</h2>
                <div class="w3-container w3-center" style="align-content:center;">
                    <table class=" w3-center w3-table-all w3-hoverable" style="width:100%">
                        <tr>
                            <td>Request ID</td>
                            <td><?php
                                echo($result["RequestID"]);
                            ?></td>
                        </tr>

                        <tr>
                            <td>Sale ID</td>
                            <td><?php
                                echo($result["SaleID"]);
                            ?></td>
                        </tr>

                        <tr>
                            <td>Client ID</td>
                            <td><?php
                                echo($result["ClientID"]);
                            ?></td>
                        </tr>

                        <tr>
                            <td>Request Date</td>
                            <td><?php
                                echo($result["RequestDate"]);
                            ?></td>
                        </tr>

                        <tr>
                            <td>Request Price</td>
                            <td><?php
                                echo($result["RequestPrice"]);
                            ?></td>
                        </tr>

                        <tr>
                            <td>Admin ID</td>
                            <td><?php
                                echo($result["AdminID"]);
                            ?></td>
                        </tr>

                        <tr>
                            <td>Approval Status</td>
                            <td><?php
                                echo(getApprovalStatusStr($result["ApprovalStatus"]));
                            ?></td>
                        </tr>
                    </table>
                </div>

                <div class="wrapper w3-theme-d4 ">
                    <form method="get" action="/Company/viewEachBlock.php">
                        <input type="hidden" name="BlockID" value="<?php
                            echo($result["BlockID"]);
                        ?>">
                        <input type="submit" value="View Related Block">
                    </form>

                    <form method="get" action="/Company/managePurchase.php">
                        <input type="submit" value="Back to View All Purchases">
                    </form>
                </div>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
