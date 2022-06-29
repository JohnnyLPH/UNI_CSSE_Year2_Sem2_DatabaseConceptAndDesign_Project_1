<?php
    // Admin Manage Staff Page.
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dbConnection.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/loginAuthenticate.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/inputValidation.php");
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

    $allStaff = NULL;
    // Staff is not available for viewing.
    if (
        !isset($queryString["StaffID"]) ||
        !is_numeric($queryString["StaffID"]) ||
        $queryString["StaffID"] < 1 ||
        count($allStaff = getAllStaff($conn, 0, $queryString["StaffID"])) < 1
    ) {
        header("Location: /Admin/manageStaff.php");
        exit;
    }

    $staffID = $queryString["StaffID"];
    $result = $allStaff[0];

    $allTreeUpdate = getAllTreeUpdate($conn, 0, 0, 0, 0, 0, $result["UserID"]);
    $allTreeUpdateComp = getAllTreeUpdate($conn, $result["CompanyID"], 0, 0, 0, 0, $result["UserID"]);
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
            <div class="w3-row">
                <div class="w3-container w3-threequarter w3-theme-d4 w3-animate-left" style="margin-left:25%; padding-bottom:2%; padding-top:2%;">
                    <div class="w3-container">
                        <h2>Staff ID <?php
                            echo($staffID);
                        ?>:</h2> 

                        <table class="w3-table-all">
                            <tr>
                                <td>Staff ID</td>
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
                                <td>Staff Name</td>
                                <td><?php
                                    echo($result["RealName"]);
                                ?></td>
                            </tr>

                            <tr>
                                <td>Company ID</td>
                                <td><?php
                                    echo($result["CompanyID"]);
                                ?></td>
                            </tr>

                            <tr>
                                <td>Employment Date</td>
                                <td><?php
                                    echo($result["EmployDate"]);
                                ?></td>
                            </tr>

                            <tr>
                                <td>Salary (RM)</td>
                                <td><?php
                                    echo($result["Salary"]);
                                ?></td>
                            </tr>

                            <tr>
                                <td>Total Tree Update</td>
                                <td><?php
                                    echo(count($allTreeUpdate));
                                ?></td>
                            </tr>

                            <tr>
                                <td>Tree Update (in Company-Owned Orchard)</td>
                                <td><?php
                                    echo(count($allTreeUpdateComp));
                                ?></td>
                            </tr>
                        </table>

                        <h3>Tree Update History:</h3>
                        <?php if (count($allTreeUpdate) > 0): ?>
                            <table class=" w3-center w3-table-all w3-centered w3-hoverable" style="width:100%">
                                <tr>
                                    <th>Update ID</th>
                                    <th>Tree ID</th>
                                    <th>Block ID</th>
                                    <th>Update Date</th>
                                    <th>Image</th>
                                    <th>Height (m)</th>
                                    <th>Diameter (m)</th>
                                    <th>Status</th>
                                </tr>
                                <?php foreach ($allTreeUpdate as $treeResult): ?>
                                    <tr>
                                        <td><?php
                                            echo($treeResult["UpdateID"]);
                                        ?></td>

                                        <td><?php
                                            echo($treeResult["TreeID"]);
                                        ?></td>

                                        <td><?php
                                            echo($treeResult["BlockID"]);
                                        ?></td>

                                        <td><?php
                                            echo($treeResult["UpdateDate"]);
                                        ?></td>

                                        <td><img id="icon" src="<?php
                                            echo(cleanInput($treeResult["TreeImage"]));
                                        ?>" alt="* UpdateID <?php
                                            echo($treeResult["UpdateID"]);
                                        ?> img *" width="350"></td>

                                        <td><?php
                                            echo($treeResult["TreeHeight"]);
                                        ?></td>

                                        <td><?php
                                            echo($treeResult["Diameter"]);
                                        ?></td>

                                        <td><?php
                                            echo(getTreeUpdateStatus($treeResult["Status"]));
                                        ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        <?php else: ?>
                            <span>* No Update History for Staff ID <?php
                                echo($staffID);
                            ?>! *</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="w3-container w3-quarter w3-sidebar w3-bar-block w3-theme-d5" style="width:25%;">
                    <br>
                    <form method="get" action="/Admin/editStaff.php">
                        <input type="hidden" name="StaffID" value="<?php
                            echo($staffID);
                        ?>">
                        <input class="fullW" type="submit" value="Edit Staff ID <?php
                            echo($staffID);
                        ?>" style="max-width:100%;">
                    </form>

                    <form method="get" action="/Admin/deleteStaff.php">
                        <input type="hidden" name="StaffID" value="<?php
                            echo($staffID);
                        ?>">
                        <input class="fullW" type="submit" value="*** Delete Staff ID <?php
                            echo($staffID);
                        ?> ***" style="max-width:100%;">
                    </form>

                    <form method="get" action="/Admin/viewEachCompany.php">
                        <input type="hidden" name="CompanyID" value="<?php
                            echo($result["CompanyID"]);
                        ?>">
                        <input class="fullW" type="submit" value="View Related Company">
                    </form>

                    <form method="get" action="/Admin/manageStaff.php">
                        <input class="fullW" type="submit" value="Back to View All Staff">
                    </form>
                </div>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
