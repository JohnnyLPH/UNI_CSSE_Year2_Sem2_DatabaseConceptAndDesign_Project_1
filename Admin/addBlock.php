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

    $tempSalePrice = $tempOrchardID = "";
    $addMsg = "";
    $passAdding = true;

    $allOrchard = getAllOrchard($conn);

    // Disable adding if no existing orchard.
    if (count($allOrchard) < 1) {
        $addMsg = "* Adding is not allowed as there is no existing orchard! *";
        $passAdding = false;
    }

    // Add attempt.
    if ($passAdding && $_SERVER["REQUEST_METHOD"] == "POST") {
        $tempSalePrice = (isset($_POST["SalePrice"])) ? cleanInput($_POST["SalePrice"]): "";
        $tempOrchardID = (isset($_POST["OrchardID"])) ? cleanInput($_POST["OrchardID"]): "";

        if (
            empty($tempSalePrice) ||
            empty($tempOrchardID)
        ) {
            $addMsg = "* Fill in ALL Fields! *";
            $passAdding = false;
        }
        else {
            // Set to true at first.
            $passAdding = true;

            // Check SalePrice.
            if (!is_numeric($tempSalePrice) || $tempSalePrice < 5000) {
                $addMsg = "* Valid Sale Price (>= RM 5000)! *";
                $passAdding = false;
            }

            // Check valid Orchard.
            if ($passAdding && count(getAllOrchard($conn, 0, $tempOrchardID)) < 1) {
                $addMsg = "* Choose an existing Orchard! *";
                $passAdding = false;
            }

            // Insert to DB.
            if ($passAdding) {
                // Insert to Block table.
                $query = "INSERT INTO `Block`(`OrchardID`)";
                $query .= " VALUES ('$tempOrchardID');";

                $rs = $conn->query($query);
                if (!$rs) {
                    $addMsg = "* Fail to insert to Block table! *";
                    $passAdding = false;
                }

                if ($passAdding) {
                    // Get the new BlockID.
                    $blockID = $conn->insert_id;
                    
                    // Insert to OnSale table.
                    $query = "INSERT INTO `OnSale`(`BlockID`, `SalePrice`)";
                    $query .= " VALUES ('$blockID', '$tempSalePrice');";
    
                    $rs = $conn->query($query);
                    if (!$rs) {
                        $addMsg = "* Fail to insert to OnSale table! *";
                        $passAdding = false;
                    }
                }

                // Check if the data is successfully inserted.
                if ($passAdding) {
                    // Reset to empty.
                    $tempSalePrice = $tempOrchardID = "";
                    $addMsg = "* Block is successfully added! *";
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
                    <h1>Add New Block:</h1>
                </div>
                <div id="formContentW2">
                    <img class="fadeIn first" src="https://i.pinimg.com/originals/07/20/ad/0720add75420ae4ad05075760c5c0723.jpg" id="icon" alt="Block Icon" />

                    <form method="post" action="/Admin/addBlock.php">
                        <table>
                            <tr>
                                <td colspan="2">
                                    <span class="<?php
                                        echo(($passAdding) ? "success": "error");
                                    ?>-message"><?php
                                        echo($addMsg);
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
                                <!-- OrchardID -->
                                <td colspan="2">
                                    <div>
                                        <label for="OrchardID">
                                            Orchard:
                                        </label><br>
                                        <select id="OrchardID" name="OrchardID">
                                            <?php foreach ($allOrchard as $result): ?>
                                                <option value="<?php
                                                    echo($result["OrchardID"]);
                                                ?>"<?php
                                                    if ($tempOrchardID == $result["OrchardID"]) {
                                                        echo(" selected");
                                                    }
                                                ?>><?php
                                                    echo(
                                                        "Orchard " . $result["OrchardID"] . " in " .
                                                        "Company ID " . $result["CompanyID"]
                                                    );
                                                    // echo($result["OrchardID"]);
                                                ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </td>
                            </tr>

                            <tr class="fadeIn forth">
                                <td colspan="2">
                                    <div>
                                        <br>
                                        <input type="submit" value="Add Block Now"<?php
                                            if (count($allOrchard) < 1) {
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
                        <h2><a class="underlineHover" href="/Admin/manageBlock.php">Back to Manage Block</a><h2><br>
                    </div>
                </div>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
