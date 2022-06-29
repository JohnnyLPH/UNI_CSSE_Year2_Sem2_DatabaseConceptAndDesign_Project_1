<?php
    // Admin Manage Client Page.
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

    $allClient = NULL;
    // Client is not available for editing.
    if (
        !isset($queryString["ClientID"]) ||
        !is_numeric($queryString["ClientID"]) ||
        $queryString["ClientID"] < 1 ||
        count($allClient = getAllClient($conn, $queryString["ClientID"])) < 1
    ) {
        header("Location: /Admin/manageClient.php");
        exit;
    }

    $clientID = $queryString["ClientID"];
    $result = $allClient[0];

    $tempName = $tempRName = $tempEmail = $tempPass = $tempRPass = $tempAddress = $tempCountry = "";
    $editMsg = "";
    $passEditing = false;

    // Edit attempt.
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $tempName = (isset($_POST["Username"])) ? cleanInput($_POST["Username"]): "";
        $tempRName = (isset($_POST["RealName"])) ? cleanInput($_POST["RealName"]): "";
        $tempEmail = (isset($_POST["Email"])) ? cleanInput($_POST["Email"]): "";
        $tempPass = (isset($_POST["Password"])) ? cleanInput($_POST["Password"]): "";
        $tempRPass = (isset($_POST["ReconfirmPassword"])) ? cleanInput($_POST["ReconfirmPassword"]): "";
        $tempAddress = (isset($_POST["Address"])) ? cleanInput($_POST["Address"]): "";
        $tempCountry = (isset($_POST["Country"])) ? cleanInput($_POST["Country"]): "";

        $tempHash = "";

        if (
            empty($tempName) ||
            empty($tempRName) ||
            empty($tempEmail) ||
            empty($tempAddress) ||
            empty($tempCountry)
        ) {
            $editMsg = "* Fill in ALL Fields! *";
            $passEditing = false;
        }
        else {
            // Set to true at first.
            $passEditing = true;
            $editPass = $editImage = false;
            
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

            $oldImagePath = $result["Photo"];
            $newImageName = $imageName = $imageFileType = "";
            // Target path to store image.
            $fullPath = $targetImagePath = "/img/client/";

            // Check image.
            if ($passEditing && isset($_FILES["Photo"]["name"]) && strlen($_FILES["Photo"]["name"]) > 0) {
                $editImage = true;

                // Only allow PNG or JPG.
                $allowImageType = array('png', 'jpeg', 'jpg');
                $imageName = (isset($_FILES["Photo"]["name"])) ? cleanInput($_FILES["Photo"]["name"]): "";
                $imageName = str_replace(" ", "_", $imageName);

                // Get image type.
                $imageFileType = (!empty($imageName)) ? pathinfo($imageName, PATHINFO_EXTENSION): "";

                // New image name.
                date_default_timezone_set('Asia/Kuala_Lumpur');
                $newImageName = "clientID" . $clientID . "_" . date('Y-m-d') . "_" . round(microtime(true));
                $newImageName .= "." . $imageFileType;
                $fullPath .= $newImageName;
                
                // Check if image is provided (actual max is 2 MiB).
                if (
                    $_FILES["Photo"]["size"] < 1 || $_FILES["Photo"]["size"] > 2097152 ||
                    !in_array($imageFileType, $allowImageType)
                ) {
                    $editMsg = "* Invalid Profile Picture (Max: 2 MB; Only PNG or JPG)!";
                    $passEditing = false;
                }
                // Check if file already exist.
                elseif ($oldImagePath == ($targetImagePath . $imageName)) {
                    $editMsg = "* Image with same name is currently in use!";
                    $passEditing = false;
                }
                // Try to create folder if not exist, remember to add root path.
                elseif (!is_dir($_SERVER['DOCUMENT_ROOT'] . $targetImagePath)) {
                    mkdir($_SERVER['DOCUMENT_ROOT'] . $targetImagePath, 0777, true);
                }

                // Move image to folder.
                if ($passEditing) {
                    if (
                        $_FILES["Photo"]["error"] == 0 &&
                        !move_uploaded_file($_FILES["Photo"]["tmp_name"], $_SERVER['DOCUMENT_ROOT'] . $fullPath)
                    ) {
                        $passEditing = false;
                        $editMsg = "* Error saving Profile Picture!";
                    }
                }
            }

            // Update in DB.
            if ($passEditing) {
                $tempAddressEscaped = $conn->real_escape_string($tempAddress);
                $tempCountryEscaped = $conn->real_escape_string($tempCountry);
                $fullPathEscaped = $conn->real_escape_string($fullPath);

                // Update in Client table.
                $query = "UPDATE `Client`";
                $query .= " SET `Address`='$tempAddressEscaped'";
                $query .= ", `Country`='$tempCountryEscaped'";

                if ($editImage) {
                    $query .= ", `Photo`='$fullPathEscaped'";
                }

                $query .= " WHERE `Client`.`UserID`='$clientID';";

                $rs = $conn->query($query);
                if (!$rs) {
                    $editMsg = "* Fail to update in Client table! *";
                    echo($query);
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
                    $query .= " WHERE `User`.`UserID`='$clientID';";
    
                    $rs = $conn->query($query);
                    if (!$rs) {
                        $editMsg = "* Fail to update in User table! *";
                        $passEditing = false;
                    }
                }

                // Check if the data is successfully updated.
                if ($passEditing) {
                    $editMsg = "* Client is successfully updated! *";

                    // Remove the old image.
                    if (
                        $editImage &&
                        !empty($oldImagePath) &&
                        strlen($oldImagePath) >= strlen($targetImagePath) &&
                        substr($oldImagePath, 0, strlen($targetImagePath)) == $targetImagePath &&
                        $oldImagePath != "/img/client/default_client.jpg"
                    ) {
                        if (file_exists($_SERVER['DOCUMENT_ROOT'] . cleanInput($oldImagePath))) {
                            unlink($_SERVER['DOCUMENT_ROOT'] . cleanInput($oldImagePath));
                        }
                    }
                }
                else {
                    // Remove the new image.
                    if ($editImage) {
                        if (file_exists($_SERVER['DOCUMENT_ROOT'] . cleanInput($fullPath))) {
                            unlink($_SERVER['DOCUMENT_ROOT'] . cleanInput($fullPath));
                        }
                    }
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

    if (empty($tempAddress)) {
        $tempAddress = $result["Address"];
    }

    if (empty($tempCountry)) {
        $tempCountry = $result["Country"];
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
        <link rel="stylesheet" href="/css/formFont.css">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <link rel="stylesheet" href="https://www.w3schools.com/lib/w3-colors-vivid.css">
        <!--<link rel="shortcut icon" href="/favicon.ico">-->
        <link rel="shortcut icon" href="https://icon-library.com/images/tree-icon/tree-icon-23.jpg">
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
                    <h1>Edit Client ID <?php
                        echo($clientID);
                    ?>:</h1>
                </div>
                <div id="formContentW2">
                    <img class="fadeIn first" src="https://png.pngtree.com/png-vector/20190721/ourlarge/pngtree-business-meeting-with-client-illustration-concept-modern-flat-design-concept-png-image_1567633.jpg" id="icon" alt="Client Icon" />

                    <form method="post" action="/Admin/editClient.php?ClientID=<?php
                        echo($clientID);
                    ?>" enctype="multipart/form-data">
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
                                <!-- Photo -->
                                <td colspan="2">
                                    <div>
                                        <label for="Photo">
                                            New Profile Picture (Optional):
                                        </label><br>
                                        <input type="file" id="Photo" name="Photo" accept="image/png, image/jpg, image/jpeg">
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
                                            Client Name:
                                        </label><br>
                                        <input id="RealName" type="text" name="RealName" value="<?php
                                            echo($tempRName);
                                        ?>" placeholder="Client Name" required>
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

                            <tr class="fadeIn fifth">
                                <!-- Country -->
                                <td colspan="2">
                                    <div>
                                        <label for="Country">
                                            Country:
                                        </label><br>
                                        <select id="Country" type="select" name="Country" placeholder="Country" required>
                                            <option value="">Select your country</option>
                                            <?php include($_SERVER['DOCUMENT_ROOT'] . "/Admin/countryOption.php"); ?>
                                            <?php foreach($countryList as $eachCountry): ?>
                                                <option style="<?php
                                                    echo($optionStyle);
                                                ?>" value="<?php
                                                    echo($eachCountry);
                                                ?>"<?php
                                                    if ($tempCountry == $eachCountry) {
                                                        echo(" selected");
                                                    }
                                                ?>><?php
                                                    echo($eachCountry);
                                                ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </td>
                            </tr>

                            <tr class="fadeIn fifth">
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
                        <h2><a class="underlineHover" href="/Admin/viewEachClient.php?ClientID=<?php
                            echo($clientID);
                        ?>">Back to View Client</a><h2><br>
                    </div>
                </div>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
