<?php
    // Admin Manage PurchaseRequest Page.
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

    $allPurchaseRequest = NULL;
    // PurchaseRequest is not available for editing.
    if (
        !isset($queryString["RequestID"]) ||
        !is_numeric($queryString["RequestID"]) ||
        $queryString["RequestID"] < 1 ||
        count($allPurchaseRequest = getAllPurchaseRequest($conn, 0, 0, 0, 0, $queryString["RequestID"])) < 1
    ) {
        header("Location: /Admin/managePurchase.php");
        exit;
    }

    $requestID = $queryString["RequestID"];
    $result = $allPurchaseRequest[0];

    $tempApprovalStatus = $tempPass = "";
    $adminID = $_SESSION["UserID"];
    $editMsg = "";
    $passEditing = false;

    // Edit attempt.
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $tempApprovalStatus = (isset($_POST["ApprovalStatus"])) ? cleanInput($_POST["ApprovalStatus"]): "";
        $tempPass = (isset($_POST["Password"])) ? cleanInput($_POST["Password"]): "";

        if (
            empty($tempApprovalStatus) ||
            empty($tempPass)
        ) {
            $editMsg = "* Fill in Password and Choose a Status! *";
            $passEditing = false;
        }
        else {
            // Set to true at first.
            $passEditing = true;

            // Check ApprovalStatus.
            if (!is_numeric($tempApprovalStatus) || $tempApprovalStatus < 1 || $tempApprovalStatus > 2) {
                $editMsg = "* Choose either Accepted or Rejected! *";
                $passEditing = false;
            }

            // Check Password with PasswordHash in session.
            if ($passEditing && !password_verify($tempPass, $_SESSION["PasswordHash"])) {
                $editMsg = "* Invalid Password! *";
                $passEditing = false;
            }

            // Update in DB.
            if ($passEditing) {
                // Update in PurchaseRequest table.
                $query = "UPDATE `PurchaseRequest`";
                $query .= " SET `ApprovalStatus`='$tempApprovalStatus'";
                $query .= ", `AdminID`='$adminID'";
                $query .= " WHERE `PurchaseRequest`.`RequestID`='$requestID';";

                $rs = $conn->query($query);
                if (!$rs) {
                    $editMsg = "* Fail to update in PurchaseRequest table! *";
                    $passEditing = false;
                }

                // Check if the data is successfully updated.
                if ($passEditing) {
                    $editMsg = "* Purchase Request is successfully updated! *";
                }
            }
        }
    }

    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Admin: Manage Purchase Page</title>
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
                <h4 style="font-size: 36px">Admin: Manage Purchase Page</h4>
            </div>
        </header>

        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Admin/navigationBar.php"); ?>

        <main>
            <div class="wrapper fadeInDown">
                <div id="formHeader">
                    <h1>Edit Purchase Request ID <?php
                        echo($requestID);
                    ?>:</h1>
                </div>
                <div id="formContentW2">
                    <br>
                    <img class="fadeIn first" src="https://img.freepik.com/free-vector/shop-with-sign-we-are-open_23-2148547718.jpg" id="icon45" alt="PurchaseRequest Icon" />

                    <form method="post" action="/Admin/editPurchase.php?RequestID=<?php
                        echo($requestID);
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

                            <?php if(!$passEditing): ?>
                                <tr class="fadeIn second">
                                    <!-- SaleID, read only. -->
                                    <td>
                                        <div>
                                            <label for="SaleID">
                                                Sale ID:
                                            </label><br>
                                            <input id="SaleID" type="number" value="<?php
                                                echo($result["SaleID"]);
                                            ?>" placeholder="Sale ID" readonly>
                                        </div>
                                    </td>

                                    <!-- SalePrice, read only. -->
                                    <td>
                                        <div>
                                            <label for="SalePrice">
                                                Sale Price (RM):
                                            </label><br>
                                            <input id="SalePrice" type="number" value="<?php
                                                echo($result["SalePrice"]);
                                            ?>" placeholder="Sale Price" readonly>
                                        </div>
                                    </td>
                                </tr>

                                <tr class="fadeIn third">
                                    <!-- ClientID, read only. -->
                                    <td>
                                        <div>
                                            <label for="ClientID">
                                                Client ID (Requester):
                                            </label><br>
                                            <input id="ClientID" type="number" value="<?php
                                                echo($result["ClientID"]);
                                            ?>" placeholder="Client ID" readonly>
                                        </div>
                                    </td>

                                    <!-- RequestPrice, read only. -->
                                    <td>
                                        <div>
                                            <label for="SalePrice">
                                                Request Price (RM):
                                            </label><br>
                                            <input id="RequestPrice" type="number" value="<?php
                                                echo($result["RequestPrice"]);
                                            ?>" placeholder="Request Price" readonly>
                                        </div>
                                    </td>
                                </tr>

                                <tr class="fadeIn fourth">
                                    <!-- ApprovalStatus -->
                                    <td colspan="2">
                                        <div>
                                            <label for="ApprovalStatus">
                                                Approval Status:
                                            </label><br>
                                            <select id="ApprovalStatus" name="ApprovalStatus">
                                                <option value="2">Rejected</option>
                                                <option value="1">Approved</option>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                            
                                <tr class="fadeIn fourth">
                                    <!-- Admin Password -->
                                    <td colspan="2">
                                        <div>
                                            <label for="Password">
                                                Admin Password:
                                            </label><br>
                                            <input id="Password" type="password" name="Password" placeholder="Enter Password to Confirm" required>
                                        </div>
                                    </td>
                                </tr>

                                <tr class="fadeIn fifth">
                                    <td colspan="2">
                                        <div>
                                            <br>
                                            <input type="submit" value="Confirm Approval Status">
                                        </div>
                                    </td>
                                </tr>

                                <tr class="fadeIn fifth">
                                    <td colspan="2">
                                        <span class="error-message">
                                            * WARNING: Once confirmed, the status cannot be changed again! *
                                        </span>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </table>
                    </form>
                    <br>
                    <div id="formFooter">
                        <?php if(!$passEditing): ?>
                            <h2><a class="underlineHover" href="/Admin/viewEachPurchase.php?RequestID=<?php
                                echo($requestID);
                            ?>">Back to View Purchase Request</a><h2><br>
                        <?php else: ?>
                            <h2><a class="underlineHover" href="/Admin/managePurchase.php">Back to Manage Purchase</a><h2><br>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
