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
    $allSales = getAllOnSale($conn);

    foreach ($allCompany as $eachCompany){
        $listCompanyID[] = $eachCompany["UserID"];
        $salebyCompanyID[] = getAllOnSale($conn,$eachCompany["UserID"]);
        
    }

    $availableCompany = $availableBlockWorth = $allMonth = NULL;
    // Function in dataManagement file.
    getMonthlyBlockWorth($conn, $availableCompany, $availableBlockWorth, $allMonth);
    // var_dump($allMonth);

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
                <h1>&#127984; Admin: Home Page &#127984;</h1>
            </div>
        </header>

        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Admin/navigationBar.php"); ?>

        <main>
            <div class="w3-row">
                <div class="w3-container w3-quarter w3-sidebar w3-bar-block w3-theme-d5" style="width:25%">
                    <div class="w3-center">
                        <h2 class="w3-center w3-bar-item" >Welcome, <?php
                            echo($_SESSION["Username"]);
                        ?> ! &#129498;</h2>
                        <img src="/img/defaults/adminUserIcon.png" id="icon" alt="User Icon" /><br><br>
                    </div>
                    <button class="fullW" onclick="document.location='/Admin/reportCompany.php'">View Company Report</button><br><br>
                    <button class="fullW" onclick="document.location='/Admin/reportClient.php'">View Client Report</button><br><br>
                    <button class="fullW" onclick="document.location='/Admin/reportStaff.php'">View Staff Report</button><br><br>
                    <button class="fullW" onclick="document.location='/Admin/reportSales.php'">View Sales Report</button><br><br>
                </div>
                
                <div class="wrapper w3-container w3-threequarter w3-theme-d4" style="margin-left:25%;">
                    <h2>Summary of Recorded Sales &#128176;</h2>
                </div>

                <!--<div class="w3-container w3-threequarter wrapper bgImgTree w3-animate-left" style="margin-left:25%;">
                    <div class='data-value card fadeIn'>
                        <div class='data-group'>
                            <label>This is a card tag</label>
                        </div>
                    </div>
                </div>-->

                <div class="w3-container w3-threequarter wrapper w3-animate-left w3-theme-l5" style="margin-left:25%;">
                    <?php 

                    ?>
                </div>

                <div class="w3-container w3-threequarter wrapper w3-animate-left w3-theme-l5" style="margin-left:25%;">
                    <h3><b>Monthly Block Worth (RM) </b></h3>
                    <canvas id="chart" style="height:450px;width:100%;"></canvas>
                </div>
            </div>
        </main>

        <footer>
            
        </footer>

        <script>
            var counter = 0;
            const preset = [
                'rgb(216,51,74)',
                'rgb(252,110,81)',
                'rgb(255,206,84)',
                'rgb(160,212,10)',
                'rgb(72,207,173)',
                'rgb(79,193,233)',
                'rgb(93,156,236)',
                'rgb(128,103,18)',
                'rgb(172,146,23)',
                'rgb(236,135,19)'
            ];
            
            var tempColor;

            // 0123456789ABCDEF, 16 possible char.
            function getRandomColor() {
                var letters = '0123456789ABCDEF'.split('');
                var color = '#';
                for (var i = 0; i < 6; i++ ) {
                    color += letters[Math.floor(Math.random() * 16)];
                }
                tempColor = color;
                return color;
            }

            function getPresetColor() {
                var color;
                if(counter!=0 && counter<=10 ){
                    color = preset[counter];
                    counter++;
                }else{
                    counter=0;
                    color = preset[counter];
                    counter++;
                }
                tempColor = color;
                return color;
            }

            //graph for company block worth
            const data = {
                labels: <?php echo(json_encode($allMonth)); ?>,
                datasets: [
                    <?php foreach($availableCompany as $key => $value): ?>
                        {
                            label: '<?php echo($value); ?>',
                            backgroundColor: getPresetColor(),
                            borderColor: `${tempColor}`,
                            data: <?php echo(json_encode($availableBlockWorth[$key])); ?>
                        },
                    <?php endforeach; ?>
                ]
            };

            const config = {
                type: 'line',
                data: data,
                options: {
                    responsive: true,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                        },
                        stacked: false,
                        plugins: {
                        title: {
                            display: true,
                            /*text: 'Chart.js Line Chart - Multi Axis'*/
                        }
                        },
                        scales: {
                        y: [
                            {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                text: 'Average Monthly Block Price (RM)'
                            }
                        ],
                        x: {
                                title: {
                                    display: true,
                                    /*text: 'Company ID'*/
                                }
                        }
                    },
                }
            };

            const chart = new Chart(
                document.getElementById('chart'),
                config
            );

        </script>
    </body>
</html>


