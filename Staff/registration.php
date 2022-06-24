<?php
    // Staff Registration Page.
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dbConnection.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/loginAuthenticate.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/inputValidation.php");

    $tempLoginCheck = checkLogin($conn);
    // Logged in.
    if ($tempLoginCheck != 0) {
        header("Location: /index.php");
        exit;
    }

    $tempName = $tempRName = $tempEmail = $tempPass = $tempRPass = $tempSalary = $tempEDate = "";
    $tempCompany = 14;
    $registrationMsg = "";
    $passRegistration = false;

    // Registration attempt.
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (
            !isset($_POST["Username"]) || empty($_POST["Username"]) ||
            !isset($_POST["RealName"]) || empty($_POST["RealName"]) ||
            !isset($_POST["Email"]) || empty($_POST["Email"]) ||
            !isset($_POST["Password"]) || empty($_POST["Password"]) ||
            !isset($_POST["ReconfirmPassword"]) || empty($_POST["ReconfirmPassword"]) ||
            !isset($_POST["EmploymentDate"]) || empty($_POST["EmploymentDate"] ||
            !isset($_POST["tempSalary"]) || empty($_POST["tempSalary"]) ||
            !isset($_POST["tempCompany"]) || empty($_POST["tempCompany"]))
        ) {
            $registrationMsg = "* Fill in ALL Fields! *";
        }
        else {
            $tempName = cleanInput($_POST["Username"]);
            $tempRName = cleanInput($_POST["RealName"]);
            $tempEmail = cleanInput($_POST["Email"]);
            $tempPass = cleanInput($_POST["Password"]);
            $tempRPass = cleanInput($_POST["ReconfirmPassword"]);
            $tempEDate = cleanInput($_POST["EmploymentDate"]);
            $tempSalary = cleanInput($_POST["tempSalary"]);
            // $tempCompany = cleanInput($_POST["tempCompany"]);

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

                        // Get UserID from UserTable.
                        $query = "SELECT `UserID` FROM `User` WHERE `Username` = '$tempName'";
                        $rs = $conn->query($query);
                        if ($rs) {
                            if ($user = mysqli_fetch_assoc($rs)) {
                                $tempID = $user["UserID"];
                                
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
                        }
                    }

                    // Check if the data is successfully inserted.
                    if ($passRegistration) {
                        // Reset to empty.
                        $tempName = $tempRName = $tempEmail = $tempPass = $tempRPass = $tempSalary = $tempEDate = $tempCompany = "";
                        $registrationMsg = "* User is successfully registered and can be used for login! *";
                    }
                }
            }
        }
    }

    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Staff: Registration Page</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">
        
        <link rel="stylesheet" href="/css/login.css">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <!--<link rel="shortcut icon" href="/favicon.ico">-->
        <link rel="shortcut icon" href="https://icon-library.com/images/tree-icon/tree-icon-23.jpg">
    </head>

    <body>
        <header>
            <!--<h1>Staff: Registration Page</h1>-->
        </header>

        <main>
            <div class="wrapper fadeInDown">
                <div id="formHeader">
                    <h1>Staff Sign Up</h1>
                </div>

                <div id="formContentW2">
                    <img class="fadeIn first" src="https://thumbs.dreamstime.com/b/call-center-customer-support-hotline-operator-advises-client-online-technical-vector-illustration-139728240.jpg" id="icon" alt="Comp Icon" />
                    
                    <span><?php
                    echo($registrationMsg);
                    ?></span>
                    
                    <form method="post" action="/Staff/registration.php">
                        <table>
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
                            </tr>

                            <!-- <tr>
                                <td>
                                    <div>
                                        <label for="tempCompany">
                                            Company?
                                        </label><br>
                                        <input id="tempCompany_true" type="radio" name="tempCompany" required>
                                        <label for="tempCompany_true">A</label>
                                        <input id="tempCompany_false" type="radio" name="tempCompany" required>
                                        <label for="tempCompany_false">B</label>
                                    </div>
                                </td>
                            </tr> -->

                            <tr class="fadeIn fourth">
                                <!-- Salary -->
                                <td>
                                    <div>
                                        <label for="tempSalary">
                                            Salary:
                                        </label><br>
                                        <input id="tempSalary" type="number" name="tempSalary" value="<?php
                                            echo($tempSalary);
                                        ?>" placeholder="Salary" required>
                                    </div>
                                </td>
                            </tr>

                            <tr class="fadeIn fifth">
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
                            
                            <tr class="fadeIn sixth">
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
                                    <input type="submit" value="Sign Up"></input>
                                </td>
                            </tr>
                        </table>
                    </form>

                    <div id="formFooter">
                        <h2><a class="underlineHover" href="/login.php?UserType=ST">Back to Login</a></h2><br>
                    </div>
                </div>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
