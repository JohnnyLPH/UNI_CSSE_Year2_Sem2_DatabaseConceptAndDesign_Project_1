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

    $allCompany = getAllCompany($conn);

    foreach ($allCompany as $eachCompany){
        $listCompanyID[] = $eachCompany["UserID"];
        $treeCount[] = getTreeCount($conn, $eachCompany["UserID"]);
        $blockCount[] = getBlockCount($conn, $eachCompany["UserID"]);
        $orchardCount[] = getOrchardCount($conn, $eachCompany["UserID"]);
        $allCompanyStaff[] = getStaffCount($conn, $eachCompany["UserID"]);   
        $allCompanyClientPurchase[] = getPurchaseRequestCount($conn,1, $eachCompany["UserID"]);   
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
                    <h2>Summary of Affiliated Companies</h2>
                </div>
                <!--
                <div class="w3-container w3-threequarter wrapper bgImgTree w3-animate-left" style="margin-left:25%;">
                    <div class='data-value card fadeIn'>
                        <div class='data-group'>
                            <label>This is a card tag</label>
                        </div>
                    </div>
                </div>
                -->

                <div class="w3-container w3-threequarter wrapper w3-animate-left w3-theme-l5" style="margin-left:25%;">
                    <h3><b>Number of Trees By Company</b></h3>
                    <canvas id="chart0" style="height: 350px;width:100%;"></canvas>
                </div>

                    <div class="wrapper w3-container w3-threequarter w3-theme-d4" style="margin-left:25%;height:20px"></div>

                <div class="w3-container w3-threequarter wrapper w3-animate-left w3-theme-l5" style="margin-left:25%;">
                    <h3><b>Number of Blocks By Company</b></h3>
                    <canvas id="chart1" style="height: 350px;width:100%;"></canvas>
                </div>

                    <div class="wrapper w3-container w3-threequarter w3-theme-d4" style="margin-left:25%;height:20px"></div>

                <div class="w3-container w3-threequarter wrapper w3-animate-left w3-theme-l5" style="margin-left:25%;">
                    <h3><b>Number of Orchards By Company</b></h3>
                    <canvas id="chart5" style="height: 350px;width:100%;"></canvas>
                </div>

                    <div class="wrapper w3-container w3-threequarter w3-theme-d4" style="margin-left:25%;height:20px"></div>

                <div class="w3-container w3-threequarter wrapper w3-animate-left w3-theme-l5" style="margin-left:25%;">
                    <h3><b>Number of Staff By Company</b></h3>
                    <canvas id="chart2" style="height: 350px;width:100%;"></canvas>
                </div>

                    <div class="wrapper w3-container w3-threequarter w3-theme-d4" style="margin-left:25%;height:20px"></div>

                <div class="w3-container w3-threequarter wrapper w3-animate-left w3-theme-l5" style="margin-left:25%;">
                    <h3><b>Number of Client Block Purchases By Company</b></h3>
                    <canvas id="chart3" style="height: 350px;width:100%;"></canvas>
                </div>

                    <div class="wrapper w3-container w3-threequarter w3-theme-d4" style="margin-left:25%;height:20px"></div>


            </div>
        </main>

        <footer>
            
        </footer>

        <script>
            companyID = <?php echo json_encode($listCompanyID); ?>;            

            //graph for tree by company
            trees = <?php echo json_encode($treeCount); ?>;

            const data0 = {
                labels: companyID,
                datasets: [{
                    label: 'Trees',
                    backgroundColor: 'rgb(0, 131, 115, 0.7)',
                    borderColor: 'rgb(0, 82, 72)',
                    data: trees,
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
                                text: 'Number of Trees'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Company ID'
                            },
                        }
                    }
                }
            };

            const chart0 = new Chart(
                document.getElementById('chart0'),
                config0
            );

            //graph for block by company
            blocks = <?php echo json_encode($blockCount); ?>;

            const data1 = {
                labels: companyID,
                datasets: [{
                    label: 'Blocks',
                    backgroundColor: 'rgb(0, 131, 115, 0.7)',
                    borderColor: 'rgb(0, 82, 72)',
                    data: blocks,
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
                                text: 'Number of Blocks'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Company ID'
                            },
                        }
                    }
                }
            };

            const chart1 = new Chart(
                document.getElementById('chart1'),
                config1
            );

            //graph for staff by company
            allstaff = <?php echo json_encode($allCompanyStaff); ?>

            const data2 = {
                labels: companyID,
                datasets: [{
                    label: 'Staffs',
                    backgroundColor: 'rgb(0, 131, 115, 0.7)',
                    borderColor: 'rgb(0, 82, 72)',
                    data: allstaff,
                }]
            };

            const config2 = {
                type: 'bar',
                data: data2,
                options: {
                    indexAxis: 'y',
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Number of Staff'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Company ID'
                            },
                        }
                    }
                }
            };

            const chart2 = new Chart(
                document.getElementById('chart2'),
                config2
            );

            //graph for client by company
            allclientpurchase = <?php echo json_encode($allCompanyClientPurchase); ?>

            const data3 = {
                labels: companyID,
                datasets: [{
                    label: 'Client Purchases',
                    backgroundColor: 'rgb(0, 131, 115, 0.7)',
                    borderColor: 'rgb(0, 82, 72)',
                    data: allclientpurchase,
                }]
            };

            const config3 = {
                type: 'bar',
                data: data3,
                options: {
                    indexAxis: 'y',
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Number of Client Purchase'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Company ID'
                            },
                        }
                    }
                }
            };

            const chart3 = new Chart(
                document.getElementById('chart3'),
                config3
            );

            //graph for orchard by company
            orchards = <?php echo json_encode($orchardCount); ?>

            const data5 = {
                labels: companyID,
                datasets: [{
                    label: 'Orchards',
                    backgroundColor: 'rgb(0, 131, 115, 0.7)',
                    borderColor: 'rgb(0, 82, 72)',
                    data: orchards,
                }]
            };

            const config5 = {
                type: 'bar',
                data: data5,
                options: {
                    indexAxis: 'y',
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Number of Orchards'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Company ID'
                            },
                        }
                    }
                }
            };

            const chart5 = new Chart(
                document.getElementById('chart5'),
                config5
            );
        </script>
    </body>
</html>


