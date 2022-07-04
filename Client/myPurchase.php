<?php
    // Company Manage Orchard Page.
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dbConnection.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/loginAuthenticate.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dataManagement.php");

    $tempLoginCheck = checkLogin($conn);
    // Not logged in as Client.
    if ($tempLoginCheck != 3) {
        header("Location: /index.php");
        exit;
    }

    function displayPurchaseHistory($conn, $userid) {
        $result = getAllPurchaseRequest($conn, -1, 0, 0, 0, 0, 0, $userid, 0, true);

        if(sizeof($result) <= 0) {
            echo("* No Purchase History! *");
            return;
        }

        $counter = 0;
        foreach($result as $row) {

            $temp = getAllCompany($conn, $row["CompanyID"]);
            $companyName = $temp[0]["RealName"];
            
            $temp = getAllOrchard($conn, $row["CompanyID"], $row["OrchardID"]);
            $location = $temp[0]["Latitude"] . ", " . $temp[0]["Longitude"];

            $numberOfTrees = getTreeCount($conn, $row["CompanyID"], $row["OrchardID"], $row["BlockID"]);

            $sellerID = ($row["SellerID"] == 0) ? "(Open Block)" : $row["SellerID"];

            $approval = getApprovalStatusStr($row["ApprovalStatus"]);

            echo("
                <tr>
                    <td>" . ++$counter . "</td>
                    <td>" . $row["BlockID"] . "</td>
                    <td>" . $companyName . "</td>
                    <td>" . $location . "</td>
                    <td>" . $numberOfTrees . "</td>
                    <td>" . $sellerID . "</td>
                    <td>" . $row["SalePrice"] . "</td>
                    <td>" . $row["RequestPrice"] . "</td>
                    <td>" . $row["SaleDate"] . "</td>
                    <td>" . $row["RequestDate"] . "</td>
                    <td>" . $approval . "</td>
                </tr>"
            );
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Client: My Purchase</title>
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
                <h1>Client: My Purchase</h1>
            </div>
        </header>

        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Client/navigationBar.php"); ?>

        <main>
            <div class="w3-container w3-theme-d4 w3-animate-opacity">
                <h2 class="w3-center">Purchase History:</h2>

                <div class="w3-container w3-center" style="align-content:center;">
                    <table class=" w3-center w3-table-all w3-centered w3-hoverable" style="width:100%">
                        <tr>
                            <th>No.</th>
                            <th>Block ID</th>
                            <th>Company</th>
                            <th>Orchard Location</th>
                            <th>Total Trees</th>
                            <th>Seller ID</th>
                            <th>Sale Price</th>
                            <th>Request Price</th>
                            <th>Sale Date</th>
                            <th>Request Date</th>
                            <th>Approval Status</th>
                        </tr>

                        <?php displayPurchaseHistory($conn, $_SESSION["UserID"]); ?>
                    </table>
                </div>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>