<?php
    // Admin Manage Admin Page.
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

    $allAdmin = NULL;
    // Admin is not available for editing.
    if (
        !isset($queryString["AdminID"]) ||
        !is_numeric($queryString["AdminID"]) ||
        $queryString["AdminID"] < 1 ||
        count($allAdmin = getAllAdmin($conn, $queryString["AdminID"])) < 1
    ) {
        header("Location: /Admin/manageAdmin.php");
        exit;
    }

    $adminID = $queryString["AdminID"];
    $result = $allAdmin[0];

    $tempName = $tempRName = $tempEmail = $tempPass = $tempRPass = $tempAdminPass = "";
    $editMsg = "";
    $passEditing = false;

    // Edit attempt.
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $tempName = (isset($_POST["Username"])) ? cleanInput($_POST["Username"]): "";
        $tempRName = (isset($_POST["RealName"])) ? cleanInput($_POST["RealName"]): "";
        $tempEmail = (isset($_POST["Email"])) ? cleanInput($_POST["Email"]): "";
        $tempPass = (isset($_POST["Password"])) ? cleanInput($_POST["Password"]): "";
        $tempRPass = (isset($_POST["ReconfirmPassword"])) ? cleanInput($_POST["ReconfirmPassword"]): "";
        $tempAdminPass = (isset($_POST["AdminPassword"])) ? cleanInput($_POST["AdminPassword"]): "";

        $tempHash = "";

        if (
            empty($tempName) ||
            empty($tempRName) ||
            empty($tempEmail) ||
            empty($tempAdminPass)
        ) {
            $editMsg = "* Fill in ALL Fields! *";
            $passEditing = false;
        }
        else {
            // Set to true at first.
            $passEditing = true;
            $editPass = false;
            
            // Check if new password is provided.
            if (!empty($tempPass) || !empty($tempRPass)) {
                $editPass = true;

                // Must have 2 password inputs.
                if (empty($tempPass) || empty($tempRPass)) {
                    $editMsg = "* Fill in BOTH Fields for NEW Password! *";
                    $passEditing = false;
                }
            }

            // Check Username.
            if (
                $passEditing &&
                checkExistUsername($conn, $tempName) &&
                $tempName != $result["Username"]
            ) {
                $editMsg = "* Username is used by another user! *";
                $passEditing = false;
            }

            // Check Email.
            if (
                $passEditing &&
                checkExistEmail($conn, $tempEmail) &&
                $tempEmail != $result["Email"]
            ) {
                $editMsg = "* Email is used by another user! *";
                $passEditing = false;
            }

            // Check Password.
            if (
                $passEditing &&
                $editPass &&
                empty($tempHash = checkReconfirmPassword($tempPass, $tempRPass))
            ) {
                $editMsg = "* Reenter the EXACT SAME Password! *";
                $passEditing = false;
            }

            // Check Admin Password (admin to be edited).
            if (
                $passEditing &&
                !password_verify($tempAdminPass, $result["PasswordHash"])
            ) {
                $editMsg = "* Invalid Current Password! *";
                $passEditing = false;
            }

            // Update in DB.
            if ($passEditing) {
                // Nothing to Update in Admin table.
                $tempNameEscaped = $conn->real_escape_string($tempName);
                $tempEmailEscaped = $conn->real_escape_string($tempEmail);
                $tempRNameEscaped = $conn->real_escape_string($tempRName);

                // Update in User table.
                $query = "UPDATE `User`";
                $query .= " SET `Username`='$tempNameEscaped'";
                $query .= ", `Email`='$tempEmailEscaped'";

                if ($editPass) {
                    $query .= ", `PasswordHash`='$tempHash'";
                }

                $query .= ", `RealName`='$tempRNameEscaped'";
                $query .= " WHERE `User`.`UserID`='$adminID';";

                $rs = $conn->query($query);
                if (!$rs) {
                    $editMsg = "* Fail to update in User table! *";
                    $passEditing = false;
                }
                // Check if current logged in Admin is changed.
                elseif ($_SESSION["UserID"] == $adminID) {
                    // Update session.
                    $_SESSION["Username"] = $tempName;
                    if ($editPass) {
                        $_SESSION["PasswordHash"] = $tempHash;
                    }
                }
            }
            
            // Check if the data is successfully updated.
            if ($passEditing) {
                $editMsg = "* Admin is successfully updated! *";
            }
        }
    }
    if (empty($tempName)) {
        $tempName = $result["Username"];
    }

    if (empty($tempRName)) {
        $tempRName = $result["RealName"];
    }

    if (empty($tempEmail)) {
        $tempEmail = $result["Email"];
    }

    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Admin: Manage Admin Page</title>
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
                <h4 style="font-size: 36px">Admin: Manage Admin Page</h4>
            </div>
        </header>

        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Admin/navigationBar.php"); ?>

        <main>
            <div class="wrapper fadeInDown">
                <div id="formHeader">
                    <h1>Edit Admin ID <?php
                        echo($adminID);
                    ?>:</h1>
                </div>
                <div id="formContentW2">
                    <img class="fadeIn first" src="/img/defaults/adminIcon.png" id="icon" alt="Admin Icon" />

                    <form method="post" action="/Admin/editAdmin.php?AdminID=<?php
                        echo($adminID);
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

                            <tr class="fadeIn second">
                                <!-- Username -->
                                <td>
                                    <div>
                                        <label for="Username">
                                            Username:
                                        </label><br>
                                        <input id="Username" type="text" name="Username" value="<?php
                                            echo($tempName);
                                        ?>" placeholder="Username" required>
                                    </div>
                                </td>

                                <!-- RealName -->
                                <td>
                                    <div>
                                        <label for="RealName">
                                            Admin Name:
                                        </label><br>
                                        <input id="RealName" type="text" name="RealName" value="<?php
                                            echo($tempRName);
                                        ?>" placeholder="Admin Name" required>
                                    </div>
                                </td>
                            </tr>

                            <tr class="fadeIn third">
                                <!-- Email -->
                                <td colspan="2">
                                    <div>
                                        <label for="Email">
                                            Email:
                                        </label><br>
                                        <input id="Email" type="email" name="Email" value="<?php
                                            echo($tempEmail);
                                        ?>" placeholder="abc@email.com" required>
                                    </div>
                                </td>
                            </tr>

                            <tr class="fadeIn fourth">
                                <!-- Password -->
                                <td>
                                    <div>
                                        <label for="Password">
                                            New Password (Optional):
                                        </label><br>
                                        <input id="Password" type="password" name="Password" placeholder="Password">
                                    </div>
                                </td>

                                <!-- ReconfirmPassword -->
                                <td>
                                    <div>
                                        <label for="ReconfirmPassword">
                                            Reconfirm New Password:
                                        </label><br>
                                        <input id="ReconfirmPassword" type="password" name="ReconfirmPassword" placeholder="Reconfirm Password">
                                    </div>
                                </td>
                            </tr>

                            <tr class="fadeIn fourth">
                                <!-- AdminPassword -->
                                <td colspan="2">
                                    <div>
                                        <label for="AdminPassword">
                                            Current Password (for Admin ID <?php
                                                echo($adminID);
                                            ?>):
                                        </label><br>
                                        <input id="AdminPassword" type="password" name="AdminPassword" placeholder="Enter Password to Edit" required>
                                    </div>
                                </td>
                            </tr>

                            <tr class="fadeIn fifth">
                                <td colspan="2">
                                    <div>
                                        <br>
                                        <input type="submit" value="Save Editing">
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </form>
                    <br>
                    <div id="formFooter">
                        <h2><a class="underlineHover" href="/Admin/viewEachAdmin.php?AdminID=<?php
                            echo($adminID);
                        ?>">Back to View Admin</a><h2><br>
                    </div>
                </div>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
