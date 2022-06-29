<?php
    // Admin Manage Staff Page.
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

    $allStaff = NULL;
    // Staff is not available for editing.
    if (
        !isset($queryString["StaffID"]) ||
        !is_numeric($queryString["StaffID"]) ||
        $queryString["StaffID"] < 1 ||
        count($allStaff = getAllStaff($conn, 0, $queryString["StaffID"])) < 1
    ) {
        header("Location: /Admin/manageStaff.php");
        exit;
    }

    $staffID = $queryString["StaffID"];
    $result = $allStaff[0];

    $tempName = $tempRName = $tempEmail = $tempPass = $tempRPass = $tempEDate = $tempSalary = "";
    $editMsg = "";
    $passEditing = false;

    // Edit attempt.
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $tempName = (isset($_POST["Username"])) ? cleanInput($_POST["Username"]): "";
        $tempRName = (isset($_POST["RealName"])) ? cleanInput($_POST["RealName"]): "";
        $tempEmail = (isset($_POST["Email"])) ? cleanInput($_POST["Email"]): "";
        $tempPass = (isset($_POST["Password"])) ? cleanInput($_POST["Password"]): "";
        $tempRPass = (isset($_POST["ReconfirmPassword"])) ? cleanInput($_POST["ReconfirmPassword"]): "";
        $tempEDate = (isset($_POST["EmployDate"])) ? cleanInput($_POST["EmployDate"]): "";
        $tempSalary = (isset($_POST["Salary"])) ? cleanInput($_POST["Salary"]): "";

        $tempHash = "";

        if (
            empty($tempName) ||
            empty($tempRName) ||
            empty($tempEmail) ||
            empty($tempEDate) ||
            empty($tempSalary)
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

            // Check Salary.
            if ($passEditing && (!is_numeric($tempSalary) || $tempSalary < 500)) {
                $editMsg = "* Valid salary (>= 500)! *";
                $tempSalary = "";
                $passEditing = false;
            }

            $checkCompany = getAllCompany($conn, $result["CompanyID"]);

            // Check EmployDate.
            if ($passEditing) {
                // From DateTime to Date.
                $checkDate = new DateTime($checkCompany[0]["EstablishDate"]);
                $checkDate = $checkDate->format('Y-m-d');

                if ($tempEDate < $checkDate) {
                    $tempCompID = $checkCompany[0]["UserID"];
                    $editMsg = "* Invalid Employment Date, Company ID $tempCompID is established on $checkDate! *";
                    $tempEDate = "";
                    $passEditing = false;
                }
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
                // Update in Staff table.
                $query = "UPDATE `Staff`";
                $query .= " SET `EmployDate`='$tempEDate'";
                $query .= ", `Salary`='$tempSalary'";
                $query .= " WHERE `Staff`.`UserID`='$staffID';";

                $rs = $conn->query($query);
                if (!$rs) {
                    $editMsg = "* Fail to update in Staff table! *";
                    $passEditing = false;
                }

                if ($passEditing) {
                    // Update in User table.
                    $query = "UPDATE `User`";
                    $query .= " SET `Username`='$tempName'";
                    $query .= ", `Email`='$tempEmail'";
    
                    if ($editPass) {
                        $query .= ", `PasswordHash`='$tempHash'";
                    }
    
                    $query .= ", `RealName`='$tempRName'";
                    $query .= " WHERE `User`.`UserID`='$staffID';";
    
                    $rs = $conn->query($query);
                    if (!$rs) {
                        $editMsg = "* Fail to update in User table! *";
                        $passEditing = false;
                    }
                }

                // Check if the data is successfully updated.
                if ($passEditing) {
                    $editMsg = "* Staff is successfully updated! *";
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
        $tempEDate = new DateTime($result["EmployDate"]);
        $tempEDate = $tempEDate->format('Y-m-d');
    }

    if (empty($tempSalary)) {
        $tempSalary = $result["Salary"];
    }

    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Admin: Manage Staff Page</title>
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
                <h4 style="font-size: 36px">Admin: Manage Staff Page</h4>
            </div>
        </header>

        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Admin/navigationBar.php"); ?>

        <main>
            <div class="wrapper fadeInDown">
                <div id="formHeader">
                    <h1>Edit Staff ID <?php
                        echo($staffID);
                    ?>:</h1>
                </div>
                <div id="formContentW2">
                    <img class="fadeIn first" src="https://thumbs.dreamstime.com/b/call-center-customer-support-hotline-operator-advises-client-online-technical-vector-illustration-139728240.jpg" id="icon" alt="Staff Icon" />

                    <form method="post" action="/Admin/editStaff.php?StaffID=<?php
                        echo($staffID);
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
                                            Staff Name:
                                        </label><br>
                                        <input id="RealName" type="text" name="RealName" value="<?php
                                            echo($tempRName);
                                        ?>" placeholder="Staff Name" required>
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
                                <!-- Salary -->
                                <td>
                                    <div>
                                        <label for="Salary">
                                            Salary (RM):
                                        </label><br>
                                        <input id="Salary" type="number" name="Salary" value="<?php
                                            echo($tempSalary);
                                        ?>" placeholder="Salary" step=".01" min="500" required>
                                    </div>
                                </td>

                                <!-- EmployDate -->
                                <td>
                                    <div>
                                        <label for="EmployDate">
                                            Employment Date:
                                        </label><br>
                                        <input id="EmployDate" type="date" name="EmployDate" value="<?php
                                            echo($tempEDate);
                                        ?>" placeholder="Employment Date" required>
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
                        <h2><a class="underlineHover" href="/Admin/viewEachStaff.php?StaffID=<?php
                            echo($staffID);
                        ?>">Back to View Staff</a><h2><br>
                    </div>
                </div>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
