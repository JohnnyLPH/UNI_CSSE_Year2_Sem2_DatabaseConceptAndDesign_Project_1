<?php
    // Staff Registration Page.
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

    $tempName = $tempRName = $tempEmail = $tempPass = $tempRPass = $tempSalary = $tempEDate = $tempCompany = "";
    $registrationMsg = "";
    $passRegistration = true;

    $allCompany = getAllCompany($conn);

    // Disable registration if no existing company.
    if (count($allCompany) < 1) {
        $registrationMsg = "* Registration is not allowed as there is no existing company! *";
        $passRegistration = false;
    }

    // Registration attempt.
    if ($passRegistration && $_SERVER["REQUEST_METHOD"] == "POST") {
        $tempName = (isset($_POST["Username"])) ? cleanInput($_POST["Username"]): "";
        $tempRName = (isset($_POST["RealName"])) ? cleanInput($_POST["RealName"]): "";
        $tempEmail = (isset($_POST["Email"])) ? cleanInput($_POST["Email"]): "";
        $tempPass = (isset($_POST["Password"])) ? cleanInput($_POST["Password"]): "";
        $tempRPass = (isset($_POST["ReconfirmPassword"])) ? cleanInput($_POST["ReconfirmPassword"]): "";
        $tempEDate = (isset($_POST["EmploymentDate"])) ? cleanInput($_POST["EmploymentDate"]): "";
        $tempSalary = (isset($_POST["tempSalary"])) ? cleanInput($_POST["tempSalary"]): "";
        $tempCompany = (isset($_POST["tempCompany"])) ? cleanInput($_POST["tempCompany"]): "";

        $tempID = $tempHash = "";

        if (
            empty($tempName) ||
            empty($tempRName) ||
            empty($tempEmail) ||
            empty($tempPass) ||
            empty($tempRPass) ||
            empty($tempEDate) ||
            empty($tempSalary) ||
            empty($tempCompany)
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

            $checkCompany = getAllCompany($conn, $tempCompany);

            // Check valid CompanyID.
            if ($passRegistration && count($checkCompany) < 1) {
                $registrationMsg = "* Choose an existing Company! *";
                $passRegistration = false;
            }

            // Check EmployDate.
            if ($passRegistration) {
                // From DateTime to Date.
                $checkDate = new DateTime($checkCompany[0]["EstablishDate"]);
                $checkDate = $checkDate->format('Y-m-d');

                if ($tempEDate < $checkDate) {
                    $registrationMsg = "* Invalid Employment Date, Company ID $tempCompany is established on $checkDate! *";
                    $tempEDate = $checkDate;
                    $passRegistration = false;
                }
            }

            // Check Password.
            if ($passRegistration && empty($tempHash = checkReconfirmPassword($tempPass, $tempRPass))) {
                $registrationMsg = "* Reenter the EXACT SAME Password! *";
                $passRegistration = false;
            }

            // Insert to DB.
            if ($passRegistration) {
                // Insert to User table with UserType CO.
                $query = "INSERT INTO `User`(`Username`, `Email`, `PasswordHash`, `RealName`, `UserType`)";
                $query .= " VALUES ('$tempName','$tempEmail','$tempHash','$tempRName','ST')";

                $rs = $conn->query($query);
                if (!$rs) {
                    $registrationMsg = "* Fail to insert to User table! *";
                    $passRegistration = false;
                }

                // Insert to Staff table.
                if ($passRegistration) {
                    $passRegistration = false;

                    // Get UserID.
                    $tempID = $conn->insert_id;

                    // Insert with the obtained UserID.
                    $query = "INSERT INTO `Staff`(`UserID`, `EmployDate`, `Salary`, `CompanyID`)";
                    $query .= " VALUES ('$tempID','$tempEDate', '$tempSalary', '$tempCompany')";
                    $rs = $conn->query($query);

                    if (!$rs) {
                        $registrationMsg = "* Fail to insert to Staff table! *";
                    }
                    else {
                        $passRegistration = true;
                    }
                }

                // Check if the data is successfully inserted.
                if ($passRegistration) {
                    // Reset to empty.
                    $tempName = $tempRName = $tempEmail = $tempPass = $tempRPass = $tempSalary = $tempEDate = $tempCompany = "";
                    $registrationMsg = "* User is successfully registered! *";
                }
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Admin: Manage Staff Page</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">
        
        <link rel="stylesheet" href="/css/main.css">
        <link rel="stylesheet" href="/css/form.css">
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
                    <h1>Staff Sign Up:</h1>
                </div>

                <div id="formContentW2">
                    <img class="fadeIn first" src="https://thumbs.dreamstime.com/b/call-center-customer-support-hotline-operator-advises-client-online-technical-vector-illustration-139728240.jpg" id="icon" alt="Comp Icon" />
                    
                    <form method="post" action="/Admin/registerStaff.php">
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

                                <!-- Company -->
                                <td>
                                    <div>
                                        <label for="tempCompany">
                                            Company:
                                        </label><br>
                                        <select id="tempCompany" name="tempCompany">
                                            <?php foreach ($allCompany as $result): ?>
                                                <option value="<?php
                                                    echo($result["UserID"]);
                                                ?>"<?php
                                                    if ($tempCompany == $result["UserID"]) {
                                                        echo(" selected");
                                                    }
                                                ?>><?php
                                                    echo($result["UserID"] . " - " . $result["RealName"]);
                                                ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </td>
                            </tr>

                            <tr class="fadeIn fourth">
                                <!-- Salary -->
                                <td>
                                    <div>
                                        <label for="tempSalary">
                                            Salary:
                                        </label><br>
                                        <input id="tempSalary" type="number" name="tempSalary" value="<?php
                                            echo($tempSalary);
                                        ?>" placeholder="Salary" step=".01" min="500" required>
                                    </div>
                                </td>

                                <!-- EmploymentDate -->
                                <td>
                                    <div>
                                        <label for="EmploymentDate">
                                            Employment Date:
                                        </label><br>
                                        <input id="EmploymentDate" type="date" name="EmploymentDate" value="<?php
                                            echo($tempEDate);
                                        ?>" placeholder="Employment Date" required>
                                    </div>
                                </td>
                            </tr>
                            
                            <tr class="fadeIn fifth">
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

                            <tr class="fadeIn sixth">
                                <td colspan="2">
                                    <br>
                                    <input type="submit" value="Sign Up"<?php
                                        if (count($allCompany) < 1) {
                                            echo(" disabled");
                                        }
                                    ?>>
                                </td>
                            </tr>
                        </table>
                    </form>

                    <div id="formFooter">
                        <h2><a class="underlineHover" href="/Admin/manageStaff.php">Back to Manage Staff</a></h2><br>
                    </div>
                </div>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
