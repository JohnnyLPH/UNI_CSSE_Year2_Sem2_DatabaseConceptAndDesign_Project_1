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

    $allAdmin = NULL;
    // Admin is not available for viewing.
    if (
        !isset($queryString["AdminID"]) ||
        !is_numeric($queryString["AdminID"]) ||
        $queryString["AdminID"] < 1 ||
        count($allAdmin = getAllAdmin($conn, $queryString["AdminID"])) < 1
    ) {
        header("Location: /Admin/manageAdmin.php");
        exit;
    }

    $adminID = $queryString["AdminID"];
    $result = $allAdmin[0];
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
                <h1>Admin: Manage Admin Page</h1>
            </div>
        </header>

        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Admin/navigationBar.php"); ?>

        <main>
            <div class="w3-row">
                <div class="w3-container w3-threequarter w3-theme-d4 w3-animate-left" style="margin-left:25%; padding-bottom:2%; padding-top:2%;">

                    <div class="w3-container">
                        <h2>Admin ID <?php
                            echo($adminID);
                        ?>:</h2> 

                        <table class="w3-table-all">
                            <tr>
                                <td>Admin ID</td>
                                <td><?php
                                    echo($result["UserID"]);
                                ?></td>
                            </tr>

                            <tr>
                                <td>Username</td>
                                <td><?php
                                    echo($result["Username"]);
                                ?></td>
                            </tr>

                            <tr>
                                <td>Email</td>
                                <td><?php
                                    echo($result["Email"]);
                                ?></td>
                            </tr>

                            <tr>
                                <td>Admin Name</td>
                                <td><?php
                                    echo($result["RealName"]);
                                ?></td>
                            </tr>

                            <tr>
                                <td>Total Processed Request</td>
                                <td><?php
                                    echo(getPurchaseRequestCount($conn, -1, 0, 0, 0, 0, 0, $result["UserID"]));
                                ?></td>
                            </tr>

                            <tr>
                                <td>Total Approved</td>
                                <td><?php
                                    echo(getPurchaseRequestCount($conn, 1, 0, 0, 0, 0, 0, $result["UserID"]));
                                ?></td>
                            </tr>

                            <tr>
                                <td>Total Rejected</td>
                                <td><?php
                                    echo(getPurchaseRequestCount($conn, 2, 0, 0, 0, 0, 0, $result["UserID"]));
                                ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="w3-container w3-quarter w3-sidebar w3-bar-block w3-theme-d5" style="width:25%;">
                    <br>
                    <form method="get" action="/Admin/editAdmin.php">
                        <input type="hidden" name="AdminID" value="<?php
                            echo($adminID);
                        ?>">
                        <input class="fullW" type="submit" value="Edit Admin ID <?php
                            echo($adminID);
                        ?>" style="max-width:100%;">
                    </form>

                    <form method="get" action="/Admin/deleteAdmin.php">
                        <input type="hidden" name="AdminID" value="<?php
                            echo($adminID);
                        ?>">
                        <input class="fullW" type="submit" value="*** Delete Admin ID <?php
                            echo($adminID);
                        ?> ***" style="max-width:100%;">
                    </form>

                    <form method="get" action="/Admin/manageAdmin.php">
                        <input class="fullW" type="submit" value="Back to View All Admins">
                    </form>
                </div>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
