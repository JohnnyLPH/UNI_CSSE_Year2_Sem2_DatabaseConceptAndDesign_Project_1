<?php
    // Authenticate Login.
    // Required on top of every main file.
    if (session_id() == "") {
        session_start();
    }

    // Return 0 if none is logged in or active.
    // 1 if Company is logged in, 2 if Staff is logged in, 3 if Client is logged in, 4 if Admin is logged in.
    function checkLogin($conn) {
        $expireMin = 30;

        // Confirm logged in.
        if (
            !isset($_SESSION["UserID"]) || $_SESSION["UserID"] < 0 ||
            !isset($_SESSION["Username"]) || empty($_SESSION["Username"]) ||
            !isset($_SESSION["PasswordHash"]) || empty($_SESSION["PasswordHash"]) ||
            !isset($_SESSION["UserType"]) || empty($_SESSION["UserType"])
        ) {
            return 0;
        }
        // Check last active time.
        else if (
            !isset($_SESSION["lastActive"]) ||
            strtotime(date("Y-m-d H:i:s")) - $_SESSION["lastActive"] > ($expireMin * 60)
        ) {
            return 0;
        }
        
        $tempID = $_SESSION["UserID"];
        $tempName = $_SESSION["Username"];
        $tempHash = $_SESSION["PasswordHash"];
        $tempType = $_SESSION["UserType"];
        
        // Check if the UserID, Username, PasswordHash, and UserType match with what's stored.
        $query = "SELECT UserID, Username, PasswordHash, UserType FROM User WHERE UserId='$tempID';";

        $rs = $conn->query($query);
        if ($rs) {
            if ($user = mysqli_fetch_assoc($rs)) {
                if (
                    $user["Username"] == $tempName &&
                    $user["PasswordHash"] == $tempHash &&
                    $user["UserType"] == $tempType
                ) {
                    // Reset last active time.
                    $_SESSION["lastActive"] = strtotime(date("Y-m-d H:i:s"));

                    if ($tempType == "CO") {
                        return 1;
                    }
                    else if ($tempType == "ST") {
                        return 2;
                    }
                    else if ($tempType == "CL") {
                        return 3;
                    }
                    else if ($tempType == "AD") {
                        return 4;
                    }
                }
            }
        }
        return 0;
    }
?>
