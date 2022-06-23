<?php
    // Company Manage Orchard Page.
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

    // Check if valid OrchardID is provided for search, set to 0 if not.
    $orchardID = (!isset($queryString["SearchKey"]) || $queryString["SearchKey"] < 1) ? 0: $queryString["SearchKey"];

    // Return all the orchard.
    $allOrchard = getAllOrchard($conn, $_SESSION["UserID"], $orchardID);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Company: Manage Orchard Page</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">
        
        <link rel="stylesheet" href="/css/main.css">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <!--<link rel="shortcut icon" href="/favicon.ico">-->
        <link rel="shortcut icon" href="https://icon-library.com/images/tree-icon/tree-icon-23.jpg">
    </head>

    <body>
        <header>
            <h1>Company: Manage Orchard Page</h1>
        </header>

        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Company/navigationBar.php"); ?>

        <main>
            <div class="w3-container w3-theme-d4 w3-animate-opacity">
                <h2 class="w3-center">All Orchards:</h2>
                <form id="reset-search" method="get" action="/Company/manageOrchard.php"></form>

                <form class="w3-center" method="get" action="/Company/manageOrchard.php">
                    <input style="width:98%" id="SearchKey" type="number" name="SearchKey" value="<?php
                        // Valid OrchardID.
                        if ($orchardID > 0) {
                            echo($orchardID);
                        }
                    ?>" placeholder="Enter Orchard ID" min="1" required>
                    
                    <input type="submit" value="Search">

                    <input form="reset-search" type="submit" value="Reset"<?php
                        // Disable if not searching for OrchardID.
                        if ($orchardID < 1) {
                            echo(" disabled");
                        }
                    ?>>
                </form>

                <div class="w3-container w3-center" style="align-content:center;">
                    <?php if (count($allOrchard) > 0): ?>
                        <table class=" w3-center w3-table-all w3-centered w3-hoverable" style="width:100%">
                            <tr>
                                <th>Orchard ID</th>
                                <th>Total Block</th>
                                <th>Total Tree</th>
                                <th>Client Purchase</th>
                                <th>Action</th>
                            </tr>
                            <?php foreach ($allOrchard as $result): ?>
                                <tr>
                                    <td><?php
                                        echo($result["OrchardID"]);
                                    ?></td>

                                    <td><?php
                                        echo(getBlockCount($conn, $_SESSION["UserID"], $result["OrchardID"]));
                                    ?></td>

                                    <td><?php
                                        echo(getTreeCount($conn, $_SESSION["UserID"], $result["OrchardID"]));
                                    ?></td>

                                    <td><?php
                                        echo(getPurchaseRequestCount($conn, 1, $_SESSION["UserID"], $result["OrchardID"]));
                                    ?></td>
                            
                                    <td>
                                        <form method="get" action="/Company/viewEachOrchard.php">
                                            <input type="hidden" name="OrchardID" value="<?php
                                                echo($result["OrchardID"]);
                                            ?>">
                                            <input type="submit" value="View">
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    <?php else: ?>
                        <span>* Orchard ID <?php
                            echo($orchardID);
                        ?> is not associated with any orchards of <?php
                            echo($_SESSION["Username"]);
                        ?>! *</span>
                    <?php endif; ?>
                    <br>
                </div>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
