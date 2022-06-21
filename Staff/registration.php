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

    $tempName = $tempRName = $tempEmail = $tempPass = $tempRPass = $tempSalary = $tempEDate = $isManager = "";
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
            !isset($_POST["isManager"]) || empty($_POST["isManager"]))
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
            $isManager = cleanInput($_POST["isManager"]);

            $tempID = $tempHash = "";

            if (
                empty($tempName) ||
                empty($tempRName) ||
                empty($tempEmail) ||
                empty($tempPass) ||
                empty($tempRPass) ||
                empty($tempEDate) ||
                empty($tempSalary) ||
                empty($isManager)
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
                                $query = "INSERT INTO `Staff`(`UserID`, `EmployDate`, `Salary`, `isManager`)";
                                $query .= " VALUES ('$tempID','$tempEDate', '$tempSalary', '$isManager')";
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
                        $tempName = $tempRName = $tempEmail = $tempPass = $tempRPass = $tempSalary = $tempEDate = $isManager = "";
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
        
        <link rel="stylesheet" href="/css/main.css">
        <link rel="shortcut icon" href="/favicon.ico">
    </head>

    <body>
        <header>
            <h1>Staff: Registration Page</h1>
        </header>

        <main>
            <span><?php
                echo($registrationMsg);
            ?></span>
            <form method="post" action="/Staff/registration.php">
                <table>
                    <tr>
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

                    <tr>
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

                    <tr>
                        <!-- isManager -->
                        <td>
                            <div>
                                <label for="isManager">
                                    Is he/she a manager?
                                </label><br>
                                <input id="isManager_true" type="radio" name="isManager" required>
                                <label for="isManager_true">Yes</label>
                                <input id="isManager_false" type="radio" name="isManager" required>
                                <label for="isManager_false">No</label>
                            </div>
                        </td>
                    </tr>

                    <tr>
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

                    <tr>
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
                    
                    <tr>
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

                    <tr>
                        <td>
                            <div>
                                <button type="submit">
                                    Register Now
                                </button>
                            </div>
                        </td>
                    </tr>
                </table>
            </form>
            <br>

            <a href="/login.php?UserType=ST">Back to Login</a><br>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
