<?php
    // Count orchard (based on CompanyID if provided).
    function getOrchardCount($conn, $companyID = 0) {
        $query = "SELECT COUNT(`Orchard`.`OrchardID`) FROM `Orchard`";

        // Add WHERE Clause.
        $multiWhere = false;

        if ($companyID > 0) {
            $query .= " WHERE `Orchard`.`CompanyID` = $companyID";
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
            $query .= " WHERE `Orchard`.`CompanyID` = $companyID";
            $multiWhere = true;
        }

        if ($orchardID > 0) {
            if ($multiWhere) {
                $query .= " AND `Orchard`.`OrchardID` = $orchardID";
            }
            else {
                $query .= " WHERE `Orchard`.`OrchardID` = $orchardID";
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
                $query .= " AND `Orchard`.`OrchardID` = $orchardID";
            }
            else {
                $query .= " WHERE `Orchard`.`OrchardID` = $orchardID";
                $multiWhere = true;
            }
        }

        if ($blockID > 0) {
            if ($multiWhere) {
                $query .= " AND `Block`.`BlockID` = $blockID";
            }
            else {
                $query .= " WHERE `Block`.`BlockID` = $blockID";
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
                $query .= " AND `Orchard`.`CompanyID` = $companyID";
            }
            else {
                $query .= " WHERE `Orchard`.`CompanyID` = $companyID";
                $multiWhere = true;
            }
        }

        if ($orchardID > 0) {
            if ($multiWhere) {
                $query .= " AND `Orchard`.`OrchardID` = $orchardID";
            }
            else {
                $query .= " WHERE `Orchard`.`OrchardID` = $orchardID";
                $multiWhere = true;
            }
        }

        if ($blockID > 0) {
            if ($multiWhere) {
                $query .= " AND `Block`.`BlockID` = $blockID";
            }
            else {
                $query .= " WHERE `Block`.`BlockID` = $blockID";
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
        $query = "SELECT * FROM `Orchard`";
        
        // Add WHERE Clause.
        $multiWhere = false;

        if ($companyID > 0) {
            $query .= " WHERE `Orchard`.`CompanyID` = $companyID";
            $multiWhere = true;
        }

        if ($orchardID > 0) {
            if ($multiWhere) {
                $query .= " AND `Orchard`.`OrchardID` = $orchardID";
            }
            else {
                $query .= " WHERE `Orchard`.`OrchardID` = $orchardID";
                $multiWhere = true;
            }
        }

        $query .= " ORDER BY `Orchard`.`OrchardID`;";
        $allRow = array();
        $rs = $conn->query($query);
        if ($rs) {
            while ($resultRow = mysqli_fetch_assoc($rs)) {
                array_push($allRow, $resultRow);
            }
        }
        return $allRow;
    }

    // Return all rows of Orchard (based on CompanyID, OrchardID, BlockID if provided).
    function getAllBlock($conn, $companyID = 0, $orchardID = 0, $blockID = 0) {
        $query = "SELECT * FROM `Block`";
        $query .= " INNER JOIN `Orchard` ON `Block`.`OrchardID` = `Orchard`.`OrchardID`";
        
        // Add WHERE Clause.
        $multiWhere = false;

        if ($companyID > 0) {
            $query .= " WHERE `Orchard`.`CompanyID` = $companyID";
            $multiWhere = true;
        }

        if ($orchardID > 0) {
            if ($multiWhere) {
                $query .= " AND `Orchard`.`OrchardID` = $orchardID";
            }
            else {
                $query .= " WHERE `Orchard`.`OrchardID` = $orchardID";
                $multiWhere = true;
            }
        }

        if ($blockID > 0) {
            if ($multiWhere) {
                $query .= " AND `Block`.`BlockID` = $blockID";
            }
            else {
                $query .= " WHERE `Block`.`BlockID` = $blockID";
                $multiWhere = true;
            }
        }

        $query .= " ORDER BY `Block`.`BlockID`;";
        $allRow = array();
        $rs = $conn->query($query);
        if ($rs) {
            while ($resultRow = mysqli_fetch_assoc($rs)) {
                array_push($allRow, $resultRow);
            }
        }
        return $allRow;
    }

    // Get Block Latest Client (can be used to find Current Owner of the Block).
    function getBlockLatestClient($conn, $companyID = 0, $orchardID = 0, $blockID = 0, $clientID = 0) {
        // SQL:
        // SELECT `Block`.`BlockID`, `Orchard`.`OrchardID`, `Orchard`.`CompanyID`
        // , `PurchaseRequest`.`ClientID`, `PurchaseRequest`.`ApprovalStatus`, `User`.`RealName`
        // FROM `Block`
        // INNER JOIN `Orchard` ON `Block`.`OrchardID` = `Orchard`.`OrchardID`
        // LEFT JOIN `OnSale` ON `Block`.`BlockID` = `OnSale`.`BlockID`
        // LEFT JOIN `PurchaseRequest` ON `OnSale`.`SaleID` = `PurchaseRequest`.`SaleID`
        // LEFT JOIN `Client` ON `PurchaseRequest`.`ClientID` = `Client`.`UserID`
        // LEFT JOIN `User` ON `Client`.`UserID` = `User`.`UserID`
        // ORDER BY `Block`.`BlockID`, `OnSale`.`SaleID` DESC, `PurchaseRequest`.`RequestID` DESC;
        $query = "SELECT `Block`.`BlockID`, `Orchard`.`OrchardID`, `Orchard`.`CompanyID`";
        $query .= ", `PurchaseRequest`.`ClientID`, `PurchaseRequest`.`ApprovalStatus`, `User`.`RealName`";
        $query .= " FROM `Block`";
        $query .= " INNER JOIN `Orchard` ON `Block`.`OrchardID` = `Orchard`.`OrchardID`";
        $query .= " LEFT JOIN `OnSale` ON `Block`.`BlockID` = `OnSale`.`BlockID`";
        $query .= " LEFT JOIN `PurchaseRequest` ON `OnSale`.`SaleID` = `PurchaseRequest`.`SaleID`";
        $query .= " LEFT JOIN `Client` ON `PurchaseRequest`.`ClientID` = `Client`.`UserID`";
        $query .= " LEFT JOIN `User` ON `Client`.`UserID` = `User`.`UserID`";

        // Add WHERE Clause.
        $multiWhere = false;

        if ($companyID > 0) {
            $query .= " WHERE `Orchard`.`CompanyID` = $companyID";
            $multiWhere = true;
        }

        if ($orchardID > 0) {
            if ($multiWhere) {
                $query .= " AND `Orchard`.`OrchardID` = $orchardID";
            }
            else {
                $query .= " WHERE `Orchard`.`OrchardID` = $orchardID";
                $multiWhere = true;
            }
        }

        if ($blockID > 0) {
            if ($multiWhere) {
                $query .= " AND `Block`.`BlockID` = $blockID";
            }
            else {
                $query .= " WHERE `Block`.`BlockID` = $blockID";
                $multiWhere = true;
            }
        }

        // Block Latest Client.
        $query .= " ORDER BY `Block`.`BlockID`, `OnSale`.`SaleID` DESC, `PurchaseRequest`.`RequestID` DESC;";
        $allRow = array();
        $lastCheckBlock = 0;

        $rs = $conn->query($query);
        if ($rs) {
            while ($resultRow = mysqli_fetch_assoc($rs)) {
                if ($resultRow["BlockID"] != $lastCheckBlock) {
                    $lastCheckBlock = $resultRow["BlockID"];
                    // Filter by ClientID, only store block owned by the client.
                    if (
                        $clientID > 0 &&
                        ($resultRow["ClientID"] != $clientID || $resultRow["ApprovalStatus"] != 1)
                    ) {
                        continue;
                    }
                    array_push($allRow, $resultRow);
                }
            }
        }
        return $allRow;
    }

    // Return all rows of Orchard (based on CompanyID, OrchardID, BlockID, TreeID if provided).
    function getAllTree($conn, $companyID = 0, $orchardID = 0, $blockID = 0, $treeID = 0) {
        $query = "SELECT * FROM `Tree`";
        $query .= " INNER JOIN `Block` ON `Tree`.`BlockID` = `Block`.`BlockID`";
        $query .= " INNER JOIN `Orchard` ON `Block`.`OrchardID` = `Orchard`.`OrchardID`";
        
        // Add WHERE Clause.
        $multiWhere = false;

        if ($companyID > 0) {
            $query .= " WHERE `Orchard`.`CompanyID` = $companyID";
            $multiWhere = true;
        }

        if ($orchardID > 0) {
            if ($multiWhere) {
                $query .= " AND `Orchard`.`OrchardID` = $orchardID";
            }
            else {
                $query .= " WHERE `Orchard`.`OrchardID` = $orchardID";
                $multiWhere = true;
            }
        }

        if ($blockID > 0) {
            if ($multiWhere) {
                $query .= " AND `Block`.`BlockID` = $blockID";
            }
            else {
                $query .= " WHERE `Block`.`BlockID` = $blockID";
                $multiWhere = true;
            }
        }

        if ($treeID > 0) {
            if ($multiWhere) {
                $query .= " AND `Tree`.`TreeID` = $treeID";
            }
            else {
                $query .= " WHERE `Tree`.`TreeID` = $treeID";
                $multiWhere = true;
            }
        }

        $query .= " ORDER BY `Tree`.`TreeID`;";
        $allRow = array();
        $rs = $conn->query($query);
        if ($rs) {
            while ($resultRow = mysqli_fetch_assoc($rs)) {
                array_push($allRow, $resultRow);
            }
        }
        return $allRow;
    }

    // Return all rows of Orchard (based on CompanyID, OrchardID, BlockID, TreeID if provided).
    function getAllTreeUpdate($conn, $companyID = 0, $orchardID = 0, $blockID = 0, $treeID = 0) {
        $query = "SELECT `TreeUpdate`.`UpdateID`, `TreeUpdate`.`UpdateDate`, `TreeUpdate`.`TreeImage`";
        $query .= ", `TreeUpdate`.`TreeHeight`, `TreeUpdate`.`Diameter`, `TreeUpdate`.`Status`";
        $query .= " FROM `TreeUpdate`";
        $query .= " INNER JOIN `Tree` ON `TreeUpdate`.`TreeID` = `Tree`.`TreeID`";
        $query .= " INNER JOIN `Block` ON `Tree`.`BlockID` = `Block`.`BlockID`";
        $query .= " INNER JOIN `Orchard` ON `Block`.`OrchardID` = `Orchard`.`OrchardID`";
        
        // Add WHERE Clause.
        $multiWhere = false;

        if ($companyID > 0) {
            $query .= " WHERE `Orchard`.`CompanyID` = $companyID";
            $multiWhere = true;
        }

        if ($orchardID > 0) {
            if ($multiWhere) {
                $query .= " AND `Orchard`.`OrchardID` = $orchardID";
            }
            else {
                $query .= " WHERE `Orchard`.`OrchardID` = $orchardID";
                $multiWhere = true;
            }
        }

        if ($blockID > 0) {
            if ($multiWhere) {
                $query .= " AND `Block`.`BlockID` = $blockID";
            }
            else {
                $query .= " WHERE `Block`.`BlockID` = $blockID";
                $multiWhere = true;
            }
        }

        if ($treeID > 0) {
            if ($multiWhere) {
                $query .= " AND `Tree`.`TreeID` = $treeID";
            }
            else {
                $query .= " WHERE `Tree`.`TreeID` = $treeID";
                $multiWhere = true;
            }
        }

        // Latest TreeUpdate.
        $query .= " ORDER BY `TreeUpdate`.`UpdateDate` DESC, `TreeUpdate`.`UpdateID` DESC;";
        $allRow = array();
        $rs = $conn->query($query);
        if ($rs) {
            while ($resultRow = mysqli_fetch_assoc($rs)) {
                array_push($allRow, $resultRow);
            }
        }
        return $allRow;
    }

    // Return all rows of Purchase Request (based on ApprovalStatus, CompanyID, OrchardID, BlockID, RequestID, SaleID, ClientID if provided).
    function getAllPurchaseRequest($conn, $approvalStatus = -1, $companyID = 0, $orchardID = 0, $blockID = 0, $requestID = 0, $saleID = 0, $clientID = 0, $orderByStatus = false) {
        $query = "SELECT `PurchaseRequest`.`RequestID`, `Orchard`.`CompanyID`, `Orchard`.`OrchardID`";
        $query .= ", `Block`.`BlockID`, `User`.`RealName`, `OnSale`.`SaleDate`, `OnSale`.`SalePrice`";
        $query .= ", `OnSale`.`SellerID`, `PurchaseRequest`.`SaleID`, `PurchaseRequest`.`RequestDate`";
        $query .= ", `PurchaseRequest`.`AdminID`, `PurchaseRequest`.`ClientID`";
        $query .= ", `PurchaseRequest`.`RequestPrice`, `PurchaseRequest`.`ApprovalStatus`";
        $query .= " FROM `PurchaseRequest`";
        $query .= " INNER JOIN `Client` ON `PurchaseRequest`.`ClientID` = `Client`.`UserID`";
        $query .= " INNER JOIN `User` ON `Client`.`UserID` = `User`.`UserID`";
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
                $query .= " AND `Orchard`.`CompanyID` = $companyID";
            }
            else {
                $query .= " WHERE `Orchard`.`CompanyID` = $companyID";
                $multiWhere = true;
            }
        }

        if ($orchardID > 0) {
            if ($multiWhere) {
                $query .= " AND `Orchard`.`OrchardID` = $orchardID";
            }
            else {
                $query .= " WHERE `Orchard`.`OrchardID` = $orchardID";
                $multiWhere = true;
            }
        }

        if ($blockID > 0) {
            if ($multiWhere) {
                $query .= " AND `Block`.`BlockID` = $blockID";
            }
            else {
                $query .= " WHERE `Block`.`BlockID` = $blockID";
                $multiWhere = true;
            }
        }

        if ($requestID > 0) {
            if ($multiWhere) {
                $query .= " AND `PurchaseRequest`.`RequestID` = $requestID";
            }
            else {
                $query .= " WHERE `PurchaseRequest`.`RequestID` = $requestID";
                $multiWhere = true;
            }
        }

        if ($saleID > 0) {
            if ($multiWhere) {
                $query .= " AND `PurchaseRequest`.`SaleID` = $saleID";
            }
            else {
                $query .= " WHERE `PurchaseRequest`.`SaleID` = $saleID";
                $multiWhere = true;
            }
        }

        if ($clientID > 0) {
            if ($multiWhere) {
                $query .= " AND `PurchaseRequest`.`ClientID` = $clientID";
            }
            else {
                $query .= " WHERE `PurchaseRequest`.`ClientID` = $clientID";
                $multiWhere = true;
            }
        }

        // Latest PurchaseRequest.
        $query .= " ORDER BY";

        if ($orderByStatus) {
            $query .= " `PurchaseRequest`.`ApprovalStatus`,";
        }

        $query .= " `PurchaseRequest`.`RequestDate` DESC, `PurchaseRequest`.`RequestID` DESC;";
        $allRow = array();
        $rs = $conn->query($query);
        if ($rs) {
            while ($resultRow = mysqli_fetch_assoc($rs)) {
                array_push($allRow, $resultRow);
            }
        }
        return $allRow;
    }

    // Return all rows of On Sale (based on CompanyID, OrchardID, BlockID, SaleID if provided).
    function getAllOnSale($conn, $companyID = 0, $orchardID = 0, $blockID = 0, $saleID = 0) {
        $query = "SELECT * FROM `OnSale`";
        $query .= " INNER JOIN `Block` ON `OnSale`.`BlockID` = `Block`.`BlockID`";
        $query .= " INNER JOIN `Orchard` ON `Block`.`OrchardID` = `Orchard`.`OrchardID`";

        // Add WHERE Clause.
        $multiWhere = false;

        if ($companyID > 0) {
            $query .= " WHERE `Orchard`.`CompanyID` = $companyID";
            $multiWhere = true;
        }

        if ($orchardID > 0) {
            if ($multiWhere) {
                $query .= " AND `Orchard`.`OrchardID` = $orchardID";
            }
            else {
                $query .= " WHERE `Orchard`.`OrchardID` = $orchardID";
                $multiWhere = true;
            }
        }

        if ($blockID > 0) {
            if ($multiWhere) {
                $query .= " AND `Block`.`BlockID` = $blockID";
            }
            else {
                $query .= " WHERE `Block`.`BlockID` = $blockID";
                $multiWhere = true;
            }
        }

        if ($saleID > 0) {
            if ($multiWhere) {
                $query .= " AND `OnSale`.`SaleID` = $saleID";
            }
            else {
                $query .= " WHERE `OnSale`.`SaleID` = $saleID";
                $multiWhere = true;
            }
        }

        // Latest On Sale.
        $query .= " ORDER BY `OnSale`.`SaleDate` DESC, `OnSale`.`SaleID` DESC;";
        $allRow = array();
        $rs = $conn->query($query);
        if ($rs) {
            while ($resultRow = mysqli_fetch_assoc($rs)) {
                array_push($allRow, $resultRow);
            }
        }
        return $allRow;
    }

    // Return PurchaseRequest ApprovalStatus string.
    function getApprovalStatusStr($approvalStatus) {
        if ($approvalStatus < 1) {
            return "<i>Not Processed</i>";
        }
        elseif ($approvalStatus == 1) {
            return "Approved";
        }
        else {
            return "Rejected";
        }
    }

    // Return TreeUpdate Status string.
    function getTreeUpdateStatus($status) {
        if ($status == 'Y') {
            return "Yellow";
        }
        elseif ($status == 'R') {
            return "Red";
        }
        else {
            return "Green";
        }
    }
?>
