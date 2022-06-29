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
        $dataEscaped = $conn->real_escape_string($data);
        $query = "SELECT Username FROM User WHERE Username='$dataEscaped';";
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
        $dataEscaped = $conn->real_escape_string($data);
        $query = "SELECT Email FROM User WHERE Email='$dataEscaped';";
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

    // Return true if valid Latitude.
    function checkValidLatitude($data) {
        if (!is_numeric($data) || $data < -90 || $data > 90) {
            return false;
        }
        return true;
    }

    // Return true if valid Longitude.
    function checkValidLongitude($data) {
        if (!is_numeric($data) || $data < -180 || $data > 180) {
            return false;
        }
        return true;
    }
?>
