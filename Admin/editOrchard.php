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

    $queryString = array();

    if (isset($_SERVER['QUERY_STRING'])) {
        parse_str($_SERVER['QUERY_STRING'], $queryString);
    }

    $allOrchard = NULL;
    // Orchard is not available for editing.
    if (
        !isset($queryString["OrchardID"]) ||
        !is_numeric($queryString["OrchardID"]) ||
        $queryString["OrchardID"] < 1 ||
        count($allOrchard = getAllOrchard($conn, 0, $queryString["OrchardID"])) < 1
    ) {
        header("Location: /Admin/manageOrchard.php");
        exit;
    }

    $orchardID = $queryString["OrchardID"];
    $result = $allOrchard[0];

    $tempAddress = $tempLatitude = $tempLongitude = $tempCompID = "";
    $editMsg = "";
    $passEditing = true;

    $allCompany = getAllCompany($conn);

    // Disable editing if no existing company.
    if (count($allCompany) < 1) {
        $editMsg = "* Editing is not allowed as there is no existing company! *";
        $passEditing = false;
    }

    // Edit attempt.
    if ($passEditing && $_SERVER["REQUEST_METHOD"] == "POST") {
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
            $editMsg = "* Fill in ALL Fields! *";
        }
        else {
            // Set to true at first.
            $passEditing = true;

            // Check Latitude.
            if (!checkValidLatitude($tempLatitude)) {
                $editMsg = "* Valid latitude (-90 <= x <= 90)! *";
                $passEditing = false;
            }

            // Check Longitude.
            if ($passEditing && !checkValidLongitude($tempLongitude)) {
                $editMsg = "* Valid longitude (-180 <= x <= 180)! *";
                $passEditing = false;
            }

            // Check valid CompanyID.
            if ($passEditing && count(getAllCompany($conn, $tempCompID)) < 1) {
                $editMsg = "* Choose an existing Company! *";
                $passEditing = false;
            }

            // Update in DB.
            if ($passEditing) {
                // Update in Orchard table.
                $query = "UPDATE `Orchard`";
                $query .= " SET `Address`='$tempAddress'";
                $query .= ", `Latitude`='$tempLatitude'";
                $query .= ", `Longitude`='$tempLongitude'";
                $query .= ", `CompanyID`='$tempCompID'";
                $query .= " WHERE `Orchard`.`OrchardID`='$orchardID'";

                $rs = $conn->query($query);
                if (!$rs) {
                    $editMsg = "* Fail to update in Orchard table! *";
                    $passEditing = false;
                }

                // Check if the data is successfully updated.
                if ($passEditing) {
                    $editMsg = "* Orchard is successfully updated! *";
                }
            }
        }
    }
    if (empty($tempAddress)) {
        $tempAddress = $result["Address"];
    }

    if (empty($tempLatitude)) {
        $tempLatitude = $result["Latitude"];
    }

    if (empty($tempLongitude)) {
        $tempLongitude = $result["Longitude"];
    }

    if (empty($tempCompID)) {
        $tempCompID = $result["CompanyID"];
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
                <h1>Admin: Manage Orchard Page</h1>
            </div>
        </header>

        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Admin/navigationBar.php"); ?>

        <main>
            <div class="wrapper fadeInDown">
                <div id="formHeader">
                    <h1>Edit Orchard ID <?php
                        echo($orchardID);
                    ?>:</h1>
                </div>
                <div id="formContentW2">
                    <img class="fadeIn first" src="https://us.123rf.com/450wm/goodstudio/goodstudio1910/goodstudio191000131/131189697-family-working-in-fruit-garden-together-flat-vector-illustration-people-gathering-apples-berries-and.jpg" id="icon" alt="Orchard Icon" />

                    <form method="post" action="/Admin/editOrchard.php?OrchardID=<?php
                        echo($orchardID);
                    ?>">
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
                                        <input type="submit" value="Save Editing"<?php
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
                        <h2><a class="underlineHover" href="/Admin/viewEachOrchard.php?OrchardID=<?php
                            echo($orchardID);
                        ?>">Back to View Orchard</a><h2><br>
                    </div>
                </div>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
