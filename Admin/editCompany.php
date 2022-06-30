<?php
    // Admin Manage Company Page.
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

    $allCompany = NULL;
    // Company is not available for editing.
    if (
        !isset($queryString["CompanyID"]) ||
        !is_numeric($queryString["CompanyID"]) ||
        $queryString["CompanyID"] < 1 ||
        count($allCompany = getAllCompany($conn, $queryString["CompanyID"])) < 1
    ) {
        header("Location: /Admin/manageCompany.php");
        exit;
    }

    $companyID = $queryString["CompanyID"];
    $result = $allCompany[0];

    $tempName = $tempRName = $tempEmail = $tempPass = $tempRPass = $tempEDate = "";
    $editMsg = "";
    $passEditing = false;

    // Edit attempt.
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $tempName = (isset($_POST["Username"])) ? cleanInput($_POST["Username"]): "";
        $tempRName = (isset($_POST["RealName"])) ? cleanInput($_POST["RealName"]): "";
        $tempEmail = (isset($_POST["Email"])) ? cleanInput($_POST["Email"]): "";
        $tempPass = (isset($_POST["Password"])) ? cleanInput($_POST["Password"]): "";
        $tempRPass = (isset($_POST["ReconfirmPassword"])) ? cleanInput($_POST["ReconfirmPassword"]): "";
        $tempEDate = (isset($_POST["EstablishDate"])) ? cleanInput($_POST["EstablishDate"]): "";

        $tempHash = "";

        if (
            empty($tempName) ||
            empty($tempRName) ||
            empty($tempEmail) ||
            empty($tempEDate)
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

            // Update in DB.
            if ($passEditing) {
                // Update in Company table.
                $query = "UPDATE `Company`";
                $query .= " SET `EstablishDate`='$tempEDate'";
                $query .= " WHERE `Company`.`UserID`='$companyID';";

                $rs = $conn->query($query);
                if (!$rs) {
                    $editMsg = "* Fail to update in Company table! *";
                    $passEditing = false;
                }

                if ($passEditing) {
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
                    $query .= " WHERE `User`.`UserID`='$companyID';";
    
                    $rs = $conn->query($query);
                    if (!$rs) {
                        $editMsg = "* Fail to update in User table! *";
                        $passEditing = false;
                    }
                }
                
                // Check if the data is successfully updated.
                if ($passEditing) {
                    $editMsg = "* Company is successfully updated! *";
                }
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

    if (empty($tempEDate)) {
        // From DateTime to Date.
        $tempEDate = new DateTime($result["EstablishDate"]);
        $tempEDate = $tempEDate->format('Y-m-d');
    }

    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Admin: Manage Company Page</title>
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
                <h4 style="font-size: 36px">Admin: Manage Company Page</h4>
            </div>
        </header>

        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Admin/navigationBar.php"); ?>

        <main>
            <div class="wrapper fadeInDown">
                <div id="formHeader">
                    <h1>Edit Company ID <?php
                        echo($companyID);
                    ?>:</h1>
                </div>
                <div id="formContentW2">
                    <img class="fadeIn first" src="/img/defaults/companyIcon.jpg" id="icon" alt="Company Icon" />

                    <form method="post" action="/Admin/editCompany.php?CompanyID=<?php
                        echo($companyID);
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
                                            Company Name:
                                        </label><br>
                                        <input id="RealName" type="text" name="RealName" value="<?php
                                            echo($tempRName);
                                        ?>" placeholder="Company Name" required>
                                    </div>
                                </td>
                            </tr>

                            <tr class="fadeIn third">
                                <!-- Email -->
                                <td>
                                    <div>
                                        <label for="Email">
                                            Email:
                                        </label><br>
                                        <input id="Email" type="email" name="Email" value="<?php
                                            echo($tempEmail);
                                        ?>" placeholder="abc@email.com" required>
                                    </div>
                                </td>

                                <!-- EstablishDate -->
                                <td>
                                    <div>
                                        <label for="EstablishDate">
                                            Establish Date:
                                        </label><br>
                                        <input id="EstablishDate" type="date" name="EstablishDate" value="<?php
                                            echo($tempEDate);
                                        ?>" placeholder="Establish Date" required>
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
                        <h2><a class="underlineHover" href="/Admin/viewEachCompany.php?CompanyID=<?php
                            echo($companyID);
                        ?>">Back to View Company</a><h2><br>
                    </div>
                </div>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
