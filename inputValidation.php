<?php
    // Clean the input data before processing.
    function cleanInput($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    // Return true if Username already exist.
    function checkExistUsername($conn, $data) {
        $query = "SELECT Username FROM User WHERE Username='$data';";
        $rs = $conn->query($query);
        if ($rs) {
            if ($user = mysqli_fetch_assoc($rs)) {
                if ($user["Username"] == $data) {
                    return true;
                }
            }
        }
        return false;
    }

    // Return true if Email already exist.
    function checkExistEmail($conn, $data) {
        $query = "SELECT Email FROM User WHERE Email='$data';";
        $rs = $conn->query($query);
        if ($rs) {
            if ($user = mysqli_fetch_assoc($rs)) {
                if ($user["Email"] == $data) {
                    return true;
                }
            }
        }
        return false;
    }

    // Return PasswordHash if both match.
    function checkReconfirmPassword($pass, $rPass) {
        if ($pass == $rPass) {
            return password_hash($pass, PASSWORD_DEFAULT);
        }
        return "";
    }
?>
