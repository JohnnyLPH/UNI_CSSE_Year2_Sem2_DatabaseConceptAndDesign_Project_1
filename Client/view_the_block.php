<?php
    // Admin Manage Orchard Page.
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dbConnection.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/loginAuthenticate.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dataManagement.php");

    $tempLoginCheck = checkLogin($conn);
    // Not logged in as Client.
    if ($tempLoginCheck != 3) {
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
        count($allBlock = getBlockLatestClient($conn, 0, 0, $queryString["BlockID"])) < 1
    ) {
        header("Location: /Client/view_block.php");
        exit;
    }

    $blockID = $queryString["BlockID"];
    $result = $allBlock[0];

    $clientID = $result["ClientID"];
    $clientName = $result["RealName"];

    $allPurchaseRequest = getAllPurchaseRequest($conn, -1, 0, 0, $queryString["BlockID"]);
    $allOnSale = getAllOnSale($conn, 0, 0, $queryString["BlockID"]);

    $treeCount = getTreeCount(
        $conn, $result["CompanyID"], $result["OrchardID"], $result["BlockID"]
    );

    $totalPurchaseCount = count($allPurchaseRequest);
    $successPurchaseCount = getPurchaseRequestCount(
        $conn, 1, $result["CompanyID"], $result["OrchardID"], $result["BlockID"]
    );

    $totalOnSaleCount = count($allOnSale);

    $sellerID = $salePrice = $approvalStatus = "";
    $approvalCheck = false;

    if($queryString["view_block"] == "Buy") {
        $temp = getAllOnSale($conn, 0, 0, 0, $result["SaleID"], 0);
        $sellerID = ($temp[0]["SellerID"] == 0) ? "(None)" : $temp[0]["SellerID"];
        $salePrice = $temp[0]["SalePrice"];

        if($result["ApprovalStatus"] < 1 && $result["ClientID"] > 0) {
            $approvalStatus = ($_SESSION["UserID"] == $result["ClientID"]) ? "<i>Pending Approval</i>" : "<i>Block On Hold</i>";
            $approvalCheck = false;
        } else {
            $approvalStatus = "<i>Click on \"Purchase Block\" to purchase this block!</i>";
            $approvalCheck = true;
        }
    }

    function displayTrees($conn, $blockID) {
        $result = array();
        $result = getAllTree($conn, 0, 0, $blockID, 0);

        if(sizeof($result) <= 0) {
            return;
        }

        $counter = 1;
        foreach($result as $row) {
            $location = $row["Latitude"] . ", " . $row["Longitude"];
            echo("
                <tr>
                    <td>" . $counter . "</td>
                    <td>" . $row["TreeID"] . "</td>
                    <td>" . $row["SpeciesName"] . "</td>
                    <td>" . $location . "</td>
                    <td>" . $row["PlantDate"] . "</td>
                    <td>
                        <form method=\"get\" action=\"/Client/view_tree.php\">
                            <input type=\"hidden\" name=\"TreeID\" value=\"" .
                                $row["TreeID"] . "\">
                            <input type=\"submit\" value=\"View\">
                        </form>
                    </td>
                </tr>"
            );

            $counter++;
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Client: View Block Page</title>
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
                <h1>Client: View Block Page</h1>
            </div>
        </header>

        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Client/navigationBar.php"); ?>

        <main>
            <div class="w3-row">
                <div class="w3-container w3-quarter w3-sidebar w3-bar-block w3-theme-d5" style="width:25%;">
                    <br>
                    
                    <?php if($_GET["view_block"] == "View"): ?>
                        <form method="get" action="/Client/sell_block.php">
                            <input type="hidden" name="blockID" value="<?php echo($result["BlockID"]); ?>">
                            <input class="fullW" type="submit" value="Let Block Go">
                        </form> 

                    <?php else: ?>
                        <form method="get" action="/Client/buy_block.php">
                            <input type="hidden" name="blockID" value="<?php echo($result["BlockID"]); ?>">
                            <?php if($approvalCheck): ?>
                                <input class="fullW" type="submit" value="Purchase Block">
                            <?php else: ?>
                                <input class="fullW" type="submit" value="Purchase Block" style="background-color:gray" disabled>
                            <?php endif; ?>
                        </form>
                    <?php endif; ?>

                    <form method="get" action="/Client/view_block.php">
                        <input class="fullW" type="submit" value="Back to View All Blocks">
                    </form>

                </div>
                <div class="w3-container w3-threequarter w3-theme-d4 w3-animate-left" style="margin-left:25%; padding-bottom:2%;">
                    <h2>Block ID <?php
                        echo($blockID);
                    ?>:</h2>

                    <table class=" w3-center w3-table-all w3-hoverable" style="width:100%">
                        <tr>
                            <td>Company</td>
                            <td><?php
                                $name = getAllCompany($conn, $result["CompanyID"]);
                                echo($name[0]["RealName"]);
                            ?></td>
                        </tr>

                        <tr>
                            <td>Orchard ID</td>
                            <td><?php
                                echo($result["OrchardID"]);
                            ?></td>
                        </tr>

                        <tr>
                            <td>Location</td>
                            <td>
                                <?php  
                                    $coordinates = getAllOrchard($conn, 0, $result["OrchardID"]);
                                    $location = $coordinates[0]["Latitude"] . ", " . $coordinates[0]["Longitude"];
                                    echo($location);
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td>Total Trees</td>
                            <td><?php
                                echo($treeCount);
                            ?></td>
                        </tr>

                        <?php 
                            if($_GET["view_block"] == "Buy") {
                                echo ("
                                    <tr>
                                        <td>Seller ID</td>
                                        <td>
                                            " . $sellerID . "
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Sale Price</td>
                                        <td>
                                            " . $salePrice . "
                                        </td>
                                    </tr>
                                ");
                            }
                        ?>

                        <tr>
                            <td>Total Sale</td>
                            <td><?php
                                echo($totalOnSaleCount);
                            ?></td>
                        </tr>

                        <?php 
                            if($_GET["view_block"] == "Buy") {
                                echo ("
                                    <tr>
                                        <td>Approval Status</td>
                                        <td>
                                            " . $approvalStatus . "
                                        </td>
                                    </tr>
                                ");
                            }
                        ?>
                    </table>

                    <h3>Trees in the Block:</h3>
                    <?php if ($treeCount > 0): ?>
                        <table class=" w3-center w3-table-all w3-centered w3-hoverable" style="width:100%">
                        <tr>
                            <th>No.</th>
                            <th>Tree ID</th>
                            <th>Species Name</th>
                            <th>Location</th>
                            <th>Plant Date</th>
                            <th>Action</th>
                        </tr>

                            <?php displayTrees($conn, $blockID); ?>
                        </table>
                    <?php else: ?>
                        <span>* No Trees Planted for Block ID <?php
                            echo($blockID);
                        ?>! *</span>
                    <?php endif; ?>

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