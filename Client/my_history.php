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
    
    function displayOnSaleBlocks($conn, $sellerID) {
        $result = getAllOnSale($conn, 0, 0, 0, 0, $sellerID);

        if(sizeof($result) <= 0) {
            echo("<span>* No block is found! *</span>");
            return;
        }

        $counter = 0;

        foreach($result as $row) {

            $tempBlock = getAllBlock($conn, 0, 0, $row["BlockID"]);
            $tempOrchard = getAllOrchard($conn, 0, $tempBlock[0]["OrchardID"]);
            $tempCompany = getAllCompany($conn, $tempOrchard[0]["CompanyID"]);

            $companyName = $tempCompany[0]["RealName"];
            $location = $tempOrchard[0]["Latitude"] . ", " . $tempOrchard[0]["Longitude"];

            echo("
                <tr>
                    <td>" . ++$counter . "</td>
                    <td>" . $row["BlockID"] . "</td>
                    <td>" . $companyName . "</td>
                    <td>" . $location . "</td>
                    <td>" . $row["SalePrice"] . "</td>
                    <td>" . $row["SaleDate"] . "</td>
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
        <title>Client: Sale History Page</title>
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
                <h1>Client: Sale History Page</h1>
            </div>
        </header>

        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Client/navigationBar.php"); ?>

        <main>
            <div class="w3-container w3-theme-d4 w3-animate-opacity">
                <h2 class="w3-center">Sale History:</h2>

                <div class="w3-container w3-center" style="align-content:center;">
                    <table class=" w3-center w3-table-all w3-centered w3-hoverable" style="width:100%">
                        <tr>
                            <th>No.</th>
                            <th>Block ID</th>
                            <th>Company</th>
                            <th>Orchard Location</th>
                            <th>Sale Price</th>
                            <th>Sale Date</th>
                        </tr>

                        <?php displayOnSaleBlocks($conn, $_SESSION["UserID"]); ?>
                    </table>
                </div>
        </main>

    </body>
</html>