<?php
    // Admin Manage PurchaseRequest Page.
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dbConnection.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/loginAuthenticate.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/inputValidation.php");
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
    
    if (
        !isset($queryString["blockID"]) ||
        !is_numeric($queryString["blockID"]) ||
        count($allBlock = getBlockLatestClient($conn, 0, 0, $queryString["blockID"])) < 1
    ) {
        header("Location: /Client/browsePage.php");
        exit;
    }

    $blockID = $queryString["blockID"];
    $result = $allBlock[0];

    $saleResult = getAllOnSale($conn, 0, 0, 0, $result["SaleID"], 0);
    $clientID = $saleResult[0]["SellerID"];
    $salePrice = $saleResult[0]["SalePrice"];
    $saleDate = $saleResult[0]["SaleDate"];

    $passEditing = $editMsg = "";

    if(isset($_GET["message"])) {
        $passEditing = ($_GET["message"] == "success");

        $editMsg = $passEditing ? "* Please wait approval from admin! *" : "* Fail to request this block! *";
    }

    // $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Client: View Block Page</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">

        <link rel="stylesheet" href="/css/main.css">
        <link rel="stylesheet" href="/css/formFont.css">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <link rel="stylesheet" href="https://www.w3schools.com/lib/w3-colors-vivid.css">
        <!--<link rel="shortcut icon" href="/favicon.ico">-->
        <link rel="shortcut icon" href="https://icon-library.com/images/tree-icon/tree-icon-23.jpg">
    </head>

    <body>
        <header>
            <div class="maintheme w3-container">
                <h4 style="font-size: 36px">Client: View Block Page</h4>
            </div>
        </header>

        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Client/navigationBar.php"); ?>

        <main>
            <div class="wrapper fadeInDown">
                <div id="formHeader">
                    <h1>Buy This Block: Block ID <?php
                        echo(" " . $blockID);
                    ?></h1>
                </div>
                <div id="formContentW2">
                    <br>
                    <img class="fadeIn first" src="/img/defaults/purchaseIcon.png" id="icon45" alt="PurchaseRequest Icon" />

                    <form method="get" action="/Client/purchaseAuth.php">
                        <input type="hidden" name="sale_id" id="sale_id" value="<?php echo($result["SaleID"]) ?>">

                        <table>
                            <tr>
                                <td colspan="2">
                                    <span class="<?php
                                        echo(($passEditing) ? "success": "error");
                                    ?>-message"><?php
                                        echo($editMsg);
                                    ?></span>
                                </td>
                            </tr>

                            <tr class="fadeIn second">
                                <!-- Company Name, read only. -->
                                <td colspan="2">
                                    <div>
                                        <label for="company">
                                            Company:
                                        </label><br>
                                        <input id="company" name="company" type="text" value="<?php
                                            $name = getAllCompany($conn, $result["CompanyID"]);
                                            echo($name[0]["RealName"]);
                                        ?>" placeholder="Company Name" readonly>
                                    </div>
                                </td>
                            </tr>

                            <tr class="fadeIn third">
                                <!-- Block ID, read only. -->
                                <td>
                                    <div>
                                        <label for="BlockID">
                                            Block ID:
                                        </label><br>
                                        <input id="BlockID" name="BlockID" type="number" value="<?php
                                            echo($blockID);
                                        ?>" placeholder="Block ID" readonly>
                                    </div>
                                </td>

                                <!-- Seller ID, read only. -->
                                <td>
                                    <div>
                                        <label for="sellerID">
                                            Seller ID:
                                        </label><br>
                                        <input id="sellerID" name="sellerID" type="number" value="<?php
                                            echo($clientID);
                                        ?>" placeholder="Seller ID" readonly>
                                    </div>
                                </td>
                            </tr>

                            <tr class="fadeIn fourth">
                                <!-- Sale Price, read only. -->
                                <td>
                                    <div>
                                        <label for="request_block_price">Sale Price</label><br>
                                        <input type="number" name="request_block_price" value="<?php
                                            echo($salePrice);
                                        ?>" placeholder="Sale Price" readonly>
                                    </div>
                                </td>

                                <!-- Sale Date readonly -->
                                <td>
                                    <div>
                                        <label for="sale_date">Sale Date</label><br>
                                        <input type="text" name="sale_date" value="<?php
                                            echo($saleDate);
                                        ?>" placeholder="Sale Date" readonly>
                                    </div>
                                </td>
                            </tr>

                            <tr class="fadeIn fifth">
                                <!-- Request Price -->
                                <td colspan="2">
                                    <div>
                                        <label for="request_price">Request Price</label><br>
                                        <input type="number" name="request_price" min="<?php echo($salePrice); ?>" step="1000" placeholder="Min <?php echo($salePrice); ?>">
                                    </div>
                                </td>
                            </tr>

                            <tr class="fadeIn sixth">
                                <td colspan="2">
                                    <div>
                                        <br>
                                        <input type="submit" value="Buy">
                                    </div>
                                </td>
                            </tr>

                            <tr class="fadeIn sixth">
                                <td colspan="2">
                                    <span class="error-message">
                                        * WARNING: Once confirmed, the status cannot be changed again! *
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </form>
                    <br>
                    <div id="formFooter">
                        <h2><a class="underlineHover" href="/Client/browsePage.php">Back to Browse Block</a><h2><br>
                    </div>
                </div>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>