<?php
    // Count orchard (based on CompanyID).
    function getOrchardCount($conn, $companyID = 0) {
        $query = "SELECT COUNT(`Orchard`.`OrchardID`) FROM `Orchard`";

        // Add WHERE Clause.
        $multiWhere = false;

        if ($companyID > 0) {
            $query .= " WHERE `CompanyID` = $companyID";
            $multiWhere = true;
        }

        $query .= ";";
        $rs = $conn->query($query);
        if ($rs) {
            if ($resultRow = mysqli_fetch_assoc($rs)) {
                return $resultRow["COUNT(`Orchard`.`OrchardID`)"];
            }
        }
        return 0;
    }

    // Count block (based on CompanyID, OrchardID if provided).
    function getBlockCount($conn, $companyID = 0, $orchardID = 0) {
        $query = "SELECT COUNT(`Block`.`BlockID`) FROM `Block`";
        $query .= " INNER JOIN `Orchard` ON `Block`.`OrchardID` = `Orchard`.`OrchardID`";

        // Add WHERE Clause.
        $multiWhere = false;

        if ($companyID > 0) {
            $query .= " WHERE `CompanyID` = $companyID";
            $multiWhere = true;
        }

        if ($orchardID > 0) {
            if ($multiWhere) {
                $query .= " AND `OrchardID` = $orchardID";
            }
            else {
                $query .= " WHERE `OrchardID` = $orchardID";
                $multiWhere = true;
            }
        }

        $query .= ";";
        $rs = $conn->query($query);
        if ($rs) {
            if ($resultRow = mysqli_fetch_assoc($rs)) {
                return $resultRow["COUNT(`Block`.`BlockID`)"];
            }
        }
        return 0;
    }

    // Count tree (based on CompanyID, OrchardID, BlockID if provided).
    function getTreeCount($conn, $companyID = 0, $orchardID = 0, $blockID = 0) {
        $query = "SELECT COUNT(`Tree`.`TreeID`) FROM `Tree`";
        $query .= " INNER JOIN `Block` ON `Tree`.`BlockID` = `Block`.`BlockID`";
        $query .= " INNER JOIN `Orchard` ON `Block`.`OrchardID` = `Orchard`.`OrchardID`";

        // Add WHERE Clause.
        $multiWhere = false;

        if ($companyID > 0) {
            $query .= " WHERE `CompanyID` = $companyID";
            $multiWhere = true;
        }

        if ($orchardID > 0) {
            if ($multiWhere) {
                $query .= " AND `OrchardID` = $orchardID";
            }
            else {
                $query .= " WHERE `OrchardID` = $orchardID";
                $multiWhere = true;
            }
        }

        if ($blockID > 0) {
            if ($multiWhere) {
                $query .= " AND `BlockID` = $blockID";
            }
            else {
                $query .= " WHERE `BlockID` = $blockID";
                $multiWhere = true;
            }
        }

        $query .= ";";
        $rs = $conn->query($query);
        if ($rs) {
            if ($resultRow = mysqli_fetch_assoc($rs)) {
                return $resultRow["COUNT(`Tree`.`TreeID`)"];
            }
        }
        return 0;
    }

    // Count purchase request (based on ApprovalStatus, CompanyID, OrchardID, BlockID if provided).
    function getPurchaseRequestCount($conn, $approvalStatus = -1, $companyID = 0, $orchardID = 0, $blockID = 0) {
        $query = "SELECT COUNT(`PurchaseRequest`.`RequestID`) FROM `PurchaseRequest`";
        $query .= " INNER JOIN `OnSale` ON `PurchaseRequest`.`SaleID` = `OnSale`.`SaleID`";
        $query .= " INNER JOIN `Block` ON `OnSale`.`BlockID` = `Block`.`BlockID`";
        $query .= " INNER JOIN `Orchard` ON `Block`.`OrchardID` = `Orchard`.`OrchardID`";

        // Add WHERE Clause.
        $multiWhere = false;

        if ($approvalStatus > -1 && $approvalStatus < 3) {
            $query .= " WHERE `PurchaseRequest`.`ApprovalStatus` = $approvalStatus";
            $multiWhere = true;
        }

        if ($companyID > 0) {
            if ($multiWhere) {
                $query .= " AND `CompanyID` = $companyID";
            }
            else {
                $query .= " WHERE `CompanyID` = $companyID";
                $multiWhere = true;
            }
        }

        if ($orchardID > 0) {
            if ($multiWhere) {
                $query .= " AND `OrchardID` = $orchardID";
            }
            else {
                $query .= " WHERE `OrchardID` = $orchardID";
                $multiWhere = true;
            }
        }

        if ($blockID > 0) {
            if ($multiWhere) {
                $query .= " AND `BlockID` = $blockID";
            }
            else {
                $query .= " WHERE `BlockID` = $blockID";
                $multiWhere = true;
            }
        }

        $query .= ";";
        $rs = $conn->query($query);
        if ($rs) {
            if ($resultRow = mysqli_fetch_assoc($rs)) {
                return $resultRow["COUNT(`PurchaseRequest`.`RequestID`)"];
            }
        }
        return 0;
    }

    // Return all rows of Orchard (based on CompanyID, OrchardID if provided).
    function getAllOrchard($conn, $companyID = 0, $orchardID = 0) {
        $query = "SELECT * FROM `Orchard` WHERE `CompanyID` = $companyID;";
        
        if ($companyID > 0 && $orchardID > 0) {
            $query .= " WHERE `CompanyID` = $companyID AND `OrchardID` = `$orchardID`;";
        }
        else if ($companyID > 0) {
            $query .= " WHERE `CompanyID` = $companyID;";
        }
        else if ($orchardID > 0) {
            $query .= " WHERE `OrchardID` = `$orchardID`;";
        }

        $allRow = array();
        $rs = $conn->query($query);
        if ($rs) {
            while ($resultRow = mysqli_fetch_assoc($rs)) {
                array_push($allRow, $resultRow);
            }
        }
        return $allRow;
    }
?>
