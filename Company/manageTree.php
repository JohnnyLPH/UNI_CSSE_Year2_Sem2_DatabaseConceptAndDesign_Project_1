<?php
    // Company Manage Tree Page.
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dbConnection.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/loginAuthenticate.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dataRetrieval.php");

    $tempLoginCheck = checkLogin($conn);
    // Not logged in as Company.
    if ($tempLoginCheck != 1) {
        header("Location: /index.php");
        exit;
    }

    $queryString = array();

    if (isset($_SERVER['QUERY_STRING'])) {
        parse_str($_SERVER['QUERY_STRING'], $queryString);
    }

    // Check if valid OrchardID, BlockID or TreeID is provided for search, set to 0 if not.
    $orchardID = $blockID = $treeID = (
        !isset($queryString["SearchKey"]) ||
        $queryString["SearchKey"] < 1
    ) ? 0: $queryString["SearchKey"];

    // Check if valid SearchOption is provided.
    $searchOption = (
        !isset($queryString["SearchOption"]) ||
        $queryString["SearchOption"] < 1 ||
        $queryString["SearchOption"] > 3
    ) ? 1: $queryString["SearchOption"];

    // Search by OrchardID.
    if ($searchOption == 1) {
        $blockID = 0;
        $treeID = 0;
    }
    // Search by BlockID.
    elseif ($searchOption == 2) {
        $orchardID = 0;
        $treeID = 0;
    }
    // Search by TreeID.
    else {
        $orchardID = 0;
        $blockID = 0;
    }

    // Return all the tree.
    $allTree = getAllTree($conn, $_SESSION["UserID"], $orchardID, $blockID, $treeID);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Company: Manage Tree Page</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">
        
        <link rel="stylesheet" href="/css/main.css">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <!--<link rel="shortcut icon" href="/favicon.ico">-->
        <link rel="shortcut icon" href="https://icon-library.com/images/tree-icon/tree-icon-23.jpg">
    </head>

    <body>
        <header>
            <h1>Company: Manage Tree Page</h1>
        </header>

        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Company/navigationBar.php"); ?>

        <main>
            <h2>All Trees:</h2>

            <form id="reset-search" method="get" action="/Company/manageTree.php"></form>

            <form method="get" action="/Company/manageTree.php">
                <input id="SearchKey" type="number" name="SearchKey" value="<?php
                    // Valid SearchKey.
                    if ($orchardID > 0) {
                        echo($orchardID);
                    }
                    elseif ($blockID > 0) {
                        echo($blockID);
                    }
                    elseif ($treeID > 0) {
                        echo($treeID);
                    }
                ?>" placeholder="Enter Orchard/Block/Tree ID" min="1" required>

                <!-- <label for="SearchOption">Search By:</label> -->
                <select id="SearchOption" name="SearchOption">
                    <option value="1"<?php
                        if ($searchOption == 1) {
                            echo(" selected");
                        }
                    ?>>OrchardID</option>
                    <option value="2"<?php
                        if ($searchOption == 2) {
                            echo(" selected");
                        }
                    ?>>BlockID</option>
                    <option value="3"<?php
                        if ($searchOption == 3) {
                            echo(" selected");
                        }
                    ?>>TreeID</option>
                </select>
                
                <input type="submit" value="Search">

                <input form="reset-search" type="submit" value="Reset"<?php
                    // Disable if not searching.
                    if ($orchardID < 1 && $blockID < 1 && $treeID < 1) {
                        echo(" disabled");
                    }
                ?>>
            </form>

            <?php if (count($allTree) > 0): ?>
                <table>
                    <tr>
                        <th>Tree ID</th>
                        <th>Block ID</th>
                        <th>Orchard ID</th>
                        <th>Tree Species</th>
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
                                echo($result["SpeciesName"]);
                            ?></td>
                            
                            <td>
                                <form method="get" action="/Company/viewEachTree.php">
                                    <input type="hidden" name="TreeID" value="<?php
                                        echo($result["TreeID"]);
                                    ?>">
                                    <input type="submit" value="View">
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <span>* <?php
                    if ($searchOption == 1) {
                        echo("Orchard ID $orchardID");
                    }
                    elseif ($searchOption == 2) {
                        echo("Block ID $blockID");
                    }
                    else {
                        echo("Tree ID $treeID");
                    }
                ?> is not associated with any trees of <?php
                    echo($_SESSION["Username"]);
                ?>! *</span>
            <?php endif; ?>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
