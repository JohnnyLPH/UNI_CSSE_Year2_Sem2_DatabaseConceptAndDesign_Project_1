<?php
    // Admin Manage Staff Page.
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
    
    // Check if valid CompanyID or StaffID is provided for search, set to 0 if not.
    $companyID = $staffID = (
        !isset($queryString["SearchKey"]) ||
        !is_numeric($queryString["SearchKey"]) ||
        $queryString["SearchKey"] < 1
    ) ? 0: $queryString["SearchKey"];

    // Check if valid SearchOption is provided.
    $searchOption = (
        !isset($queryString["SearchOption"]) ||
        !is_numeric($queryString["SearchOption"]) ||
        $queryString["SearchOption"] < 1 ||
        $queryString["SearchOption"] > 2
    ) ? 1: $queryString["SearchOption"];

    // Search by CompanyID.
    if ($searchOption == 1) {
        $staffID = 0;
    }
    // Search by StaffID.
    else {
        $companyID = 0;
    }

    // Return all the staff.
    $allStaff = getAllStaff($conn, $companyID, $staffID);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Admin: Manage Staff Page</title>
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
                <h1>Admin: Manage Staff Page</h1>
            </div>
        </header>

        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Admin/navigationBar.php"); ?>

        <main>
            <div class="w3-container w3-theme-d4 w3-animate-opacity">
                <h2 class="w3-center">All Staff:</h2>

                <form id="reset-search" method="get" action="/Admin/manageStaff.php"></form>
                <form id="register-staff" method="get" action="/Admin/registerStaff.php"></form>

                <form class="w3-center" method="get" action="/Admin/manageStaff.php">
                    <input style="width:98%" id="SearchKey" type="number" name="SearchKey" value="<?php
                        // Valid CompanyID.
                        if ($companyID > 0) {
                            echo($companyID);
                        }
                        // Valid StaffID.
                        elseif ($staffID > 0) {
                            echo($staffID);
                        }
                    ?>" placeholder="Enter Company/Staff ID" min="1" required>
                    
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
                        ?>>StaffID</option>
                    </select>
                    
                    <input type="submit" value="Search">

                    <input form="reset-search" type="submit" value="Reset"<?php
                        // Disable if not searching.
                        if ($companyID + $staffID < 1) {
                            echo(" disabled");
                        }
                    ?>>

                    <input form="register-staff" type="submit" value="Register New Staff">
                </form>

                <div class="w3-container w3-center" style="align-content:center;">
                    <?php if (count($allStaff) > 0): ?>
                        <table class=" w3-center w3-table-all w3-centered w3-hoverable" style="width:100%">
                            <tr>
                                <th>Staff ID</th>
                                <th>Username</th>
                                <th>Salary</th>
                                <th>Total Tree Update</th>
                                <th>Action</th>
                            </tr>
                            <?php foreach ($allStaff as $result): ?>
                                <tr>
                                    <td><?php
                                        echo($result["UserID"]);
                                    ?></td>

                                    <td><?php
                                        echo($result["Username"]);
                                    ?></td>

                                    <td><?php
                                        echo($result["Salary"]);
                                    ?></td>

                                    <td><?php
                                        echo(
                                            count(
                                                getAllTreeUpdate(
                                                    $conn, 0, 0, 0, 0, 0, $result["UserID"]
                                                )
                                            )
                                        );
                                    ?></td>
                            
                                    <td>
                                        <form method="get" action="/Admin/viewEachStaff.php">
                                            <input type="hidden" name="StaffID" value="<?php
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
                            if ($companyID + $staffID < 1) {
                                echo("No Staff is found!");
                            }
                            elseif ($searchOption == 1) {
                                echo("Company ID $companyID is not associated with any Staff!");
                            }
                            else {
                                echo("Staff ID $staffID is not associated with any Staff!");
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