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

    $tempSpeciesName = $tempLatitude = $tempLongitude = $tempBlockID = "";
    $addMsg = "";
    $passAdding = true;

    $allBlock = getAllBlock($conn);

    // Disable adding if no existing block.
    if (count($allBlock) < 1) {
        $addMsg = "* Adding is not allowed as there is no existing block! *";
        $passAdding = false;
    }

    // Add attempt.
    if ($passAdding && $_SERVER["REQUEST_METHOD"] == "POST") {
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

            // Check valid Block.
            if ($passAdding && count(getAllBlock($conn, 0, 0, $tempBlockID)) < 1) {
                $addMsg = "* Choose an existing Block! *";
                $passAdding = false;
            }

            // Insert to DB.
            if ($passAdding) {
                $tempSpeciesNameEscaped = $conn->real_escape_string($tempSpeciesName);
                // Insert to Tree table.
                $query = "INSERT INTO `Tree`(`SpeciesName`, `Latitude`, `Longitude`, `BlockID`)";
                $query .= " VALUES ('$tempSpeciesNameEscaped','$tempLatitude','$tempLongitude','$tempBlockID');";

                $rs = $conn->query($query);
                if (!$rs) {
                    $addMsg = "* Fail to insert to Tree table! *";
                    $passAdding = false;
                }

                // Check if the data is successfully inserted.
                if ($passAdding) {
                    // Reset to empty.
                    $tempSpeciesName = $tempLatitude = $tempLongitude = $tempBlockID = "";
                    $addMsg = "* Tree is successfully added! *";
                }
            }
        }
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
                    <h1>Add New Tree:</h1>
                </div>
                <div id="formContentW2">
                    <img class="fadeIn first" src="https://static.vecteezy.com/system/resources/previews/002/140/928/non_2x/gardening-concept-illustration-with-man-and-women-planting-a-tree-free-vector.jpg" id="icon45" alt="Tree Icon" />

                    <form method="post" action="/Admin/addTree.php" >
                        <table >
                            <tr>
                                <td colspan="2">
                                    <span class="<?php
                                        echo(($passAdding) ? "success": "error");
                                    ?>-message"><?php
                                        echo($addMsg);
                                    ?></span>
                                </td>
                            </tr>

                            <tr class="fadeIn second" >
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
                                        <select class="fullW selectForm" id="BlockID" name="BlockID">
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
                                        <input type="submit" value="Add Tree Now"<?php
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
                        <h2><a class="underlineHover" href="/Admin/manageTree.php">Back to Manage Tree</a><h2><br>
                    </div>
                </div>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
