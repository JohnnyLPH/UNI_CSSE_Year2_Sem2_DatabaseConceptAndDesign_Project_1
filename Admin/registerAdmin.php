<?php
    // Admin Registration Page.
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dbConnection.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/loginAuthenticate.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/inputValidation.php");

    $tempLoginCheck = checkLogin($conn);
    // Not logged in as Admin.
    if ($tempLoginCheck != 4) {
        header("Location: /index.php");
        exit;
    }

    $tempName = $tempRName = $tempEmail = $tempPass = $tempRPass = "";
    $registrationMsg = "";
    $passRegistration = false;

    // Registration attempt.
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $tempName = (isset($_POST["Username"])) ? cleanInput($_POST["Username"]): "";
        $tempRName = (isset($_POST["RealName"])) ? cleanInput($_POST["RealName"]): "";
        $tempEmail = (isset($_POST["Email"])) ? cleanInput($_POST["Email"]): "";
        $tempPass = (isset($_POST["Password"])) ? cleanInput($_POST["Password"]): "";
        $tempRPass = (isset($_POST["ReconfirmPassword"])) ? cleanInput($_POST["ReconfirmPassword"]): "";

        $tempID = $tempHash = "";

        if (
            empty($tempName) ||
            empty($tempRName) ||
            empty($tempEmail) ||
            empty($tempPass) ||
            empty($tempRPass)
        ) {
            $registrationMsg = "* Fill in ALL Fields! *";
            $passRegistration = false;
        }
        else {
            // Set to true at first.
            $passRegistration = true;

            // Check Username.
            if (checkExistUsername($conn, $tempName)) {
                $registrationMsg = "* Username is used by another user! *";
                $passRegistration = false;
            }

            // Check Email.
            if ($passRegistration && checkExistEmail($conn, $tempEmail)) {
                $registrationMsg = "* Email is used by another user! *";
                $passRegistration = false;
            }

            // Check Password.
            if ($passRegistration && empty($tempHash = checkReconfirmPassword($tempPass, $tempRPass))) {
                $registrationMsg = "* Reenter the EXACT SAME Password! *";
                $passRegistration = false;
            }

            // Insert to DB.
            if ($passRegistration) {
                $tempNameEscaped = $conn->real_escape_string($tempName);
                $tempEmailEscaped = $conn->real_escape_string($tempEmail);
                $tempRNameEscaped = $conn->real_escape_string($tempRName);

                // Insert to User table with UserType AD.
                $query = "INSERT INTO `User`(`Username`, `Email`, `PasswordHash`, `RealName`, `UserType`)";
                $query .= " VALUES ('$tempNameEscaped','$tempEmailEscaped','$tempHash','$tempRNameEscaped','AD')";

                $rs = $conn->query($query);
                if (!$rs) {
                    $registrationMsg = "* Fail to insert to User table! *";
                    $passRegistration = false;
                }

                // Insert to Admin table.
                if ($passRegistration) {
                    $passRegistration = false;

                    // Get UserID.
                    $tempID = $conn->insert_id;

                    // Insert with the obtained UserID.
                    $query = "INSERT INTO `Admin`(`UserID`)";
                    $query .= " VALUES ('$tempID')";
                    $rs = $conn->query($query);

                    if (!$rs) {
                        $registrationMsg = "* Fail to insert to Admin table! *";
                    }
                    else {
                        $passRegistration = true;
                    }
                }

                // Check if the data is successfully inserted.
                if ($passRegistration) {
                    // Reset to empty.
                    $tempName = $tempRName = $tempEmail = $tempPass = $tempRPass = "";
                    $registrationMsg = "* User is successfully registered! *";
                }
            }
        }
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
                    <h1>Admin Sign Up</h1>
                </div>
                <div id="formContentW2">
                    <img class="fadeIn first" src="/img/defaults/adminIcon.png" id="icon" alt="Comp Icon" />

                    <form method="post" action="/Admin/registerAdmin.php">
                        <table>
                            <tr>
                                <td colspan="2">
                                    <span class="<?php
                                        echo(($passRegistration) ? "success": "error");
                                    ?>-message"><?php
                                        echo($registrationMsg);
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
                                            Password:
                                        </label><br>
                                        <input id="Password" type="password" name="Password" placeholder="Password" required>
                                    </div>
                                </td>

                                <!-- ReconfirmPassword -->
                                <td>
                                    <div>
                                        <label for="ReconfirmPassword">
                                            Reconfirm Password:
                                        </label><br>
                                        <input id="ReconfirmPassword" type="password" name="ReconfirmPassword" placeholder="Reconfirm Password" required>
                                    </div>
                                </td>
                            </tr>

                            <tr class="fadeIn fifth">
                                <td colspan="2">
                                    <div>
                                        <br>
                                        <input type="submit" value="Sign Up">
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </form>
                    <br>
                    <div id="formFooter">
                        <h2><a class="underlineHover" href="/Admin/manageAdmin.php">Back to Manage Admin</a><h2><br>
                    </div>
                    
                </div>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
