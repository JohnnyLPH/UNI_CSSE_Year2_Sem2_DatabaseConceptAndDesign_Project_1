<?php
    // Company Manage Orchard Page.
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dbConnection.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/loginAuthenticate.php");
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

    // Block is not available for viewing.
    if (
        !isset($queryString["BlockID"]) ||
        !is_numeric($queryString["BlockID"]) ||
        $queryString["BlockID"] < 1
    ) {
        header("Location: /Client/view_block.php");
        exit;
    }

    $blockID = $queryString["BlockID"];

    function displayTrees($conn, $blockID) {
        $result = array();
        $result = getAllTree($conn, 0, 0, $blockID, 0);

        if(sizeof($result) <= 0) {
            return;
        }

        $counter = 1;
        foreach($result as $row) {
            $location = $row["Latitude"] . ", " . $row["Longitude"];
            echo("
                <tr>
                    <td>" . $counter . "</td>
                    <td>" . $row["TreeID"] . "</td>
                    <td>" . $row["SpeciesName"] . "</td>
                    <td>" . $location . "</td>
                    <td>" . $row["PlantDate"] . "</td>
                    <td>
                        <form method=\"get\" action=\"/Client/view_tree.php\">
                            <input type=\"hidden\" name=\"TreeID\" value=\"" .
                                $row["TreeID"] . "\">
                            <input type=\"submit\" value=\"View\">
                        </form>
                    </td>
                </tr>"
            );

            $counter++;
        }
    }
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
            <div class="w3-container w3-theme-d4 w3-animate-opacity">
                <h2 class="w3-center"> Block ID: <?php echo($blockID); ?></h2>

                <div class="w3-container w3-center" style="align-content:center;">
                    <table class=" w3-center w3-table-all w3-centered w3-hoverable" style="width:100%">
                        <tr>
                            <th>No.</th>
                            <th>Tree ID</th>
                            <th>Species Name</th>
                            <th>Location</th>
                            <th>Plant Date</th>
                            <th>Action</th>
                        </tr>

                        <?php displayTrees($conn, $blockID); ?>
                    </table>
                </div>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>