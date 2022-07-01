<?php
    // Admin Manage Admin Page.
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
    
    // Check if valid AdminID is provided for search, set to 0 if not.
    $adminID = (
        !isset($queryString["SearchKey"]) ||
        !is_numeric($queryString["SearchKey"]) ||
        $queryString["SearchKey"] < 1
    ) ? 0: $queryString["SearchKey"];

    // Return all the Admin.
    $allAdmin = getAllAdmin($conn, $adminID);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Admin: Manage Admin Page</title>
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
                <h1>&#129498; Admin: Manage Admin Page &#129498;</h1>
            </div>
        </header>

        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Admin/navigationBar.php"); ?>

        <main>
            <div class="w3-container w3-theme-d4 w3-animate-opacity">
                <h2 class="w3-center">All Admins:</h2>

                <form id="reset-search" method="get" action="/Admin/manageAdmin.php"></form>
                <form id="register-Admin" method="get" action="/Admin/registerAdmin.php"></form>

                <form class="w3-center" method="get" action="/Admin/manageAdmin.php">
                    <input style="width:98%" id="SearchKey" type="number" name="SearchKey" value="<?php
                        // Valid AdminID.
                        if ($adminID > 0) {
                            echo($adminID);
                        }
                    ?>" placeholder="Enter Admin ID" min="1" required>
                    
                    <input type="submit" value="Search">

                    <input form="reset-search" type="submit" value="Reset"<?php
                        // Disable if not searching.
                        if ($adminID < 1) {
                            echo(" disabled");
                        }
                    ?>>

                    <input form="register-Admin" type="submit" value="Register New Admin">
                </form>

                <div class="w3-container w3-center" style="align-content:center;">
                    <?php if (count($allAdmin) > 0): ?>
                        <table class=" w3-center w3-table-all w3-centered w3-hoverable" style="width:100%">
                            <tr>
                                <th>Admin ID</th>
                                <th>Username</th>
                                <th>Total Processed Request</th>
                                <th>Total Approved &#10004;</th>
                                <th>Total Rejected &#10006;</th>
                                <th>Action</th>
                            </tr>
                            <?php foreach ($allAdmin as $result): ?>
                                <tr>
                                    <td><?php
                                        echo($result["UserID"]);
                                    ?></td>

                                    <td><?php
                                        echo($result["Username"]);
                                    ?></td>

                                    <td><?php
                                        echo(
                                            getPurchaseRequestCount(
                                                $conn, -1, 0, 0, 0, 0, 0, $result["UserID"]
                                            )
                                        );
                                    ?></td>

                                    <td><?php
                                        echo(
                                            getPurchaseRequestCount(
                                                $conn, 1, 0, 0, 0, 0, 0, $result["UserID"]
                                            )
                                        );
                                    ?></td>

                                    <td><?php
                                        echo(
                                            getPurchaseRequestCount(
                                                $conn, 2, 0, 0, 0, 0, 0, $result["UserID"]
                                            )
                                        );
                                    ?></td>
                            
                                    <td>
                                        <form method="get" action="/Admin/viewEachAdmin.php">
                                            <input type="hidden" name="AdminID" value="<?php
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
                            if ($adminID < 1) {
                                echo("No Admin is found!");
                            }
                            else {
                                echo("Admin ID $adminID is not associated with any Admin!");
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