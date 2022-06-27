<?php
    // Company Manage Orchard Page.
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dbConnection.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/loginAuthenticate.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dataManagement.php");

    $tempLoginCheck = checkLogin($conn);
    // Not logged in as Company.
    if ($tempLoginCheck != 1) {
        header("Location: /index.php");
        exit;
    }

    $queryString = array();

    if (isset($_SERVER['QUERY_STRING'])) {
        parse_str($_SERVER['QUERY_STRING'], $queryString);
    }

    $allOrchard = NULL;
    // Orchard is not available for viewing.
    if (
        !isset($queryString["OrchardID"]) ||
        !is_numeric($queryString["OrchardID"]) ||
        $queryString["OrchardID"] < 1 ||
        count($allOrchard = getAllOrchard($conn, $_SESSION["UserID"], $queryString["OrchardID"])) < 1
    ) {
        header("Location: /Company/manageOrchard.php");
        exit;
    }

    $orchardID = $queryString["OrchardID"];
    $result = $allOrchard[0];
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Company: Manage Orchard Page</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">

        <link rel="stylesheet" href="/css/main.css">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <!--<link rel="shortcut icon" href="/favicon.ico">-->
        <link rel="shortcut icon" href="https://icon-library.com/images/tree-icon/tree-icon-23.jpg">

        <script src="https://maps.google.com/maps/api/js?sensor=false"></script>
        <script>
            function showPosition() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(showMap, showError);
                } else {
                    alert("Sorry, your browser does not support HTML5 geolocation.");
                }
            }
            
            // Define callback function for successful attempt
            function showMap(position) {
                // Get location data
                long = <?php echo $result["Longitude"] ?>;
                lat = <?php echo $result["Latitude"] ?>;
                var latlong = new google.maps.LatLng(lat, long);
                
                var myOptions = {
                    center: latlong,
                    zoom: 16,
                    mapTypeControl: true,
                    navigationControlOptions: {
                        style:google.maps.NavigationControlStyle.SMALL
                    }
                }
                
                var map = new google.maps.Map(document.getElementById("embedMap"), myOptions);
                var marker = new google.maps.Marker({ position:latlong, map:map, title:"You are here!" });
            }
            
            // Define callback function for failed attempt
            function showError(error) {
                if(error.code == 1) {
                    result.innerHTML = "You've decided not to share your position, but it's OK. We won't ask you again.";
                } else if(error.code == 2) {
                    result.innerHTML = "The network is down or the positioning service can't be reached.";
                } else if(error.code == 3) {
                    result.innerHTML = "The attempt timed out before it could get the location data.";
                } else {
                    result.innerHTML = "Geolocation failed due to unknown error.";
                }
            }
            window.onload = showPosition;
        </script>
    </head>

    <body>
        <header>
            <div class="maintheme w3-container">
                <h1>Company: Manage Orchard Page</h1>
            </div>
        </header>

        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Company/navigationBar.php"); ?>

        <main>
            <div class="w3-row">
                <div class="w3-container w3-threequarter w3-theme-d4 w3-animate-left" style="margin-left:25%; padding-bottom:2%; padding-top:2%;">

                    <div class="w3-container w3-half">
                        <h2>Orchard ID <?php
                            echo($orchardID);
                        ?>:</h2> 

                        <table class="w3-table-all">
                            <tr>
                                <td>Orchard ID</td>
                                <td><?php
                                    echo($result["OrchardID"]);
                                ?></td>
                            </tr>

                            <tr>
                                <td>Address</td>
                                <td><?php
                                    echo($result["Address"]);
                                ?></td>
                            </tr>

                            <tr>
                                <td>Latitude</td>
                                <td><?php
                                    echo($result["Latitude"]);
                                ?></td>
                            </tr>

                            <tr>
                                <td>Longitude</td>
                                <td><?php
                                    echo($result["Longitude"]);
                                ?></td>
                            </tr>

                            <tr>
                                <td>Company ID</td>
                                <td><?php
                                    echo($result["CompanyID"]);
                                ?></td>
                            </tr>

                            <tr>
                                <td>Total Block</td>
                                <td><?php
                                    echo(getBlockCount($conn, $_SESSION["UserID"], $result["OrchardID"]));
                                ?></td>
                            </tr>

                            <tr>
                                <td>Total Tree</td>
                                <td><?php
                                    echo(getTreeCount($conn, $_SESSION["UserID"], $result["OrchardID"]));
                                ?></td>
                            </tr>

                            <tr>
                                <td>Client Purchase</td>
                                <td><?php
                                    echo(getPurchaseRequestCount($conn, 1, $_SESSION["UserID"], $result["OrchardID"]));
                                ?></td>
                            </tr>
                        </table>
                    </div>

                    <div id="embedMap" style="width: 490px; height: 490px;" class="w3-container w3-half">
                        <!--Google map will be embedded here-->
                    </div>
                </div>
                <div class="w3-container w3-quarter w3-sidebar w3-bar-block w3-theme-d5" style="width:25%;">
                    <br>
                    <form method="get" action="/Company/manageBlock.php">
                        <input type="hidden" name="SearchKey" value="<?php
                            echo($orchardID);
                        ?>">
                        <input type="hidden" name="SearchOption" value="1">
                        <input class="fullW" type="submit" value="View Related Blocks" style="max-width:100%;">
                    </form>

                    <form method="get" action="/Company/manageTree.php">
                        <input type="hidden" name="SearchKey" value="<?php
                            echo($orchardID);
                        ?>">
                        <input type="hidden" name="SearchOption" value="1">
                        <input class="fullW" type="submit" value="View Related Trees">
                    </form>
                    
                    <form method="get" action="/Company/managePurchase.php">
                        <input type="hidden" name="SearchKey" value="<?php
                            echo($orchardID);
                        ?>">
                        <input type="hidden" name="SearchOption" value="1">
                        <input class="fullW" type="submit" value="View Related Purchases">
                    </form>
                    
                    <form method="get" action="/Company/manageOrchard.php">
                        <input class="fullW" type="submit" value="Back to View All Orchards">
                    </form>
                </div>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
