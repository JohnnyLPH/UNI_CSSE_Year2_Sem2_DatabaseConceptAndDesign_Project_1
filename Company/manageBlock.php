<?php
    // Company Manage Block Page.
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

    // Check if valid OrchardID or BlockID is provided for search, set to 0 if not.
    $orchardID = $blockID = (
        !isset($queryString["SearchKey"]) ||
        $queryString["SearchKey"] < 1
    ) ? 0: $queryString["SearchKey"];

    // Check if valid SearchOption is provided.
    $searchOption = (
        !isset($queryString["SearchOption"]) ||
        $queryString["SearchOption"] < 1 ||
        $queryString["SearchOption"] > 2
    ) ? 1: $queryString["SearchOption"];

    // Search by OrchardID.
    if ($searchOption == 1) {
        $blockID = 0;
    }
    // Search by BlockID.
    else {
        $orchardID = 0;
    }

    // Return all the block.
    $allBlock = getAllBlock($conn, $_SESSION["UserID"], $orchardID, $blockID);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Company: Manage Block Page</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">
        
        <link rel="stylesheet" href="/css/main.css">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <!--<link rel="shortcut icon" href="/favicon.ico">-->
        <link rel="shortcut icon" href="https://icon-library.com/images/tree-icon/tree-icon-23.jpg">
    </head>

    <body>
        <header>
            <h1>Company: Manage Block Page</h1>
        </header>

        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Company/navigationBar.php"); ?>

        <main>
            <div class="w3-container w3-theme-d4 w3-animate-opacity">
                <h2 class="w3-center">All Blocks:</h2>

                <form id="reset-search" method="get" action="/Company/manageBlock.php"></form>

                <form class="w3-center" method="get" action="/Company/manageBlock.php">
                    <input id="SearchKey" type="number" name="SearchKey" value="<?php
                        // Valid SearchKey.
                        if ($orchardID > 0) {
                            echo($orchardID);
                        }
                        elseif ($blockID > 0) {
                            echo($blockID);
                        }
                    ?>" placeholder="Enter Orchard/Block ID" min="1" required>

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
                    </select>
                    
                    <input type="submit" value="Search">

                    <input form="reset-search" type="submit" value="Reset"<?php
                        // Disable if not searching.
                        if ($orchardID < 1 && $blockID < 1) {
                            echo(" disabled");
                        }
                    ?>>
                </form>

                <?php if (count($allBlock) > 0): ?>
                    <table>
                        <tr>
                            <th>Block ID</th>
                            <th>Orchard ID</th>
                            <th>Total Tree</th>
                            <th>Client Purchase</th>
                            <th>Action</th>
                        </tr>
                        <?php foreach ($allBlock as $result): ?>
                            <tr>
                                <td><?php
                                    echo($result["BlockID"]);
                                ?></td>

                                <td><?php
                                    echo($result["OrchardID"]);
                                ?></td>

                                <td><?php
                                    echo(getTreeCount(
                                        $conn, $_SESSION["UserID"], $result["OrchardID"], $result["BlockID"]
                                    ));
                                ?></td>

                                <td><?php
                                    echo(getPurchaseRequestCount(
                                        $conn, 1, $_SESSION["UserID"], $result["OrchardID"], $result["BlockID"]
                                    ));
                                ?></td>
                                
                                <td>
                                    <form method="get" action="/Company/viewEachBlock.php">
                                        <input type="hidden" name="BlockID" value="<?php
                                            echo($result["BlockID"]);
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
                        else {
                            echo("Block ID $blockID");
                        }
                    ?> is not associated with any blocks of <?php
                        echo($_SESSION["Username"]);
                    ?>! *</span>
                <?php endif; ?>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
