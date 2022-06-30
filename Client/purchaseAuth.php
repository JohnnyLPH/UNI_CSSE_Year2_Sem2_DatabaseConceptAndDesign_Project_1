<?php
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

    // Empty request price + request price < sale price
    if($_SERVER["REQUEST_METHOD"] == "GET") {
        $tempSaleID = (isset($_GET["sale_id"])) ? cleanInput($_GET["sale_id"]): "";
        $tempPrice = (isset($_GET["request_price"])) ? cleanInput($_GET["request_price"]): "";
        $tempSalePrice = (isset($_GET["request_block_price"])) ? cleanInput($_GET["request_block_price"]): "";

        if(
            empty($tempSaleID) || 
            empty($tempPrice) ||
            empty($tempSalePrice) ||
            $tempPrice < $tempSalePrice
        ) {
            header("Location: /Client/browsePage.php");
            exit;
        } else {
            $tempClientID = $_SESSION["UserID"];
            date_default_timezone_set('Asia/Kuala_Lumpur');
            $tempDate = date('Y-m-d H:i:s', time());
            $tempApproval = 0;

            $query = "INSERT INTO `PurchaseRequest`(`SaleID`, `ClientID`, `RequestDate`, `RequestPrice`, `ApprovalStatus`)";
            $query .= " VALUES ('$tempSaleID', '$tempClientID', '$tempDate', '$tempPrice', '$tempApproval')";
            $result = $conn->query($query);

            $errorMsg = "";

            if (!$result) {
                $errorMsg = "err";
            }

            header("Location: /Client/browsePage.php?" . $errorMsg);
            exit;
        }
    } else {
        header("Location: /Client/browsePage.php");
        exit;
    } 

    $conn->close();
?>