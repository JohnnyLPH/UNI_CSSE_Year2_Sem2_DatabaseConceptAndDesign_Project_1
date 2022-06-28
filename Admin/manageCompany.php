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
    
    // Check if valid CompanyID is provided for search, set to 0 if not.
    $companyID = (
        !isset($queryString["SearchKey"]) ||
        !is_numeric($queryString["SearchKey"]) ||
        $queryString["SearchKey"] < 1
    ) ? 0: $queryString["SearchKey"];

    // Return all the company.
    $allCompany = getAllCompany($conn, $companyID);
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
            <div class="w3-container w3-theme-d4 w3-animate-opacity">
                <h2 class="w3-center">All Companies:</h2>

                <form id="reset-search" method="get" action="/Admin/manageCompany.php"></form>
                <form id="register-company" method="get" action="/Admin/registerCompany.php"></form>

                <form class="w3-center" method="get" action="/Admin/manageCompany.php">
                    <input style="width:98%" id="SearchKey" type="number" name="SearchKey" value="<?php
                        // Valid CompanyID.
                        if ($companyID > 0) {
                            echo($companyID);
                        }
                    ?>" placeholder="Enter Company ID" min="1" required>
                    
                    <input type="submit" value="Search">

                    <input form="reset-search" type="submit" value="Reset"<?php
                        // Disable if not searching.
                        if ($companyID < 1) {
                            echo(" disabled");
                        }
                    ?>>

                    <input form="register-company" type="submit" value="Register New Company">
                </form>

                <div class="w3-container w3-center" style="align-content:center;">
                    <?php if (count($allCompany) > 0): ?>
                        <table class=" w3-center w3-table-all w3-centered w3-hoverable" style="width:100%">
                            <tr>
                                <th>Company ID</th>
                                <th>Username</th>
                                <th>Total Staff</th>
                                <th>Total Block</th>
                                <th>Total Tree</th>
                                <th>Action</th>
                            </tr>
                            <?php foreach ($allCompany as $result): ?>
                                <tr>
                                    <td><?php
                                        echo($result["UserID"]);
                                    ?></td>

                                    <td><?php
                                        echo($result["Username"]);
                                    ?></td>

                                    <td><?php
                                        echo(getStaffCount($conn, $result["UserID"]));
                                    ?></td>

                                    <td><?php
                                        echo(getBlockCount($conn, $result["UserID"]));
                                    ?></td>

                                    <td><?php
                                        echo(getTreeCount($conn, $result["UserID"]));
                                    ?></td>
                            
                                    <td>
                                        <form method="get" action="/Admin/viewEachCompany.php">
                                            <input type="hidden" name="CompanyID" value="<?php
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
                            if ($companyID < 1) {
                                echo("No company is found!");
                            }
                            else {
                                echo("Company ID $companyID is not associated with any company!");
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