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
        header("Location: /Client/view_block.php");
        exit;
    }

    $blockID = $queryString["blockID"];
    $result = $allBlock[0];

    $clientID = $result["ClientID"];
    $clientName = $result["RealName"];

    $passEditing = $editMsg = "";

    if(isset($_GET["message"])) {
        $passEditing = ($_GET["message"] == "success");

        $editMsg = $passEditing ? "* This block is on sale! *" : "* Fail to put this block on sale! *";
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
                    <h1>Let Block Go: Block ID <?php
                        echo(" " . $blockID);
                    ?></h1>
                </div>
                <div id="formContentW2">
                    <br>
                    <img class="fadeIn first" src="/img/defaults/purchaseIcon.png" id="icon45" alt="PurchaseRequest Icon" />

                    <form method="get" action="/Client/sellAuth.php">
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
                                <td>
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
                                        <label for="block_id">
                                            Block ID:
                                        </label><br>
                                        <input id="block_id" name="block_id" type="number" value="<?php
                                            echo($blockID);
                                        ?>" placeholder="Block ID" readonly>
                                    </div>
                                </td>
                            </tr>

                            <tr class="fadeIn fourth">
                                <!-- Seller ID, read only. -->
                                <td>
                                    <div>
                                        <label for="sellerID">
                                            Client ID:
                                        </label><br>
                                        <input id="sellerID" name="sellerID" type="number" value="<?php
                                            echo($clientID);
                                        ?>" placeholder="Client ID" readonly>
                                    </div>
                                </td>
                            </tr>

                            <tr class="fadeIn fifth">
                                <!-- Selling Price -->
                                <td colspan="2">
                                    <div>
                                        <label for="sale_price">Sale Price</label><br>
                                        <input type="number" name="sale_price" min="5000" step="1000" placeholder="Min 5000">
                                    </div>
                                </td>
                            </tr>

                            <tr class="fadeIn sixth">
                                <td colspan="2">
                                    <div>
                                        <br>
                                        <input type="submit" value="Sell">
                                    </div>
                                </td>
                            </tr>

                            <tr class="fadeIn sixth">
                                <td colspan="2">
                                    <span class="error-message">
                                        * WARNING: Once confirmed, You can't undo the process! *
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </form>
                    <br>
                    <div id="formFooter">
                        <h2><a class="underlineHover" href="/Client/view_block.php">Back to View Block</a><h2><br>
                    </div>
                </div>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>