<?php
    // Count orchard owned by a company.
    function getCompanyOrchardCount($conn, $id) {
        $query = "SELECT COUNT(`OrchardID`) FROM `Orchard` WHERE `CompanyID` = $id;";
        $rs = $conn->query($query);
        if ($rs) {
            if ($resultRow = mysqli_fetch_assoc($rs)) {
                return $resultRow["COUNT(`OrchardID`)"];
            }
        }
        return 0;
    }
?>
