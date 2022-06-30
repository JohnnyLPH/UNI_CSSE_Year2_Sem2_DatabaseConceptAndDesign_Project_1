<?php
    // Admin Manage Admin Page.
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

    $allAdmin = NULL;
    // Admin is not available for viewing.
    if (
        !isset($queryString["AdminID"]) ||
        !is_numeric($queryString["AdminID"]) ||
        $queryString["AdminID"] < 1 ||
        count($allAdmin = getAllAdmin($conn, $queryString["AdminID"])) < 1
    ) {
        header("Location: /Admin/manageAdmin.php");
        exit;
    }

    $adminID = $queryString["AdminID"];
    $result = $allAdmin[0];

    $allPurchaseRequest = getAllPurchaseRequest($conn, -1, 0, 0, 0, 0, 0, 0, $adminID);
    
    // $totalPurchaseCount = getPurchaseRequestCount($conn, -1, 0, 0, 0, 0, 0, $result["UserID"]);
    $totalPurchaseCount = count($allPurchaseRequest);
    $approvedPurchaseCount = getPurchaseRequestCount($conn, 1, 0, 0, 0, 0, 0, $result["UserID"]);
    $rejectedPurchaseCount = getPurchaseRequestCount($conn, 2, 0, 0, 0, 0, 0, $result["UserID"]);
    $adminCount = getAdminCount($conn);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Admin: Manage Admin Page</title>
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
                <h1>Admin: Manage Admin Page</h1>
            </div>
        </header>

        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Admin/navigationBar.php"); ?>

        <main>
            <div class="w3-row">
                <div class="w3-container w3-threequarter w3-theme-d4 w3-animate-left" style="margin-left:25%; padding-bottom:2%; padding-top:2%;">

                    <div class="w3-container">
                        <h2>Admin ID <?php
                            echo($adminID);
                        ?>:</h2> 

                        <table class="w3-table-all">
                            <tr>
                                <td>Admin ID</td>
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
                                <td>Admin Name</td>
                                <td><?php
                                    echo($result["RealName"]);
                                ?></td>
                            </tr>

                            <tr>
                                <td>Total Processed Request</td>
                                <td><?php
                                    echo($totalPurchaseCount);
                                ?></td>
                            </tr>

                            <tr>
                                <td>Total Approved</td>
                                <td><?php
                                    echo($approvedPurchaseCount);
                                ?></td>
                            </tr>

                            <tr>
                                <td>Total Rejected</td>
                                <td><?php
                                    echo($rejectedPurchaseCount);
                                ?></td>
                            </tr>
                        </table>
                            
                        <h3>Processed Purchase Request:</h3>
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
                            <span>* No Processed Purchase Request for Admin ID <?php
                                echo($adminID);
                            ?>! *</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="w3-container w3-quarter w3-sidebar w3-bar-block w3-theme-d5" style="width:25%;">
                    <br>
                    <form method="get" action="/Admin/editAdmin.php">
                        <input type="hidden" name="AdminID" value="<?php
                            echo($adminID);
                        ?>">
                        <input class="fullW" type="submit" value="Edit Admin ID <?php
                            echo($adminID);
                        ?>" style="max-width:100%;">
                    </form>

                    <?php if ($adminCount > 0): ?>
                        <form method="get" action="/Admin/deleteAdmin.php">
                            <input type="hidden" name="AdminID" value="<?php
                                echo($adminID);
                            ?>">
                            <input class="fullW" type="submit" value="*** Delete Admin ID <?php
                                echo($adminID);
                            ?> ***" style="max-width:100%;">
                        </form>
                    <?php endif; ?>

                    <form method="get" action="/Admin/manageAdmin.php">
                        <input class="fullW" type="submit" value="Back to View All Admins">
                    </form>
                </div>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
