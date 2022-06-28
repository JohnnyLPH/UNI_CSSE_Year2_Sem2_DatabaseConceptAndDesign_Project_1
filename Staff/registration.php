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

    $tempName = $tempRName = $tempEmail = $tempPass = $tempRPass = $tempSalary = $tempEDate = $tempCompany = "";
    $registrationMsg = "";
    $passRegistration = false;

    // Registration attempt.
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

    function getCompanies($conn) {
        $sql = "SELECT User.UserID, User.RealName FROM User INNER JOIN Company USING(UserID);";
        $result = $conn->query($sql);

        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo ("<option value=\" " . $row["UserID"] . "\"> " . $row["RealName"] . "</option>");
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Staff: Registration Page</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">
        
        <link rel="stylesheet" href="/css/form.css">
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
                    
                    <form method="post" action="/Staff/registration.php">
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
                                            <?php 
                                                getCompanies($conn);    
                                                $conn->close(); 
                                            ?>
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
                                        ?>" placeholder="Salary" required>
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
