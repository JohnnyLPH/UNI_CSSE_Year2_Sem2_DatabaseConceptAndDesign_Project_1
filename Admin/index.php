<?php
    // Admin Home Page.
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dbConnection.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/loginAuthenticate.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dataManagement.php");

    $tempLoginCheck = checkLogin($conn);
    // Not logged in as Admin.
    if ($tempLoginCheck != 4) {
        header("Location: /index.php");
        exit;
    }

    $totalAdmin = getAdminCount($conn);
    $totalCompany = getCompanyCount($conn);
    $totalStaff = getStaffCount($conn);
    $totalClient = getClientCount($conn);
    $totalOrchard = getOrchardCount($conn);
    $totalBlock = getBlockCount($conn);
    $totalTree = getTreeCount($conn);
    $totalPurchase = getPurchaseRequestCount($conn, 1);

    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Admin: Home Page</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">
        
        <link rel="stylesheet" href="/css/main.css">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <!--<link rel="shortcut icon" href="/favicon.ico">-->
        <link rel="shortcut icon" href="https://icon-library.com/images/tree-icon/tree-icon-23.jpg">

        <!-- Chart.js-->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-datalabels/2.0.0/chartjs-plugin-datalabels.min.js"
        integrity="sha512-R/QOHLpV1Ggq22vfDAWYOaMd5RopHrJNMxi8/lJu8Oihwi4Ho4BRFeiMiCefn9rasajKjnx9/fTQ/xkWnkDACg==" 
        crossorigin="anonymous" referrerpolicy="no-referrer"></script> 
        <!-- Google Chart API-->
        <script type='text/javascript'src='https://www.gstatic.com/charts/loader.js'></script>
    </head>

    <body>
        <header>
            <div class="maintheme w3-container">
                <h1>Admin: Home Page</h1>
            </div>
        </header>

        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Admin/navigationBar.php"); ?>

        <main>
            <div class="w3-row">
                <div class="w3-container w3-quarter w3-sidebar w3-bar-block w3-theme-d5" style="width:25%">
                    <div class="w3-center">
                        <h2 class="w3-center w3-bar-item" >Welcome, <?php
                            echo($_SESSION["Username"]);
                        ?></h2>
                        <img src="https://cdn-icons-png.flaticon.com/512/270/270023.png" id="icon" alt="User Icon" /><br><br>
                    </div>
                    <button class="fullW" onclick="document.location='/Admin/reportCompany.php'">View Company Report</button><br><br>
                    <button class="fullW" onclick="document.location='/Admin/reportClient.php'">View Client Report</button><br><br>
                    <button class="fullW" onclick="document.location='/Admin/reportStaff.php'">View Staff Report</button><br><br>
                    <button class="fullW" onclick="document.location='/Admin/reportSales.php'">View Sales Report</button><br><br>
                </div>
                
                <div class="wrapper w3-container w3-threequarter w3-theme-d4" style="margin-left:25%;">
                    <h2>Your Summary:</h2>
                </div>

                <div class="w3-container w3-threequarter wrapper bgImgTree w3-animate-left" style="margin-left:25%;">
                    <div class='data-value card fadeIn first'>
                        <div class='data-group'>
                            <img src="/img/defaults/adminIcon.png" id="icon" alt="User Icon" />
                            <br>
                            <span class='overall-data'><?php
                                echo($totalAdmin);
                            ?></span>
                            <span class='data-title'>Total Admin User</span>
                        </div>
                    </div>
                    <div class='data-value card fadeIn second'>
                        <div class='data-group'>
                            <img src="/img/defaults/companyIcon.jpg" id="icon" alt="User Icon" />
                            <br>
                            <span class='overall-data'><?php
                                echo($totalCompany);
                            ?></span>
                            <span class='data-title'>Total Company User</span>
                        </div>
                    </div>
                    <div class='data-value card fadeIn third'>
                        <div class='data-group'>
                            <img src="/img/defaults/staffIcon.jpg" id="icon" alt="User Icon" />
                            <br>
                            <span class='overall-data'><?php
                                echo($totalStaff);
                            ?></span>
                            <span class='data-title'>Total Staff User</span>
                        </div>
                    </div>
                    <div class='data-value card fadeIn fourth'>
                        <div class='data-group'>
                            <img src="/img/defaults/clientIcon.jpg" id="icon" alt="User Icon" />
                            <br>
                            <span class='overall-data'><?php
                                echo($totalClient);
                            ?></span>
                            <span class='data-title'>Total Client User</span>
                        </div>
                    </div>

                    <div class='data-value card fadeIn first'>
                        <div class='data-group'>
                            <img src="https://us.123rf.com/450wm/goodstudio/goodstudio1910/goodstudio191000131/131189697-family-working-in-fruit-garden-together-flat-vector-illustration-people-gathering-apples-berries-and.jpg" id="icon" alt="User Icon" />
                            <br>
                            <span class='overall-data'><?php
                                echo($totalOrchard);
                            ?></span>
                            <span class='data-title'>Total Orchard Record</span>
                        </div>
                    </div>
                    <div class='data-value card fadeIn second'>
                        <div class='data-group'>
                            <img src="https://i.pinimg.com/originals/07/20/ad/0720add75420ae4ad05075760c5c0723.jpg" id="icon" alt="User Icon" />
                            <br>
                            <span class='overall-data'><?php
                                echo($totalBlock);
                            ?></span>
                            <span class='data-title'>Total Block Record</span>
                        </div>
                    </div>
                    <div class='data-value card fadeIn third'>
                        <div class='data-group'>
                            <img src="https://static.vecteezy.com/system/resources/previews/002/140/928/non_2x/gardening-concept-illustration-with-man-and-women-planting-a-tree-free-vector.jpg" id="icon" alt="User Icon" />
                            <br>
                            <span class='overall-data'><?php
                                echo($totalTree);
                            ?></span>
                            <span class='data-title'>Total Tree Record</span>
                        </div>
                    </div>
                    <div class='data-value card fadeIn fourth'>
                        <div class='data-group'>
                            <img src="https://img.freepik.com/free-vector/shop-with-sign-we-are-open_23-2148547718.jpg" id="icon" alt="User Icon" />
                            <br>
                            <span class='overall-data'><?php
                                echo($totalPurchase);
                            ?></span>
                            <span class='data-title'>Success Client Purchase</span>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>


