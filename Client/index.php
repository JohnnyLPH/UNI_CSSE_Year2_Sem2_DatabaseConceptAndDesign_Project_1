<?php
    // Client Home Page.
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dbConnection.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/loginAuthenticate.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dataManagement.php");

    $tempLoginCheck = checkLogin($conn);
    // Not logged in as Client.
    if ($tempLoginCheck != 3) {
        header("Location: /index.php");
        exit;
    }

    // Fetch image from database
    $ID = $_SESSION["UserID"];
    $img = mysqli_query($conn, "SELECT * FROM Client WHERE UserID = $ID");
    $result = mysqli_fetch_array($img);
    $imgPath = $result["Photo"];

    $allBlockOwned = getBlockLatestClient($conn, 0, 0, 0, $_SESSION["UserID"]);
    
    // Count owned block.
    $ownedBlockCount = count($allBlockOwned);

    // Count tree in owned block.
    $ownedTreeCount = 0;
    foreach ($allBlockOwned as $eachBlock) {
        $ownedTreeCount += getTreeCount($conn, 0, 0, $eachBlock["BlockID"]);
    }

    // Count available block (OnSale).
    $availableBlockCount = 0;
    $tempBlockOnSale = getBlockLatestClient($conn);
    foreach ($tempBlockOnSale as $eachBlock) {
        if (
            empty($eachBlock["SaleID"]) ||
            (
                $eachBlock["ClientID"] > 0 &&
                $eachBlock["ApprovalStatus"] == 1
            )
        ) {
            continue;
        }
        $availableBlockCount++;
    }

    function getTotalSale($conn, $sellerID) {
        $query = "SELECT SUM(RequestPrice) sum FROM PurchaseRequest INNER JOIN OnSale USING(SaleID) WHERE OnSale.SellerID = '$sellerID' AND PurchaseRequest.ApprovalStatus = 1;";

        $allRow = array();
        $rs = $conn->query($query);
        if ($rs) {
            while ($resultRow = mysqli_fetch_assoc($rs)) {
                array_push($allRow, $resultRow);
            }
        }

        $sale = $allRow[0]["sum"];

        return empty($sale) ? 0 : $sale;
    }

    $totalSale = getTotalSale($conn, $_SESSION["UserID"]);

    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Client: Home Page</title>
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
                <h1>Client: Home Page</h1>
            </div>   
        </header>

        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Client/navigationBar.php"); ?>

        <main>
            <div class="w3-row">
                    <div class="w3-container w3-center w3-quarter w3-sidebar w3-bar-block w3-theme-d5" style="width:25%">
                            <h2 class="w3-bar-item" >Welcome, <?php
                                echo($_SESSION["Username"]);
                            ?></h2>
                        
                            <img  src="<?php echo($imgPath); ?>"  id="icon" 
                            alt="* UserID <?php echo($_SESSION["UserID"]); ?> img *">
                    </div>

                    <div class="wrapper w3-container w3-threequarter w3-theme-d4" style="margin-left:25%;">
                        <h2>What You Need to Know</h2>
                    </div>

                    <div class="w3-container w3-threequarter wrapper bgImgTree w3-animate-left" style="margin-left:25%;">
                        <div class="card fadeIn first">
                            <img src="/img/defaults/blockIcon.jpg" id="icon" alt="Info" />
                            <br>
                            <span class='overall-data'><?php
                                echo($ownedBlockCount);
                            ?></span>
                            <span class='data-title'> Blocks Owned</span>
                        </div>
                        <div class="card fadeIn second">
                            <img src="/img/defaults/blockIcon.jpg" id="icon" alt="Info" />
                            <img src="https://jhinsite.com/wp-content/uploads/2018/02/available_now_banner_homeslider.png" id="oriImg" alt="Info" 
                                style="position:absolute; top:0%; left:0%; width:70%; "/>
                            <br>
                            <span class='overall-data'><?php
                                echo($availableBlockCount);
                            ?></span>
                            <span class='data-title'> Blocks Available</span>
                        </div>
                        <div class="card fadeIn third">
                            <img src="https://static.vecteezy.com/system/resources/thumbnails/003/089/451/small/forest-scenery-background-free-vector.jpg" id="icon" alt="Info" />
                            <br>
                            <span class='overall-data'><?php
                                echo($ownedTreeCount);
                            ?></span>
                            <span class='data-title'>Trees in your Block</span>
                        </div>
                        <div class="card fadeIn fourth">
                            <img src="https://media.indiedb.com/images/members/4/3384/3383828/tree.1.jpg" id="icon" alt="Info" />
                            <br>
                            <span class='data-title'>Total Successful Sale</span>
                            <span class='overall-data'><?php
                                echo($totalSale);
                            ?></span>
                        </div>
                    </div>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
