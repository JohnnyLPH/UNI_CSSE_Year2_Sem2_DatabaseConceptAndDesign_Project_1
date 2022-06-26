<?php
    // Admin Manage Orchard Page.
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

    $tempAddress = $tempLatitude = $tempLongitude = $tempCompID = "";
    $addMsg = "";
    $passAdding = false;

    // Registration attempt.
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (
            !isset($_POST["Address"]) || empty($_POST["Address"]) ||
            !isset($_POST["Latitude"]) || empty($_POST["Latitude"]) ||
            !isset($_POST["Longitude"]) || empty($_POST["Longitude"]) ||
            !isset($_POST["CompanyID"]) || empty($_POST["CompanyID"])
        ) {
            $addMsg = "* Fill in ALL Fields! *";
        }
        else {
            $tempAddress = cleanInput($_POST["Address"]);
            $tempLatitude = cleanInput($_POST["Latitude"]);
            $tempLongitude = cleanInput($_POST["Longitude"]);
            $tempCompID = cleanInput($_POST["CompanyID"]);

            if (
                empty($tempAddress) ||
                empty($tempLatitude) ||
                empty($tempLongitude) ||
                empty($tempCompID)
            ) {
                $addMsg = "* Fill in ALL Fields! *";
            }
            else {
                // Set to true at first.
                $passAdding = true;

                // Check Latitude.
                if (!checkValidLatitude($tempLatitude)) {
                    $addMsg = "* Valid latitude (-90 <= x <= 90)! *";
                    $passAdding = false;
                }

                // Check Longitude.
                if ($passAdding && !checkValidLongitude($tempLongitude)) {
                    $addMsg = "* Valid longitude (-180 <= x <= 180)! *";
                    $passAdding = false;
                }

                // Check valid CompanyID.
                if ($passAdding && count(getAllCompany($conn, $tempCompID)) < 1) {
                    $addMsg = "* Choose an existing Company! *";
                    $passAdding = false;
                }

                // Insert to DB.
                if ($passAdding) {
                    // Insert to Orchard table.
                    $query = "INSERT INTO `Orchard`(`Address`, `Latitude`, `Longitude`, `CompanyID`)";
                    $query .= " VALUES ('$tempAddress','$tempLatitude','$tempLongitude','$tempCompID')";

                    $rs = $conn->query($query);
                    if (!$rs) {
                        $addMsg = "* Fail to insert to Orchard table! *";
                        $passAdding = false;
                    }

                    // Check if the data is successfully inserted.
                    if ($passAdding) {
                        // Reset to empty.
                        $tempAddress = $tempLatitude = $tempLongitude = $tempCompID = "";
                        $addMsg = "* Orchard is successfully added! *";
                    }
                }
            }
        }
    }
    $allCompany = getAllCompany($conn);
    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Admin: Manage Orchard Page</title>
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
                <h1>Admin: Manage Orchard Page</h1>
            </div>
        </header>

        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Admin/navigationBar.php"); ?>

        <main>
            <div class="wrapper fadeInDown">
                <div id="formHeader">
                    <h1>Add New Orchard:</h1>
                </div>
                <div id="formContentW2">
                    <img class="fadeIn first" src="https://us.123rf.com/450wm/goodstudio/goodstudio1910/goodstudio191000131/131189697-family-working-in-fruit-garden-together-flat-vector-illustration-people-gathering-apples-berries-and.jpg" id="icon" alt="Orchard Icon" />

                    <form method="post" action="/Admin/addOrchard.php">
                        <table>
                            <tr>
                                <td colspan="2">
                                    <span class="<?php
                                        echo(($passAdding) ? "success": "error");
                                    ?>-message"><?php
                                        echo($addMsg);
                                    ?></span>
                                </td>
                            </tr>

                            <tr class="fadeIn second">
                                <!-- Address -->
                                <td colspan="2">
                                    <div>
                                        <label for="Address">
                                            Orchard Address:
                                        </label><br>
                                        <input id="Address" type="text" name="Address" value="<?php
                                            echo($tempAddress);
                                        ?>" placeholder="Orchard Address" required>
                                    </div>
                                </td>
                            </tr>

                            <tr class="fadeIn third">
                                <!-- Latitude -->
                                <td>
                                    <div>
                                        <label for="Latitude">
                                            Latitude:
                                        </label><br>
                                        <input id="Latitude" type="number" step="0.00001" name="Latitude" value="<?php
                                            echo($tempLatitude);
                                        ?>" placeholder="Orchard Latitude" min="-90" max="90" required>
                                    </div>
                                </td>

                                <!-- Longitude -->
                                <td>
                                    <div>
                                        <label for="Longitude">
                                            Longitude:
                                        </label><br>
                                        <input id="Longitude" type="number" step="0.00001" name="Longitude" value="<?php
                                            echo($tempLongitude);
                                        ?>" placeholder="Orchard Longitude" min="-180" max="180" required>
                                    </div>
                                </td>
                            </tr>

                            <tr class="fadeIn fourth">
                                <!-- CompanyID -->
                                <td colspan="2">
                                    <div>
                                        <label for="CompanyID">
                                            Company (Owner):
                                        </label><br>
                                        <select id="CompanyID" name="CompanyID">
                                            <?php foreach ($allCompany as $result): ?>
                                                <option value="<?php
                                                    echo($result["UserID"]);
                                                ?>"<?php
                                                    if ($tempCompID == $result["UserID"]) {
                                                        echo(" selected");
                                                    }
                                                ?>><?php
                                                    echo($result["RealName"]);
                                                ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </td>
                            </tr>

                            <tr class="fadeIn fifth">
                                <td colspan="2">
                                    <div>
                                        <br>
                                        <input type="submit" value="Add Orchard Now">
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </form>
                    <br>
                    <div id="formFooter">
                        <h2><a class="underlineHover" href="/Admin/manageOrchard.php">Back to Manage Orchard</a><h2><br>
                    </div>
                    
                </div>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
