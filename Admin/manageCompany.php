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
    $allCompany = getAllCompany($conn, $_SESSION["UserID"], $companyID);
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

                <form class="w3-center" method="get" action="/Admin/manageCompany.php">
                    <input style="width:98%" id="SearchKey" type="number" name="SearchKey" value="<?php
                        // Valid CompanyID.
                        if ($companyID > 0) {
                            echo($companyID);
                        }
                    ?>" placeholder="Enter Company ID" min="1" required>
                    
                    <!-- <label for="SearchOption">Search By:</label> -->
                    <select id="SearchOption" name="SearchOption">
                        <option value="1"<?php
                            if ($searchOption == 1) {
                                echo(" selected");
                            }
                        ?>>CompanyID</option>
                    </select>

                    <input type="submit" value="Search">

                    <input form="reset-search" type="submit" value="Reset"<?php
                        // Disable if not searching.
                        if ($companyID < 1) {
                            echo(" disabled");
                        }
                    ?>>
                </form>

                <div class="w3-container w3-center" style="align-content:center;">
                <?php if (count($allCompany) > 0): ?>
                        <table class=" w3-center w3-table-all w3-centered w3-hoverable" style="width:100%">
                            <tr>
                                <th>Company ID</th>
                                <th>User ID</th>
                                <th>Username</th>
                                <th>Real Name</th>
                                <th>Establish Date</th>
                            </tr>
                            <?php foreach ($allCompany as $result): ?>
                                <tr>
                                    <td><?php
                                        echo($result["CompanyID"]);
                                    ?></td>

                                    <td><?php
                                        echo(getCompanyCount($conn, $_SESSION["UserID"], $result["CompanyID"]));
                                    ?></td>

                                    <td><?php
                                        echo(getTreeCount($conn, $_SESSION["UserID"], $result["CompanyID"]));
                                    ?></td>

                                    <td><?php
                                        echo(getPurchaseRequestCount($conn, 1, $_SESSION["UserID"], $result["CompanyID"]));
                                    ?></td>
                            
                                    <td>
                                        <form method="get" action="/Company/registration.php">
                                            <input type="hidden" name="CompanyID" value="<?php
                                                echo($result["CompanyID"]);
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
                                echo(
                                    "Company ID $companyID is not associated with any company of " .
                                    $_SESSION["Username"] . "!"
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