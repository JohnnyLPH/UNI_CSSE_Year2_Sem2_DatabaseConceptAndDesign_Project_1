<?php
    // Admin Manage Tree Page.
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dbConnection.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/loginAuthenticate.php");
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

    // Check if valid CompanyID, OrchardID, BlockID or TreeID is provided for search, set to 0 if not.
    $companyID = $orchardID = $blockID = $treeID = (
        !isset($queryString["SearchKey"]) ||
        !is_numeric($queryString["SearchKey"]) ||
        $queryString["SearchKey"] < 1
    ) ? 0: $queryString["SearchKey"];

    // Check if valid SearchOption is provided.
    $searchOption = (
        !isset($queryString["SearchOption"]) ||
        !is_numeric($queryString["SearchOption"]) ||
        $queryString["SearchOption"] < 1 ||
        $queryString["SearchOption"] > 4
    ) ? 1: $queryString["SearchOption"];

    // Search by CompanyID.
    if ($searchOption == 1) {
        $orchardID = $blockID = $treeID = 0;
    }
    // Search by OrchardID.
    elseif ($searchOption == 2) {
        $companyID = $blockID = $treeID = 0;
    }
    // Search by BlockID.
    elseif ($searchOption == 3) {
        $companyID = $orchardID = $treeID = 0;
    }
    // Search by TreeID.
    else {
        $companyID = $orchardID = $blockID = 0;
    }

    // Return all the tree.
    $allTree = getAllTree($conn, $companyID, $orchardID, $blockID, $treeID);
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
            <div class="w3-container w3-theme-d4 w3-animate-opacity">
                <h2 class="w3-center">All Trees:</h2>

                <form id="reset-search" method="get" action="/Admin/manageTree.php"></form>
                <form id="add-tree" method="get" action="/Admin/addTree.php"></form>

                <form class="w3-center" method="get" action="/Admin/manageTree.php">
                    <input style="width:98%" id="SearchKey" type="number" name="SearchKey" value="<?php
                        // Valid SearchKey.
                        if ($companyID > 0) {
                            echo($companyID);
                        }
                        elseif ($orchardID > 0) {
                            echo($orchardID);
                        }
                        elseif ($blockID > 0) {
                            echo($blockID);
                        }
                        elseif ($treeID > 0) {
                            echo($treeID);
                        }
                    ?>" placeholder="Enter Company/Orchard/Block/Tree ID" min="1" required>

                    <!-- <label for="SearchOption">Search By:</label> -->
                    <select id="SearchOption" name="SearchOption">
                        <option value="1"<?php
                            if ($searchOption == 1) {
                                echo(" selected");
                            }
                        ?>>CompanyID</option>
                        <option value="2"<?php
                            if ($searchOption == 2) {
                                echo(" selected");
                            }
                        ?>>OrchardID</option>
                        <option value="3"<?php
                            if ($searchOption == 3) {
                                echo(" selected");
                            }
                        ?>>BlockID</option>
                        <option value="4"<?php
                            if ($searchOption == 4) {
                                echo(" selected");
                            }
                        ?>>TreeID</option>
                    </select>
                    
                    <input type="submit" value="Search">

                    <input form="reset-search" type="submit" value="Reset"<?php
                        // Disable if not searching.
                        if ($companyID + $orchardID + $blockID + $treeID < 1) {
                            echo(" disabled");
                        }
                    ?>>

                    <input form="add-tree" type="submit" value="Add Tree">
                </form>
                
                <div class="w3-container w3-center" style="align-content:center;">
                    <?php if (count($allTree) > 0): ?>
                        <table class=" w3-center w3-table-all w3-centered w3-hoverable" style="width:100%">
                            <tr>
                                <th>Tree ID</th>
                                <th>Block ID</th>
                                <th>Orchard ID</th>
                                <th>Company ID</th>
                                <th>Tree Species</th>
                                <th>Tree Plant Date</th>
                                <th>Action</th>
                            </tr>
                            <?php foreach ($allTree as $result): ?>
                                <tr>
                                    <td><?php
                                        echo($result["TreeID"]);
                                    ?></td>

                                    <td><?php
                                        echo($result["BlockID"]);
                                    ?></td>

                                    <td><?php
                                        echo($result["OrchardID"]);
                                    ?></td>

                                    <td><?php
                                        echo($result["CompanyID"]);
                                    ?></td>

                                    <td><?php
                                        echo($result["SpeciesName"]);
                                    ?></td>

                                    <td>
                                        <?php
                                        echo($result["PlantDate"]);
                                        ?>
                                    </td>
                                    
                                    <td>
                                        <form method="get" action="/Admin/viewEachTree.php">
                                            <input type="hidden" name="TreeID" value="<?php
                                                echo($result["TreeID"]);
                                            ?>">
                                            <input type="submit" value="View">
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                        <br>
                    <?php else: ?>
                        <span>* <?php
                            if ($companyID + $orchardID + $blockID + $treeID < 1) {
                                echo("No tree is found!");
                            }
                            elseif ($searchOption == 1) {
                                echo(
                                    "Company ID $companyID is not associated with any tree!"
                                );
                            }
                            elseif ($searchOption == 2) {
                                echo(
                                    "Orchard ID $orchardID is not associated with any tree!"
                                );
                            }
                            elseif ($searchOption == 3) {
                                echo(
                                    "Block ID $blockID is not associated with any tree!"
                                );
                            }
                            else {
                                echo(
                                    "Tree ID $treeID is not associated with any tree!"
                                );
                            }
                        ?> *</span>
                    <?php endif; ?>
                </div>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
