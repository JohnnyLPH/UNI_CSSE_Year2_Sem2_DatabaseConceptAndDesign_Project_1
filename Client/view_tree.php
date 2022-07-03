<?php
    // Admin Manage PurchaseRequest Page.
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dbConnection.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/loginAuthenticate.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/inputValidation.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dataManagement.php");

    $tempLoginCheck = checkLogin($conn);

    // Not logged in as Client.
    if ($tempLoginCheck != 3) {
        header("Location: /index.php");
        exit;
    }

    $queryString = array();

    if (isset($_SERVER['QUERY_STRING'])) {
        parse_str($_SERVER['QUERY_STRING'], $queryString);
    }

    $trees = NULL;
    $previous_url = $_SERVER["HTTP_REFERER"];

    if(
        !isset($queryString["TreeID"]) ||  
        count($trees = getAllTree($conn, 0, 0, 0, $queryString["TreeID"])) < 1
    ) {
        header("Location: " . $previous_url);
        exit;
    }

    $treeid = $queryString["TreeID"];
    $result = getAllTreeUpdate($conn, 0, 0, 0, $treeid);
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client: View Block Page</title>

    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <!--<link rel="shortcut icon" href="/favicon.ico">-->
    <link rel="shortcut icon" href="https://icon-library.com/images/tree-icon/tree-icon-23.jpg">

    <script src=".../css/stickynav.js"></script>
</head>
<body>
    <header>
        <div class="maintheme w3-container">
            <h1>Client: View Block Page</h1>
        </div>
    </header>

    <?php include($_SERVER['DOCUMENT_ROOT'] . "/Client/navigationBar.php"); ?>

    <main>
        <div class="w3-row">
            <div class="w3-container w3-quarter w3-sidebar w3-bar-block w3-theme-d5" style="width:25%;">
                <br>

                <form method="post" action="<?php echo($previous_url); ?>">
                    <input class="fullW" name="back" id="back" type="submit" value="Back to View All Blocks">
                </form>

                <?php if(!empty($result[0]["TreeImage"])): ?>
                    <img id="icon" src="<?php echo(cleanInput($result[0]["TreeImage"])); ?>" alt="* TreeID <?php echo($treeid); ?> img *" width="350">
                <?php endif; ?>
            </div>
            <div class="w3-container w3-threequarter w3-theme-d4 w3-animate-left" style="margin-left:25%; padding-bottom:2%;">
                <h2>Tree ID <?php echo($treeid); ?>:</h2>

                <table class=" w3-center w3-table-all w3-hoverable" style="width:100%">
                    <tr>
                        <td>Species Name</td>
                        <td>
                            <?php 
                                echo($trees[0]["SpeciesName"]);
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <td>Block ID</td>
                        <td>
                            <?php 
                                echo($trees[0]["BlockID"]);
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <td>Location</td>
                        <td>
                            <?php
                                $location = $trees[0]["Latitude"] . ", " . $trees[0]["Longitude"];
                                echo($location);
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <td>Plant Date</td>
                        <td>
                            <?php 
                                echo($trees[0]["PlantDate"]);
                            ?>
                        </td>
                    </tr>
                </table>

                <h3>Tree Update History</h3>
                <?php if(count($result) > 0): ?>
                    <table class=" w3-center w3-table-all w3-centered w3-hoverable" style="width:100%">
                        <tr>
                            <th>Update ID</th>
                            <th>Height</th>
                            <th>Diameter</th>
                            <th>Status</th>
                            <th>Staff</th>
                            <th>Update Date</th>
                        </tr>
                        <?php foreach($result as $row): ?>
                            <tr>
                                <td>
                                    <?php echo($row["UpdateID"]); ?>
                                </td>
                                <td>
                                    <?php echo($row["TreeHeight"]); ?>
                                </td>
                                <td>
                                    <?php echo($row["Diameter"]); ?>
                                </td>
                                <td>
                                    <?php 
                                        $status = getTreeUpdateStatus($row["Status"]);
                                        echo($status); 
                                    ?>
                                </td>
                                <td>
                                    <?php
                                        $name = getAllStaff($conn, 0, $row["StaffID"]); 
                                        echo($name[0]["RealName"]); 
                                    ?>
                                </td>
                                <td>
                                    <?php echo($row["UpdateDate"]); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php else: ?>
                    <span>* No Tree Update for Tree ID <?php
                        echo($treeid);
                    ?>! *</span>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>