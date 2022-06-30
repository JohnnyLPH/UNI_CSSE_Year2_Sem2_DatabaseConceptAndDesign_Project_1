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

    $allStaff = getAllStaff($conn);

    foreach($allStaff as $eachStaff){
        $listStaffID[] = $eachStaff["UserID"];
        $listStaffSalary[] = $eachStaff["Salary"];
        $totalUpdates[] = count(getAllTreeUpdate($conn,0,0,0,0,0,$eachStaff["UserID"]));

    }

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
                        <img src="/img/defaults/adminUserIcon.png" id="icon" alt="User Icon" /><br><br>
                    </div>
                    <button class="fullW" onclick="document.location='/Admin/reportCompany.php'">View Company Report</button><br><br>
                    <button class="fullW" onclick="document.location='/Admin/reportClient.php'">View Client Report</button><br><br>
                    <button class="fullW" onclick="document.location='/Admin/reportStaff.php'">View Staff Report</button><br><br>
                    <button class="fullW" onclick="document.location='/Admin/reportSales.php'">View Sales Report</button><br><br>
                </div>
                
                <div class="wrapper w3-container w3-threequarter w3-theme-d4" style="margin-left:25%;">
                    <h2>Summary of Affiliated Staffs</h2>
                </div>

                <!--<div class="w3-container w3-threequarter wrapper bgImgTree w3-animate-left" style="margin-left:25%;">
                    <div class='data-value card fadeIn'>
                        <div class='data-group'>
                            <label>This is a card tag</label>
                        </div>
                    </div>    
                </div>-->

                <div class="w3-container w3-threequarter wrapper w3-animate-left w3-theme-l5" style="margin-left:25%;">
                    <h3><b>Staff Salary Summary</b></h3>
                    <canvas id="chart0" style="height: 350px;width:100%;"></canvas>
                </div>

                    <div class="wrapper w3-container w3-threequarter w3-theme-d4" style="margin-left:25%;height:20px"></div>

                <div class="w3-container w3-threequarter wrapper w3-animate-left w3-theme-l5" style="margin-left:25%;">
                    <h3><b>Total Tree Updates Created by Staff</b></h3>
                    <canvas id="chart1" style="height: 350px;width:100%;"></canvas>
                </div>
            </div>
        </main>

        <footer>
            
        </footer>

        <script>
            staffID = <?php echo json_encode($listStaffID); ?>;            

            //graph for staff and their salaries
            staffSalary = <?php echo json_encode($listStaffSalary); ?>;

            const data0 = {
                labels: staffID,
                datasets: [{
                    label: 'Salary (RM)',
                    backgroundColor: 'rgb(0, 131, 115, 0.7)',
                    borderColor: 'rgb(0, 82, 72)',
                    data: staffSalary,
                }]
            };

            const config0 = {
                type: 'bar',
                data: data0,
                options: {
                    indexAxis: 'y',
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Salary (RM)'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Staff ID'
                            },
                        }
                    }
                }
            };

            const chart0 = new Chart(
                document.getElementById('chart0'),
                config0
            );

            staffID = <?php echo json_encode($listStaffID); ?>;            

            //graph for staff and their tree updates
            treeUpdates = <?php echo json_encode($totalUpdates); ?>;

            const data1 = {
                labels: staffID,
                datasets: [{
                    label: 'Number of Tree Updates Made',
                    backgroundColor: 'rgb(0, 131, 115, 0.7)',
                    borderColor: 'rgb(0, 82, 72)',
                    data: treeUpdates,
                }]
            };

            const config1 = {
                type: 'bar',
                data: data1,
                options: {
                    indexAxis: 'y',
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Number of Tree Updates Made'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Staff ID'
                            },
                        }
                    }
                }
            };

            const chart1 = new Chart(
                document.getElementById('chart1'),
                config1
            );
        </script>
    </body>
</html>


