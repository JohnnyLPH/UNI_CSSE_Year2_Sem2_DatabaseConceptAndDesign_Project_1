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
                // Check Username.
                if (checkExistUsername($conn, $tempName)) {
                    $registrationMsg = "* Username is used by another user! *";
                }

                // $registrationMsg = "* Invalid Registration Credentials! *";
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
        
        <!-- <link rel="stylesheet" href="/css/main.css"> -->
        <!-- <link rel="shortcut icon" href="/favicon.ico"> -->
    </head>

    <body>
        <header>
            <h1>Company: Registration Page</h1>
        </header>

        <main>
            <span><?php
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
                                    if (!empty($tempName)) {
                                        echo($tempName);
                                    }
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
                                    if (!empty($tempRName)) {
                                        echo($tempRName);
                                    }
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
                                    if (!empty($tempEmail)) {
                                        echo($tempEmail);
                                    }
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
                                    if (!empty($tempEDate)) {
                                        echo($tempEDate);
                                    }
                                ?>" placeholder="Establish Date" required>
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

            <a href="/login.php?UserType=CO">Back to Login</a><br>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
