<?php
    // Admin Manage Tree Page.
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

    $allTree = NULL;
    // Tree is not available for editing.
    if (
        !isset($queryString["TreeID"]) ||
        !is_numeric($queryString["TreeID"]) ||
        $queryString["TreeID"] < 1 ||
        count($allTree = getAllTree($conn, 0, 0, 0, $queryString["TreeID"])) < 1
    ) {
        header("Location: /Admin/manageTree.php");
        exit;
    }

    $treeID = $queryString["TreeID"];
    $result = $allTree[0];

    $tempSpeciesName = $tempLatitude = $tempLongitude = $tempBlockID = "";
    $editMsg = "";
    $passEditing = true;

    // Allow assigning to different blocks within same orchard.
    $orchardID = $result["OrchardID"];
    $allBlock = getAllBlock($conn, 0, $orchardID);

    // Disable editing if no existing block.
    if (count($allBlock) < 1) {
        $editMsg = "* Editing is not allowed as there is no existing block! *";
        $passEditing = false;
    }

    // Edit attempt.
    if ($passEditing && $_SERVER["REQUEST_METHOD"] == "POST") {
        $tempSpeciesName = (isset($_POST["SpeciesName"])) ? cleanInput($_POST["SpeciesName"]): "";
        $tempLatitude = (isset($_POST["Latitude"])) ? cleanInput($_POST["Latitude"]): "";
        $tempLongitude = (isset($_POST["Longitude"])) ? cleanInput($_POST["Longitude"]): "";
        $tempBlockID = (isset($_POST["BlockID"])) ? cleanInput($_POST["BlockID"]): "";

        if (
            empty($tempSpeciesName) ||
            empty($tempLatitude) ||
            empty($tempLongitude) ||
            empty($tempBlockID)
        ) {
            $editMsg = "* Fill in ALL Fields! *";
            $passEditing = false;
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

            // Check valid Block.
            if ($passEditing && count(getAllBlock($conn, 0, 0, $tempBlockID)) < 1) {
                $editMsg = "* Choose an existing Block! *";
                $passEditing = false;
            }

            // Update in DB.
            if ($passEditing) {
                $tempSpeciesNameEscaped = $conn->real_escape_string($tempSpeciesName);
                
                // Update in Tree table.
                $query = "UPDATE `Tree`";
                $query .= " SET `SpeciesName`='$tempSpeciesNameEscaped'";
                $query .= ", `Latitude`='$tempLatitude'";
                $query .= ", `Longitude`='$tempLongitude'";
                $query .= ", `BlockID`='$tempBlockID'";
                $query .= " WHERE `Tree`.`TreeID`='$treeID';";

                $rs = $conn->query($query);
                if (!$rs) {
                    $editMsg = "* Fail to update in Tree table! *";
                    $passEditing = false;
                }

                // Check if the data is successfully updated.
                if ($passEditing) {
                    $editMsg = "* Tree is successfully updated! *";
                }
            }
        }
    }
    if (empty($tempSpeciesName)) {
        $tempSpeciesName = $result["SpeciesName"];
    }

    if (empty($tempLatitude)) {
        $tempLatitude = $result["Latitude"];
    }

    if (empty($tempLongitude)) {
        $tempLongitude = $result["Longitude"];
    }

    if (empty($tempBlockID)) {
        $tempBlockID = $result["BlockID"];
    }

    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Admin: Manage Tree Page</title>
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
                <h4 style="font-size: 36px">Admin: Manage Tree Page</h4>
            </div>
        </header>

        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Admin/navigationBar.php"); ?>

        <main>
            <div class="wrapper fadeInDown">
                <div id="formHeader">
                    <h1>Edit Tree ID <?php
                        echo($treeID);
                    ?>:</h1>
                </div>
                <div id="formContentW2">
                    <img class="fadeIn first" src="/img/defaults/treeIcon.jpg" id="icon" alt="Tree Icon" />

                    <form method="post" action="/Admin/editTree.php?TreeID=<?php
                        echo($treeID);
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
                                <!-- SpeciesName -->
                                <td colspan="2">
                                    <div>
                                        <label for="SpeciesName">
                                            Species Name:
                                        </label><br>
                                        <input id="SpeciesName" type="text" name="SpeciesName" value="<?php
                                            echo($tempSpeciesName);
                                        ?>" placeholder="Tree Species" required>
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
                                        ?>" placeholder="Tree Latitude" min="-90" max="90" required>
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
                                        ?>" placeholder="Tree Longitude" min="-180" max="180" required>
                                    </div>
                                </td>
                            </tr>

                            <tr class="fadeIn fourth">
                                <!-- BlockID -->
                                <td colspan="2">
                                    <div>
                                        <label for="BlockID">
                                            Block:
                                        </label><br>
                                        <select id="BlockID" name="BlockID">
                                            <?php foreach ($allBlock as $result): ?>
                                                <option value="<?php
                                                    echo($result["BlockID"]);
                                                ?>"<?php
                                                    if ($tempBlockID == $result["BlockID"]) {
                                                        echo(" selected");
                                                    }
                                                ?>><?php
                                                    echo(
                                                        "Block " . $result["BlockID"] . " in " .
                                                        "Orchard " . $result["OrchardID"]
                                                    );
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
                                            if (count($allBlock) < 1) {
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
                        <h2><a class="underlineHover" href="/Admin/viewEachTree.php?TreeID=<?php
                            echo($treeID);
                        ?>">Back to View Tree</a><h2><br>
                    </div>
                </div>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
