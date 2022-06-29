<?php
    // Client Registration Page.
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dbConnection.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/loginAuthenticate.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/inputValidation.php");

    $tempLoginCheck = checkLogin($conn);
    // Not logged in as Admin.
    if ($tempLoginCheck != 4) {
        header("Location: /index.php");
        exit;
    }

    $tempPFP = $tempName = $tempRName = $tempEmail = $tempPass = $tempRPass = $tempCountry = $tempAddress = "";
    $registrationMsg = "";
    $passRegistration = false;

    // Registration attempt.
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $tempName = (isset($_POST["Username"])) ? cleanInput($_POST["Username"]): "";
        $tempRName = (isset($_POST["RealName"])) ? cleanInput($_POST["RealName"]): "";
        $tempEmail = (isset($_POST["Email"])) ? cleanInput($_POST["Email"]): "";
        $tempPass = (isset($_POST["Password"])) ? cleanInput($_POST["Password"]): "";
        $tempRPass = (isset($_POST["ReconfirmPassword"])) ? cleanInput($_POST["ReconfirmPassword"]): "";
        $tempCountry = (isset($_POST["Country"])) ? cleanInput($_POST["Country"]): "";
        $tempAddress = (isset($_POST["Address"])) ? cleanInput($_POST["Address"]): "";

        $tempID = $tempHash = "";

        if (
            empty($tempName) ||
            empty($tempRName) ||
            empty($tempEmail) ||
            empty($tempPass) ||
            empty($tempRPass) ||
            empty($tempCountry) ||
            empty($tempAddress) 
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

            $newImageName = $imageName = $imageFileType = "";
            // Target path to store image.
            $fullPath = $targetImagePath = "/img/client/";
            $imageSaved = false;

            // Check image.
            if ($passRegistration) {
                // Only allow PNG or JPG.
                $allowImageType = array('png', 'jpeg', 'jpg');
                $imageName = (isset($_FILES['ClientPfp']['name'])) ? cleanInput($_FILES['ClientPfp']['name']): "";
                $imageName = str_replace(" ", "_", $imageName);

                // Get image type.
                $imageFileType = (!empty($imageName)) ? pathinfo($imageName, PATHINFO_EXTENSION): "";
                
                // Check if image is provided (actual max is 2 MiB).
                if (
                    !isset($_FILES['ClientPfp']) ||
                    $_FILES['ClientPfp']['size'] < 1 || $_FILES['ClientPfp']['size'] > 2097152 ||
                    !in_array($imageFileType, $allowImageType)
                ) {
                    $registrationMsg = "* Upload a Profile Picture (Max: 2 MB; Only PNG or JPG)!";
                    $passRegistration = false;
                }
                // Try to create folder if not exist, remember to add root path.
                elseif (!is_dir($_SERVER['DOCUMENT_ROOT'] . $targetImagePath)) {
                    mkdir($_SERVER['DOCUMENT_ROOT'] . $targetImagePath, 0777, true);
                }
            }

            // Insert to DB.
            if ($passRegistration) {
                // Insert to User table with UserType CO.
                $query = "INSERT INTO `User`(`Username`, `Email`, `PasswordHash`, `RealName`, `UserType`)";
                $query .= " VALUES ('$tempName','$tempEmail','$tempHash','$tempRName','CL')";

                $rs = $conn->query($query);
                if (!$rs) {
                    $registrationMsg = "* Fail to insert to User table! *";
                    $passRegistration = false;
                }

                // Insert to Client table.
                if ($passRegistration) {
                    $passRegistration = false;

                    // Get UserID.
                    $tempID = $conn->insert_id;

                    // Append image name to full path.
                    if ($_FILES["ClientPfp"]["error"] == 0) {
                        date_default_timezone_set('Asia/Kuala_Lumpur');

                        $newImageName = "clientID" . $tempID . "_" . date('Y-m-d') . "_" . round(microtime(true));
                        $newImageName .= "." . $imageFileType;
                        $fullPath .= $newImageName;
                    }
                    else {
                        $fullPath .= "default_client.jpg";
                    }
                    
                    // Insert with the obtained UserID.
                    $query = "INSERT INTO `Client`(`UserID`, `Country`,`Address`,`Photo`)";
                    $query .= " VALUES ('$tempID','$tempCountry','$tempAddress','$fullPath')";
                    $rs = $conn->query($query);

                    if (!$rs) {
                        $registrationMsg = "* Fail to insert to Client table! *";
                    }
                    else {
                        $passRegistration = $imageSaved = true;

                        if (
                            $_FILES["ClientPfp"]["error"] == 0 &&
                            !move_uploaded_file($_FILES["ClientPfp"]["tmp_name"], $_SERVER['DOCUMENT_ROOT'] . $fullPath)
                        ) {
                            $imageSaved = false;
                        }
                    }
                }

                // Check if the data is successfully inserted.
                if ($passRegistration) {
                    // Reset to empty.
                    $tempPFP = $tempName = $tempRName = $tempEmail = $tempPass = $tempRPass = $tempEDate = $tempCountry = $tempAddress = "";

                    if ($imageSaved) {
                        $registrationMsg = "* User is successfully registered! *";
                    }
                    else {
                        $registrationMsg = "* User is registered but there's an error saving image! *";
                        $passRegistration = false;
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
        <title>Admin: Manage Client Page</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">
        
        <link rel="stylesheet" href="/css/main.css">
        <link rel="stylesheet" href="/css/form.css">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <link rel="stylesheet" href="https://www.w3schools.com/lib/w3-colors-vivid.css">
        <!--<link rel="shortcut icon" href="/favicon.ico">-->
        <link rel="shortcut icon" href="https://icon-library.com/images/tree-icon/tree-icon-23.jpg">

        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            $(function(){
                $("#Country").select2();
            }); 
        </script>
    </head>

    <body>
        <header>
            <div class="maintheme w3-container">
                <h4 style="font-size: 36px">Admin: Manage Client Page</h4>
            </div>
        </header>

        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Admin/navigationBar.php"); ?>

        <main>
            <div class="wrapper fadeInDown">
                <div id="formHeader">
                    <h1>Client Sign Up:</h1>
                </div>
                <div id="formContentW2">
                    <br>
                    <img class="fadeIn first" src="https://png.pngtree.com/png-vector/20190721/ourlarge/pngtree-business-meeting-with-client-illustration-concept-modern-flat-design-concept-png-image_1567633.jpg" id="icon" alt="Comp Icon" />
                    <br>
                    <form method="post" action="/Admin/registerClient.php" enctype="multipart/form-data">
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
                                <!-- Profile Pic -->
                                <td colspan="2">
                                    <div>
                                        <label for="ClientPfp">
                                            Profile Picture:
                                        </label><br>
                                        <input type="file" id="ClientPfp" name="ClientPfp" accept="image/png, image/jpg, image/jpeg" required>
                                    </div>
                                </td>
                            </tr>

                            <tr class="fadeIn third">
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
                                            Full Name:
                                        </label><br>
                                        <input id="RealName" type="text" name="RealName" value="<?php
                                            echo($tempRName);
                                        ?>" placeholder="Full Name" required>
                                    </div>
                                </td>
                            </tr>

                            <tr class="fadeIn fourth">
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

                            <tr class="fadeIn fifth">
                                <!-- Address -->
                                <td colspan="2">
                                    <div>
                                        <label for="Address">
                                            Address:
                                        </label><br>
                                        <textarea id="Address" name="Address" placeholder="Address" required><?php
                                            echo($tempAddress);
                                        ?></textarea>
                                    </div>
                                </td>
                            </tr>

                            <tr class="fadeIn sixth">
                                <!-- Country Select -->
                                <td colspan="2">
                                    <div>
                                        <label for="Country">
                                            Country:
                                        </label><br>
                                        <select id="Country" type="select" name="Country" placeholder="Country" required>
                                            <option value="">Select your country</option>
                                            <?php include($_SERVER['DOCUMENT_ROOT'] . "/Admin/countryOption.php"); ?>
                                        </select>
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
                                    <div>
                                        <br>
                                        <input type="submit" value="Sign Up">
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </form>
                    <div id="formFooter">
                        <h2><a class="underlineHover" href="/Admin/manageClient.php">Back to Manage Client</a><h2><br>
                    </div>
                </div>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>