<?php
    // Client Browse Block Page.
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dbConnection.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/loginAuthenticate.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dataManagement.php");

    $tempLoginCheck = checkLogin($conn);
    // Not logged in as Client.
    if ($tempLoginCheck != 3) {
        header("Location: /index.php");
        exit;
    }
    
    function displayOnSaleBlocks($conn) {
        $result = getBlockLatestClient($conn);
        
        if(sizeof($result) <= 0) {
            echo("<span>* No block is found! *</span>");
            return;
        }

        $counter = 0;

        foreach($result as $row) {
            if (
                !(empty($row["ClientID"]) ||
                $row["ApprovalStatus"] != 1) ||
                empty($row["SaleID"]) ||
                $row["SaleID"] < 1
            ) {
                continue;
            }

            $temp = getAllCompany($conn, $row["CompanyID"]);
            $companyName = $temp[0]["RealName"];

            $temp = getAllOrchard($conn, $row["CompanyID"], $row["OrchardID"]);
            $location = $temp[0]["Latitude"] . ", " . $temp[0]["Longitude"];

            $numberOfTrees = getTreeCount($conn, $row["CompanyID"], $row["OrchardID"], $row["BlockID"]);

            $saleID = $row["SaleID"];
            $temp = getAllOnSale($conn, 0, 0, 0, $saleID, 0);
            $seller = ($temp[0]["SellerID"] == 0) ? "(None)" : $temp[0]["SellerID"];
            $salePrice = $temp[0]["SalePrice"];
            $saleDate = $temp[0]["SaleDate"];

            echo("
                <tr>
                    <td>" . ++$counter . "</td>
                    <td>" . $row["BlockID"] . "</td>
                    <td>" . $companyName . "</td>
                    <td>" . $location . "</td>
                    <td>" . $numberOfTrees . "</td>
                    <td>" . $seller . "</td>
                    <td>" . $saleID . "</td>
                    <td>" . $salePrice . "</td>
                    <td>" . $saleDate . "</td>
                    <td>
                        <form action=\"/Client/view_the_block.php\" method=\"get\">
                            <table>
                                <input type=\"hidden\" name=\"BlockID\" id=\"BlockID\" value=\"" . $row["BlockID"] . "\">
                                <input type=\"submit\" name=\"view_block\" value=\"Buy\">
                            </table>
                        </form>
                    </td>
                </tr>"
            );
        }

        if($counter == 0) {
            echo("<span>* No block on sale! *</span>");
            return;
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Client: Browse Sale Page</title>
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
                <h1>Client: Browse Sale Page</h1>
            </div>
        </header>

        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Client/navigationBar.php"); ?>

        <main>
            <div class="w3-container w3-theme-d4 w3-animate-opacity">
                <h2 class="w3-center">On Sale Blocks:</h2>

                <div class="w3-container w3-center" style="align-content:center;">
                    <table class=" w3-center w3-table-all w3-centered w3-hoverable" style="width:100%">
                        <tr>
                            <th>No.</th>
                            <th>Block ID</th>
                            <th>Company</th>
                            <th>Orchard Location</th>
                            <th>Total Trees</th>
                            <th>Seller ID</th>
                            <th>Sale ID</th>
                            <th>Sale Price</th>
                            <th>Sale Date</th>
                            <th>Action</th>
                        </tr>

                        <?php displayOnSaleBlocks($conn); ?>
                    </table>
                </div>
        </main>

    </body>
</html>