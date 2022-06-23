<?php
    // Count orchard owned by a company.
    function getCompanyOrchardCount($conn, $id) {
        $query = "SELECT COUNT(`Orchard`.`OrchardID`) FROM `Orchard` WHERE `CompanyID` = $id;";
        $rs = $conn->query($query);
        if ($rs) {
            if ($resultRow = mysqli_fetch_assoc($rs)) {
                return $resultRow["COUNT(`Orchard`.`OrchardID`)"];
            }
        }
        return 0;
    }

    // Count block owned by a company.
    function getCompanyBlockCount($conn, $id) {
        $query = "SELECT COUNT(`Block`.`BlockID`) FROM `Block`";
        $query .= " INNER JOIN `Orchard` ON `Block`.`OrchardID` = `Orchard`.`OrchardID`";
        $query .= " WHERE `CompanyID` = $id;";
        $rs = $conn->query($query);
        if ($rs) {
            if ($resultRow = mysqli_fetch_assoc($rs)) {
                return $resultRow["COUNT(`Block`.`BlockID`)"];
            }
        }
        return 0;
    }

    // Count tree planted by a company.
    function getCompanyTreeCount($conn, $id) {
        $query = "SELECT COUNT(`Tree`.`TreeID`) FROM `Tree`";
        $query .= " INNER JOIN `Block` ON `Tree`.`BlockID` = `Block`.`BlockID`";
        $query .= " INNER JOIN `Orchard` ON `Block`.`OrchardID` = `Orchard`.`OrchardID`";
        $query .= " WHERE `CompanyID` = $id;";
        $rs = $conn->query($query);
        if ($rs) {
            if ($resultRow = mysqli_fetch_assoc($rs)) {
                return $resultRow["COUNT(`Tree`.`TreeID`)"];
            }
        }
        return 0;
    }

    // Count purchase of block (approved PurchaseRequest) owned by a company.
    function getCompanyPurchaseCount($conn, $id) {
        $query = "SELECT COUNT(`PurchaseRequest`.`RequestID`) FROM `PurchaseRequest`";
        $query .= " INNER JOIN `OnSale` ON `PurchaseRequest`.`SaleID` = `OnSale`.`SaleID`";
        $query .= " INNER JOIN `Block` ON `OnSale`.`BlockID` = `Block`.`BlockID`";
        $query .= " INNER JOIN `Orchard` ON `Block`.`OrchardID` = `Orchard`.`OrchardID`";
        $query .= " WHERE `PurchaseRequest`.`ApprovalStatus` = 1 AND `CompanyID` = $id;";
        $rs = $conn->query($query);
        if ($rs) {
            if ($resultRow = mysqli_fetch_assoc($rs)) {
                return $resultRow["COUNT(`PurchaseRequest`.`RequestID`)"];
            }
        }
        return 0;
    }
?>
