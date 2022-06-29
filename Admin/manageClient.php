<?php
    // Admin Manage Client Page.
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
    
    // Check if valid ClientID is provided for search, set to 0 if not.
    $clientID = (
        !isset($queryString["SearchKey"]) ||
        !is_numeric($queryString["SearchKey"]) ||
        $queryString["SearchKey"] < 1
    ) ? 0: $queryString["SearchKey"];

    // Return all the Client.
    $allClient = getAllClient($conn, $clientID);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Admin: Manage Client Page</title>
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
                <h1>Admin: Manage Client Page</h1>
            </div>
        </header>

        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Admin/navigationBar.php"); ?>

        <main>
            <div class="w3-container w3-theme-d4 w3-animate-opacity">
                <h2 class="w3-center">All Clients:</h2>

                <form id="reset-search" method="get" action="/Admin/manageClient.php"></form>
                <form id="register-client" method="get" action="/Admin/registerClient.php"></form>

                <form class="w3-center" method="get" action="/Admin/manageClient.php">
                    <input style="width:98%" id="SearchKey" type="number" name="SearchKey" value="<?php
                        // Valid ClientID.
                        if ($clientID > 0) {
                            echo($clientID);
                        }
                    ?>" placeholder="Enter Client ID" min="1" required>
                    
                    <input type="submit" value="Search">

                    <input form="reset-search" type="submit" value="Reset"<?php
                        // Disable if not searching.
                        if ($clientID < 1) {
                            echo(" disabled");
                        }
                    ?>>

                    <input form="register-client" type="submit" value="Register New Client">
                </form>

                <div class="w3-container w3-center" style="align-content:center;">
                    <?php if (count($allClient) > 0): ?>
                        <table class=" w3-center w3-table-all w3-centered w3-hoverable" style="width:100%">
                            <tr>
                                <th>Client ID</th>
                                <th>Username</th>
                                <th>Total Block Owned</th>
                                <th>Total Purchase Request</th>
                                <th>Total Sale</th>
                                <th>Action</th>
                            </tr>
                            <?php foreach ($allClient as $result): ?>
                                <tr>
                                    <td><?php
                                        echo($result["UserID"]);
                                    ?></td>

                                    <td><?php
                                        echo($result["Username"]);
                                    ?></td>

                                    <td><?php
                                        echo(count(getBlockLatestClient($conn, 0, 0, 0, $result["UserID"])));
                                    ?></td>

                                    <td><?php
                                        echo(getPurchaseRequestCount($conn, -1, 0, 0, 0, 0, $result["UserID"]));
                                    ?></td>

                                    <td><?php
                                        echo(getOnSaleCount($conn, 0, 0, 0, $result["UserID"]));
                                    ?></td>
                            
                                    <td>
                                        <form method="get" action="/Admin/viewEachClient.php">
                                            <input type="hidden" name="ClientID" value="<?php
                                                echo($result["UserID"]);
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
                            if ($clientID < 1) {
                                echo("No Client is found!");
                            }
                            else {
                                echo("Client ID $clientID is not associated with any Client!");
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