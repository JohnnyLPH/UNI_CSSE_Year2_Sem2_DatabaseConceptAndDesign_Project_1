<?php
    // Company Manage Block Page.
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dbConnection.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/loginAuthenticate.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dataManagement.php");

    $tempLoginCheck = checkLogin($conn);
    // Not logged in as Client.
    if ($tempLoginCheck != 3) {
        header("Location: /index.php");
        exit;
    }

    // Return all the block & latest client.
    $allBlock = getBlockLatestClient($conn, 0, 0, 0, $_SESSION["UserID"]);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Client: View Block Page</title>
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
                <h1>Client: View Block Page</h1>
            </div>
        </header>

        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Client/navigationBar.php"); ?>

        <main>
            <div class="w3-container w3-theme-d4 w3-animate-opacity">
                <h2 class="w3-center">All Blocks:</h2>

                <div class="w3-container w3-center" style="align-content:center;">
                    <?php if (count($allBlock) > 0): ?>
                        <table class=" w3-center w3-table-all w3-centered w3-hoverable" style="width:100%">
                            <tr>
                                <th>No.</th>
                                <th>Block ID</th>
                                <th>Orchard ID</th>
                                <th>Company Name</th>
                                <th>Total Tree</th>
                                <!-- <th>Status</th> -->
                                <th>Action</th>
                            </tr>
                            <?php $counter = 1; ?>
                            <?php foreach ($allBlock as $result): ?>
                                <tr>
                                    <td>
                                        <?php echo($counter++); ?>
                                    </td>
                                    <td><?php
                                        echo($result["BlockID"]);
                                    ?></td>

                                    <td><?php
                                        echo($result["OrchardID"]);
                                    ?></td>

                                    <td>
                                        <?php 
                                            $company = getAllCompany($conn, $result["CompanyID"]);
                                            echo($company[0]["RealName"]);
                                        ?>
                                    </td>

                                    

                                    <td><?php
                                        echo(getTreeCount(
                                            $conn, $result["CompanyID"], $result["OrchardID"], $result["BlockID"]
                                        ));
                                    ?></td>
                                    
                                    <td>
                                        <form method="GET" action="/Client/view_the_block.php">
                                            <input type="hidden" name="BlockID" value="<?php
                                                echo($result["BlockID"]);
                                            ?>">
                                            <input type="submit" name="view_block" value="View">
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                        <br>
                    <?php else: ?>
                        <span>* No block is found! *</span>
                    <?php endif; ?>
                </div>
            </div>
        </main>

        <footer>
            <?php $conn->close(); ?>
        </footer>
    </body>
</html>
