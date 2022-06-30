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
    $passAdding = true;

    $allCompany = getAllCompany($conn);

    // Disable adding if no existing company.
    if (count($allCompany) < 1) {
        $addMsg = "* Adding is not allowed as there is no existing company! *";
        $passAdding = false;
    }

    // Add attempt.
    if ($passAdding && $_SERVER["REQUEST_METHOD"] == "POST") {
        $tempAddress = (isset($_POST["Address"])) ? cleanInput($_POST["Address"]): "";
        $tempLatitude = (isset($_POST["Latitude"])) ? cleanInput($_POST["Latitude"]): "";
        $tempLongitude = (isset($_POST["Longitude"])) ? cleanInput($_POST["Longitude"]): "";
        $tempCompID = (isset($_POST["CompanyID"])) ? cleanInput($_POST["CompanyID"]): "";

        if (
            empty($tempAddress) ||
            empty($tempLatitude) ||
            empty($tempLongitude) ||
            empty($tempCompID)
        ) {
            $addMsg = "* Fill in ALL Fields! *";
            $passAdding = false;
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
                $tempAddressEscaped = $conn->real_escape_string($tempAddress);
                // Insert to Orchard table.
                $query = "INSERT INTO `Orchard`(`Address`, `Latitude`, `Longitude`, `CompanyID`)";
                $query .= " VALUES ('$tempAddressEscaped','$tempLatitude','$tempLongitude','$tempCompID');";

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
                <h4 style="font-size: 36px">Admin: Manage Orchard Page</h4>
            </div>
        </header>

        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Admin/navigationBar.php"); ?>

        <main>
            <div class="wrapper fadeInDown">
                <div id="formHeader">
                    <h1>Add New Orchard:</h1>
                </div>
                <div id="formContentW2">
                    <img class="fadeIn first" src="/img/defaults/orchardIcon.png" id="icon45" alt="Orchard Icon" />

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
                                        <input id="Latitude" type="number" step="0.0001" name="Latitude" value="<?php
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
                                        <input id="Longitude" type="number" step="0.0001" name="Longitude" value="<?php
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
                                        <select class="fullW selectForm" id="CompanyID" name="CompanyID">
                                            <?php foreach ($allCompany as $result): ?>
                                                <option value="<?php
                                                    echo($result["UserID"]);
                                                ?>"<?php
                                                    if ($tempCompID == $result["UserID"]) {
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

                            <tr class="fadeIn fifth">
                                <td colspan="2">
                                    <div>
                                        <br>
                                        <input type="submit" value="Add Orchard Now"<?php
                                            if (count($allCompany) < 1) {
                                                echo(" disabled");
                                            }
                                        ?>>
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
