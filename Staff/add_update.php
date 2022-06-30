<?php
    // Staff Home Page.
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dbConnection.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/loginAuthenticate.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/inputValidation.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dataManagement.php");

    $tempLoginCheck = checkLogin($conn);

    // Not logged in as Staff.
    if ($tempLoginCheck != 2) {
        header("Location: /index.php");
        exit;
    }

    // Tree is not available for updating.
    if (
        !isset($_GET['item']) ||
        !is_numeric($_GET['item']) ||
        $_GET['item'] < 1 ||
        count(getAllTree($conn, getAllStaff($conn, 0, $_SESSION['UserID'])[0]["CompanyID"], 0, 0, $_GET["item"])) < 1
    ) {
        header("Location: /Staff/update.php");
        exit;
    }

    $treeID = $_GET['item'];

    $tempTreeID = $tempStaffID = $tempTreeImage = $tempStatus = $tempUpdateDate = "";
    $tempTreeHeight = $tempTreeDiameter = 0;
    $updateMessage = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $tempTreeID = (isset($_POST["treeid"])) ? cleanInput($_POST["treeid"]): "";
        $tempStaffID = (isset($_POST["staffid"])) ? cleanInput($_POST["staffid"]): "";
        $tempTreeHeight = (isset($_POST["treeheight"])) ? cleanInput($_POST["treeheight"]): "";
        $tempTreeDiameter = (isset($_POST["treediameter"])) ? cleanInput($_POST["treediameter"]): "";
        $tempStatus = (isset($_POST["treestatus"])) ? cleanInput($_POST["treestatus"]): "";
        $tempUpdateDate = (isset($_POST["updatedate"])) ? cleanInput($_POST["updatedate"]): "";

        if (
            empty($tempTreeID) ||
            empty($tempStaffID) ||
            empty($tempTreeHeight) ||
            empty($tempTreeDiameter) ||
            empty($tempStatus) ||
            empty($tempUpdateDate)
        ) {
            $updateMessage = "* Fill in ALL Fields! *";
        }
        else {
            // Target path to store image.
            $filepath = $targetImagePath = "/img/tree/";

            // Process image path.
            $tempTreeImage = explode(".", $_FILES["treeimage"]["name"]);
            $newfilename = "treeID$tempTreeID" . "_" . $tempUpdateDate . "_" . round(microtime(true));
            $newfilename .= "." . end($tempTreeImage);

            $filepath .= $newfilename;
            $filePathEscaped = $conn->real_escape_string($filepath);

            // Insert to TreeUpdate table.
            $query = "INSERT INTO `TreeUpdate`(`TreeID`, `StaffID`, `TreeImage`, `TreeHeight`, `Diameter`, `Status`, `UpdateDate`)";
            $query .= " VALUES ('$tempTreeID','$tempStaffID','$filePathEscaped','$tempTreeHeight','$tempTreeDiameter', '$tempStatus','$tempUpdateDate')";

            $rs = $conn->query($query);
            if (!$rs) {
                $updateMessage = "* Fail to insert to TreeUpdate table! *";
            }
            else {
                // Try to create folder if not exist, remember to add root path.
                if (!is_dir($_SERVER['DOCUMENT_ROOT'] . $targetImagePath)) {
                    mkdir($_SERVER['DOCUMENT_ROOT'] . $targetImagePath, 0777, true);
                }

                // Reset to empty.
                move_uploaded_file(
                    $_FILES["treeimage"]["tmp_name"],
                    $_SERVER['DOCUMENT_ROOT'] . cleanInput($filepath)
                );
                $tempTreeID = $tempStaffID = $tempTreeHeight = $tempTreeDiameter = $tempTreeImage = $tempStatus = $tempUpdateDate = "";
                $updateMessage = "* Tree is successfully Updated! *";
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff: Tree Update</title>

    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="/css/formFont.css">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://www.w3schools.com/lib/w3-colors-vivid.css">
    <!--<link rel="shortcut icon" href="/favicon.ico">-->
    <link rel="shortcut icon" href="https://icon-library.com/images/tree-icon/tree-icon-23.jpg">
</head>
<body>
    <header>
        <div class="maintheme w3-container">
            <h4 style="font-size: 36px">Staff: Tree Update</h4>
        </div>  
    </header>

    <?php include($_SERVER['DOCUMENT_ROOT'] . "/staff/navigationBar.php"); ?>

    <main>
        <div class="wrapper fadeInDown">
            <div id="formHeader">
                <h1>Update Tree</h1>
            </div>
            
            <div id="formContentW2">
                <div class="fadeIn first"><br>
                    <!--placeholder image-->
                    <img src="/img/defaults/treeIcon.jpg" id="icon" alt="tree" />
                </div>
                <span><?php
                        echo($updateMessage);
                    ?></span>
                <!-- Tree Update Form -->
                <form action="add_update.php?item=<?php echo($treeID); ?>" method="POST" enctype="multipart/form-data">
                    <br>    
                    <label for="treeid"><h3>Tree ID: </h3></label>
                    <input class="fullW95" type="text" id="treeid" name="treeid" value="<?php echo($treeID); ?>" readonly><br>

                    <label for="staffid"><h3>Staff ID: </h3></label>
                    <input class="fullW95" type="text" id="treeid" name="staffid" value="<?php echo($_SESSION['UserID']); ?>" readonly><br>

                    <label for="treeheight"><h3>Height: </h3></label>
                    <input class="fullW95" type="number" id="treeheight" name="treeheight" placeholder="Height" step="0.01" min="0.5"><br>
                    
                    <label for="treediameter"><h3>Diameter: </h3></label>
                    <input class="fullW95" type="number" id="treediameter" name="treediameter" placeholder="Diameter" step="0.01" min="0.05"><br>

                    <label for="treestatus"><h3>Status: </h3></label>
                    <div class="wrapper">
                        <div class="card w3-vivid-yellow-green">
                            <img src="https://cdn-addjh.nitrocdn.com/BzukxzxIDWSkBjOuXIuFVkjjEriFmqlw/assets/static/optimized/rev-f2a9b3c/wp-content/uploads/2020/02/Leaves-768x510.jpg" id="iconHalf" alt="tree" /><br> 
                            <label for="green_status">
                                Green
                            </label><br>
                            <input class="w3-radio" type="radio" id="green_status" name="treestatus" value="G" checked>
                        </div>
                        <div class="card w3-vivid-orange-yellow">
                            <img src="https://previews.123rf.com/images/sotnichenko/sotnichenko1910/sotnichenko191000002/131476609-veins-in-the-yellow-autumn-leaf-close-up-nature-texture-abstract-background.jpg" id="iconHalf" alt="tree" /><br>
                            <label for="yellow_status">
                                Yellow
                            </label><br>
                            <input class="w3-radio" type="radio" id="yellow_status" name="treestatus" value="Y">
                        </div>
                        <div class="card w3-vivid-reddish-orange">
                            <img src="https://thumbs.dreamstime.com/b/close-up-red-leaf-texture-159803699.jpg" id="iconHalf" alt="tree" /><br>           
                            <label for="red_status">
                                Red
                            </label><br>
                            <input class="w3-radio" type="radio" id="red_status" name="treestatus" value="R">
                        </div>
                    </div>

                    <label for="treeimage"><h3>Image: </h3></label>
                    <input class="fullW95" type="file" id="treeimage" name="treeimage" accept="image/png, image/jpg, image/jpeg" required><br>

                    <label for="updatedate"><h3>Update Date: </h3></label>
                    <input class="fullW95" type="date" id="updatedate" name="updatedate" value="<?php echo date('Y-m-d'); ?>" readonly><br>

                    <input type="submit" id="submit" name="submit" value="Submit">
                </form>
            </div>
        </div>
    </main>
</body>
</html>