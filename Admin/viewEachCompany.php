<?php
    // Admin Manage Company Page.
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

    $allCompany = NULL;
    // Company is not available for viewing.
    if (
        !isset($queryString["CompanyID"]) ||
        !is_numeric($queryString["CompanyID"]) ||
        $queryString["CompanyID"] < 1 ||
        count($allCompany = getAllCompany($conn, $queryString["CompanyID"])) < 1
    ) {
        header("Location: /Admin/manageCompany.php");
        exit;
    }

    $companyID = $queryString["CompanyID"];
    $result = $allCompany[0];
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Admin: Manage Company Page</title>
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
                <h1>Admin: Manage Company Page</h1>
            </div>
        </header>

        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Admin/navigationBar.php"); ?>

        <main>
            <div class="w3-row">
                <div class="w3-container w3-threequarter w3-theme-d4 w3-animate-left" style="margin-left:25%; padding-bottom:2%; padding-top:2%;">

                    <div class="w3-container w3-half">
                        <h2>Company ID <?php
                            echo($companyID);
                        ?>:</h2> 

                        <table class="w3-table-all">
                            <tr>
                                <td>Company ID</td>
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
                                <td>Company Name</td>
                                <td><?php
                                    echo($result["RealName"]);
                                ?></td>
                            </tr>

                            <tr>
                                <td>Establish Date</td>
                                <td><?php
                                    echo($result["EstablishDate"]);
                                ?></td>
                            </tr>

                            <tr>
                                <td>Total Staff</td>
                                <td><?php
                                    echo(getStaffCount($conn, $result["UserID"]));
                                ?></td>
                            </tr>

                            <tr>
                                <td>Total Orchard</td>
                                <td><?php
                                    echo(getOrchardCount($conn, $result["UserID"]));
                                ?></td>
                            </tr>

                            <tr>
                                <td>Total Block</td>
                                <td><?php
                                    echo(getBlockCount($conn, $result["UserID"]));
                                ?></td>
                            </tr>

                            <tr>
                                <td>Total Tree</td>
                                <td><?php
                                    echo(getTreeCount($conn, $result["UserID"]));
                                ?></td>
                            </tr>

                            <tr>
                                <td>Client Purchase</td>
                                <td><?php
                                    echo(getPurchaseRequestCount($conn, 1, $result["UserID"]));
                                ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="w3-container w3-quarter w3-sidebar w3-bar-block w3-theme-d5" style="width:25%;">
                    <br>
                    <form method="get" action="/Admin/editCompany.php">
                        <input type="hidden" name="CompanyID" value="<?php
                            echo($companyID);
                        ?>">
                        <input class="fullW" type="submit" value="Edit Company ID <?php
                            echo($companyID);
                        ?>" style="max-width:100%;">
                    </form>

                    <form method="get" action="/Admin/deleteCompany.php">
                        <input type="hidden" name="CompanyID" value="<?php
                            echo($companyID);
                        ?>">
                        <input class="fullW" type="submit" value="*** Delete Company ID <?php
                            echo($companyID);
                        ?> ***" style="max-width:100%;">
                    </form>

                    <form method="get" action="/Admin/manageOrchard.php">
                        <input type="hidden" name="SearchKey" value="<?php
                            echo($companyID);
                        ?>">
                        <input type="hidden" name="SearchOption" value="1">
                        <input class="fullW" type="submit" value="View Related Orchards">
                    </form>

                    <form method="get" action="/Admin/manageBlock.php">
                        <input type="hidden" name="SearchKey" value="<?php
                            echo($companyID);
                        ?>">
                        <input type="hidden" name="SearchOption" value="1">
                        <input class="fullW" type="submit" value="View Related Blocks">
                    </form>

                    <form method="get" action="/Admin/manageTree.php">
                        <input type="hidden" name="SearchKey" value="<?php
                            echo($companyID);
                        ?>">
                        <input type="hidden" name="SearchOption" value="1">
                        <input class="fullW" type="submit" value="View Related Trees">
                    </form>
                    
                    <form method="get" action="/Admin/managePurchase.php">
                        <input type="hidden" name="SearchKey" value="<?php
                            echo($companyID);
                        ?>">
                        <input type="hidden" name="SearchOption" value="21">
                        <input class="fullW" type="submit" value="View Related Purchases">
                    </form>
                    
                    <form method="get" action="/Admin/manageCompany.php">
                        <input class="fullW" type="submit" value="Back to View All Companies">
                    </form>
                </div>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
