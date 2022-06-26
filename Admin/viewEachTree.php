<?php
    // Admin Manage Tree Page.
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dbConnection.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/loginAuthenticate.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/inputValidation.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dataRetrieval.php");

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
    // Tree is not available for viewing.
    if (
        !isset($queryString["TreeID"]) ||
        !is_numeric($queryString["TreeID"]) ||
        $queryString["TreeID"] < 1 ||
        count($allTree = getAllTree(
            $conn, 0, 0, 0, $queryString["TreeID"]
        )) < 1
    ) {
        header("Location: /Admin/manageTree.php");
        exit;
    }

    $treeID = $queryString["TreeID"];
    $result = $allTree[0];

    $allTreeUpdate = getAllTreeUpdate($conn, 0, 0, 0, $queryString["TreeID"]);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Admin: Manage Tree Page</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">
        
        <link rel="stylesheet" href="/css/main.css">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <!--<link rel="shortcut icon" href="/favicon.ico">-->
        <link rel="shortcut icon" href="https://icon-library.com/images/tree-icon/tree-icon-23.jpg">
    </head>

    <body>
        <header>
            <div class="maintheme w3-container">
                <h1>Admin: Manage Tree Page</h1>
            </div>
        </header>

        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Admin/navigationBar.php"); ?>

        <main>
            <div class="w3-row">
                <div class="w3-container w3-quarter w3-sidebar w3-bar-block w3-theme-d5" style="width:25%;">
                    <br>
                    <form method="get" action="/Admin/viewEachOrchard.php">
                        <input type="hidden" name="OrchardID" value="<?php
                            echo($result["OrchardID"]);
                        ?>">
                        <input type="submit" value="View Related Orchard">
                    </form>

                    <form method="get" action="/Admin/viewEachBlock.php">
                        <input type="hidden" name="BlockID" value="<?php
                            echo($result["BlockID"]);
                        ?>">
                        <input type="submit" value="View Related Block">
                    </form>

                    <form method="get" action="/Admin/manageTree.php">
                        <input type="submit" value="Back to View All Trees">
                    </form>
                </div>

                <div class="w3-container w3-threequarter w3-theme-d4 w3-animate-left" style="margin-left:25%; padding-bottom:2%;">
                    <h2>Tree ID <?php
                        echo($treeID);
                    ?>:</h2>

                    <table class=" w3-center w3-table-all" style="width:100%">
                        <tr>
                            <td>Tree ID</td>
                            <td><?php
                                echo($result["TreeID"]);
                            ?></td>
                        </tr>

                        <tr>
                            <td>Species Name</td>
                            <td><?php
                                echo($result["SpeciesName"]);
                            ?></td>
                        </tr>

                        <tr>
                            <td>Latitude</td>
                            <td><?php
                                echo($result["Latitude"]);
                            ?></td>
                        </tr>

                        <tr>
                            <td>Longitude</td>
                            <td><?php
                                echo($result["Longitude"]);
                            ?></td>
                        </tr>

                        <tr>
                            <td>Plant Date</td>
                            <td><?php
                                echo($result["PlantDate"]);
                            ?></td>
                        </tr>

                        <tr>
                            <td>Block ID</td>
                            <td><?php
                                echo($result["BlockID"]);
                            ?></td>
                        </tr>

                        <tr>
                            <td>Orchard ID</td>
                            <td><?php
                                echo($result["OrchardID"]);
                            ?></td>
                        </tr>

                        <tr>
                            <td>Company ID</td>
                            <td><?php
                                echo($result["CompanyID"]);
                            ?></td>
                        </tr>
                    </table>

                    <h3>Tree Update History:</h3>
                    <?php if (count($allTreeUpdate) > 0): ?>
                        <table class=" w3-center w3-table-all w3-centered w3-hoverable" style="width:100%">
                            <tr>
                                <th>Update ID</th>
                                <th>Update Date</th>
                                <th>Image</th>
                                <th>Height (m)</th>
                                <th>Diameter (m)</th>
                                <th>Status</th>
                            </tr>
                            <?php foreach ($allTreeUpdate as $result): ?>
                                <tr>
                                    <td><?php
                                        echo($result["UpdateID"]);
                                    ?></td>

                                    <td><?php
                                        echo($result["UpdateDate"]);
                                    ?></td>

                                    <td><img id="icon" src="<?php
                                        echo(cleanInput($result["TreeImage"]));
                                    ?>" alt="* UpdateID <?php
                                        echo($result["UpdateID"]);
                                    ?> img *" width="350"></td>

                                    <td><?php
                                        echo($result["TreeHeight"]);
                                    ?></td>

                                    <td><?php
                                        echo($result["Diameter"]);
                                    ?></td>

                                    <td><?php
                                        echo(getTreeUpdateStatus($result["Status"]));
                                    ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    <?php else: ?>
                        <span>* No Update History for Tree ID <?php
                            echo($treeID);
                        ?>! *</span>
                    <?php endif; ?>
                </div>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
