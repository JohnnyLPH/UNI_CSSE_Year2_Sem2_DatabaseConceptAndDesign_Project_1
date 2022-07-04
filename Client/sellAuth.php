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
        $tempBlockID = (isset($_GET["block_id"])) ? cleanInput($_GET["block_id"]): "";
        $tempSalePrice = (isset($_GET["sale_price"])) ? cleanInput($_GET["sale_price"]): "";

        if(
            empty($tempBlockID) || 
            empty($tempSalePrice) ||
            $tempSalePrice < 5000
        ) {
            header("Location: /Client/sell_block.php");
            exit;
        } else {
            $tempSellerID = $_SESSION["UserID"];
            date_default_timezone_set('Asia/Kuala_Lumpur');
            $tempDate = date('Y-m-d H:i:s', time());
            $tempApproval = 0;

            $query = "INSERT INTO `OnSale`(`BlockID`, `SaleDate`, `SalePrice`, `SellerID`)";
            $query .= " VALUES ('$tempBlockID', '$tempDate', '$tempSalePrice', '$tempSellerID')";
            $result = $conn->query($query);

            $message = "success";

            if (!$result) {
                $message = "error";
            }

            header("Location: /Client/sell_block.php?" . $message);
            exit;
        }
    } else {
        header("Location: /Client/sell_block.php");
        exit;
    } 

    $conn->close();
?>