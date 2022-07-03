<?php
    // Admin Manage Block Page.
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dbConnection.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/loginAuthenticate.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/inputValidation.php");
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

    $allBlock = NULL;
    // Block is not available for editing.
    if (
        !isset($queryString["BlockID"]) ||
        !is_numeric($queryString["BlockID"]) ||
        $queryString["BlockID"] < 1 ||
        count($allBlock = getAllBlock($conn, 0, 0, $queryString["BlockID"])) < 1 ||
        getOnSaleCount($conn, 0, 0, $queryString["BlockID"]) > 0
    ) {
        header("Location: /Admin/manageBlock.php");
        exit;
    }

    $blockID = $queryString["BlockID"];
    $result = $allBlock[0];

    $tempSalePrice = "";
    $editMsg = "";
    $passEditing = false;

    // Edit attempt.
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $tempSalePrice = (isset($_POST["SalePrice"])) ? cleanInput($_POST["SalePrice"]): "";

        if (
            empty($tempSalePrice)
        ) {
            $editMsg = "* Fill in Initial Sale Price! *";
            $passEditing = false;
        }
        else {
            // Set to true at first.
            $passEditing = true;

            // Check SalePrice.
            if (!is_numeric($tempSalePrice) || $tempSalePrice < 5000) {
                $editMsg = "* Valid Sale Price (>= RM 5000)! *";
                $passEditing = false;
            }

            // Insert to DB.
            if ($passEditing) {
                // Insert to OnSale table.
                $query = "INSERT INTO `OnSale`(`BlockID`, `SalePrice`)";
                $query .= " VALUES ('$blockID', '$tempSalePrice');";

                $rs = $conn->query($query);
                if (!$rs) {
                    $editMsg = "* Fail to insert to OnSale table! *";
                    $passEditing = false;
                }

                // Check if the data is successfully updated.
                if ($passEditing) {
                    $editMsg = "* Block is successfully updated! *";
                }
            }
        }
    }

    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Admin: Manage Block Page</title>
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
                <h4 style="font-size: 36px">Admin: Manage Block Page</h4>
            </div>
        </header>

        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Admin/navigationBar.php"); ?>

        <main>
            <div class="wrapper fadeInDown">
                <div id="formHeader">
                    <h1>Edit Block ID <?php
                        echo($blockID);
                    ?>:</h1>
                </div>
                <div id="formContentW2">
                    <img class="fadeIn first" src="/img/defaults/blockIcon.jpg" id="icon" alt="Block Icon" />

                    <form method="post" action="/Admin/editBlock.php?BlockID=<?php
                        echo($blockID);
                    ?>">
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
                                <!-- SalePrice -->
                                <td colspan="2">
                                    <div>
                                        <label for="SalePrice">
                                            Sale Price (RM):
                                        </label><br>
                                        <input id="SalePrice" type="number" step="0.01" name="SalePrice" value="<?php
                                            echo($tempSalePrice);
                                        ?>" placeholder="Initial Price" min="5000" required>
                                    </div>
                                </td>
                            </tr>

                            <tr class="fadeIn third">
                                <td colspan="2">
                                    <div>
                                        <br>
                                        <input type="submit" value="Save Editing"<?php
                                            if ($passEditing) {
                                                echo(" disabled");
                                            }
                                        ?>>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </form>
                    <br>
                    <div id="formFooter">
                        <h2><a class="underlineHover" href="/Admin/viewEachBlock.php?BlockID=<?php
                            echo($blockID);
                        ?>">Back to View Block</a><h2><br>
                    </div>
                </div>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
