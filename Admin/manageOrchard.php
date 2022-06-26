<?php
    // Admin Manage Orchard Page.
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dbConnection.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/loginAuthenticate.php");
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

    // Check if valid CompanyID or OrchardID is provided for search, set to 0 if not.
    $companyID = $orchardID = (
        !isset($queryString["SearchKey"]) ||
        $queryString["SearchKey"] < 1
    ) ? 0: $queryString["SearchKey"];

    // Check if valid SearchOption is provided.
    $searchOption = (
        !isset($queryString["SearchOption"]) ||
        $queryString["SearchOption"] < 1 ||
        $queryString["SearchOption"] > 2
    ) ? 1: $queryString["SearchOption"];

    // Search by CompanyID.
    if ($searchOption == 1) {
        $orchardID = 0;
    }
    // Search by OrchardID.
    else {
        $companyID = 0;
    }

    // Return all the orchard.
    $allOrchard = getAllOrchard($conn, $companyID, $orchardID);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Admin: Manage Orchard Page</title>
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
                <h1>Admin: Manage Orchard Page</h1>
            </div>
        </header>

        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Admin/navigationBar.php"); ?>

        <main>
            <div class="w3-container w3-theme-d4 w3-animate-opacity">
                <h2 class="w3-center">All Orchards:</h2>
                <form id="reset-search" method="get" action="/Admin/manageOrchard.php"></form>

                <form class="w3-center" method="get" action="/Admin/manageOrchard.php">
                    <input style="width:98%" id="SearchKey" type="number" name="SearchKey" value="<?php
                        // Valid CompanyID.
                        if ($companyID > 0) {
                            echo($companyID);
                        }
                        // Valid OrchardID.
                        elseif ($orchardID > 0) {
                            echo($orchardID);
                        }
                    ?>" placeholder="Enter Company/Orchard ID" min="1" required>
                    
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
                    </select>
                    
                    <input type="submit" value="Search">

                    <input form="reset-search" type="submit" value="Reset"<?php
                        // Disable if not searching.
                        if ($companyID + $orchardID < 1) {
                            echo(" disabled");
                        }
                    ?>>
                </form>

                <div class="w3-container w3-center" style="align-content:center;">
                    <?php if (count($allOrchard) > 0): ?>
                        <table class=" w3-center w3-table-all w3-centered w3-hoverable" style="width:100%">
                            <tr>
                                <th>Orchard ID</th>
                                <th>Company ID</th>
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
                                        echo($result["CompanyID"]);
                                    ?></td>

                                    <td><?php
                                        echo(getBlockCount($conn, $result["CompanyID"], $result["OrchardID"]));
                                    ?></td>

                                    <td><?php
                                        echo(getTreeCount($conn, $result["CompanyID"], $result["OrchardID"]));
                                    ?></td>

                                    <td><?php
                                        echo(getPurchaseRequestCount($conn, 1, $result["CompanyID"], $result["OrchardID"]));
                                    ?></td>
                            
                                    <td>
                                        <form method="get" action="/Admin/viewEachOrchard.php">
                                            <input type="hidden" name="OrchardID" value="<?php
                                                echo($result["OrchardID"]);
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
                            if ($companyID + $orchardID < 1) {
                                echo("No orchard is found!");
                            }
                            elseif ($searchOption == 1) {
                                echo("Company ID $companyID is not associated with any orchard!");
                            }
                            else {
                                echo("Orchard ID $orchardID is not associated with any orchard!");
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
