<?php
    // Company Registration Page.
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dbConnection.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/loginAuthenticate.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/inputValidation.php");

    $tempLoginCheck = checkLogin($conn);
    // Logged in.
    if ($tempLoginCheck != 0) {
        header("Location: /index.php");
        exit;
    }
    
    $tempName = $tempRName = $tempEmail = $tempPass = $tempRPass = $tempEDate = "";
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
            !isset($_POST["EstablishDate"]) || empty($_POST["EstablishDate"])
        ) {
            $registrationMsg = "* Fill in ALL Fields! *";
        }
        else {
            $tempName = cleanInput($_POST["Username"]);
            $tempRName = cleanInput($_POST["RealName"]);
            $tempEmail = cleanInput($_POST["Email"]);
            $tempPass = cleanInput($_POST["Password"]);
            $tempRPass = cleanInput($_POST["ReconfirmPassword"]);
            $tempEDate = cleanInput($_POST["EstablishDate"]);

            $tempID = $tempHash = "";

            if (
                empty($tempName) ||
                empty($tempRName) ||
                empty($tempEmail) ||
                empty($tempPass) ||
                empty($tempRPass) ||
                empty($tempEDate)
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
                    $query .= " VALUES ('$tempName','$tempEmail','$tempHash','$tempRName','CO')";

                    $rs = $conn->query($query);
                    if (!$rs) {
                        $registrationMsg = "* Fail to insert to User table! *";
                        $passRegistration = false;
                    }

                    // Insert to Company table.
                    if ($passRegistration) {
                        $passRegistration = false;

                        // Get UserID from UserTable.
                        $query = "SELECT `UserID` FROM `User` WHERE `Username` = '$tempName'";
                        $rs = $conn->query($query);
                        if ($rs) {
                            if ($user = mysqli_fetch_assoc($rs)) {
                                $tempID = $user["UserID"];
                                
                                // Insert with the obtained UserID.
                                $query = "INSERT INTO `Company`(`UserID`, `EstablishDate`)";
                                $query .= " VALUES ('$tempID','$tempEDate')";
                                $rs = $conn->query($query);

                                if (!$rs) {
                                    $registrationMsg = "* Fail to insert to Company table! *";
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
                        $tempName = $tempRName = $tempEmail = $tempPass = $tempRPass = $tempEDate = "";
                        $registrationMsg = "* User is successfully registered! *";
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
        <title>Company: Registration Page</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">
        
        <!--<link rel="stylesheet" href="/css/main.css">-->
        <link rel="stylesheet" href="/css/login.css">
        <link rel="shortcut icon" href="/favicon.ico">
    </head>

    <body>
        <header>
            <!--<h1>Company Sign Up</h1>-->
        </header>

        <main>
            <div class="wrapper fadeInDown">
                <div id="formHeader">
                    <h1>Company Sign Up</h1>
                </div>
                <div id="formContentW2">
                    <img src="https://png.pngtree.com/png-vector/20200124/ourmid/pngtree-client-and-designer-working-together-graphic-design-3d-isometric-illustration-perfect-png-image_2133712.jpg" id="icon" alt="Comp Icon" />

                    <span class="<?php
                        echo(($passRegistration) ? "success": "error");
                    ?>-message"><?php
                        echo($registrationMsg);
                    ?></span>
                    <form method="post" action="/Company/registration.php">
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
                                            Company Name:
                                        </label><br>
                                        <input id="RealName" type="text" name="RealName" value="<?php
                                            echo($tempRName);
                                        ?>" placeholder="Company Name" required>
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

                            <tr>
                                <td colspan="2">
                                    <div>
                                        <br>
                                        <input type="submit" value="Sign Up"></input>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </form>
                    <br>
                    <div id="formFooter">
                        <h2><a class="underlineHover" href="/login.php?UserType=CO">Back to Login</a><br></h2>
                    </div>
                    
                </div>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
