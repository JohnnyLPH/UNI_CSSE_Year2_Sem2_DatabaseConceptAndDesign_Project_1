<?php
    // Staff Home Page.
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dbConnection.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/loginAuthenticate.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/inputValidation.php");

    $tempLoginCheck = checkLogin($conn);

    // Not logged in as Staff.
    if ($tempLoginCheck != 2) {
        header("Location: /index.php");
        exit;
    }

    if(!(isset($_GET['item']))) {
        header("Location: update.php");
        exit;
    }

    $treeID = $_GET['item'];

    $tempTreeID = $tempStaffID = $tempTreeImage = $tempStatus = $tempUpdateDate = "";
    $tempTreeHeight = $tempTreeDiameter = 0;
    $updateMessage = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (
            !isset($_POST["treeid"]) || empty($_POST["treeid"]) ||
            !isset($_POST["staffid"]) || empty($_POST["staffid"]) ||
            !isset($_POST["treeheight"]) || empty($_POST["treeheight"]) ||
            !isset($_POST["treediameter"]) || empty($_POST["treediameter"]) ||
            !isset($_POST["treestatus"]) || empty($_POST["treestatus"]) ||
            !isset($_POST["updatedate"]) || empty($_POST["updatedate"])
        ) {
            $updateMessage = "* Fill in ALL Fields! *";
        }
        else {
            $tempTreeID = cleanInput($_POST["treeid"]);
            $tempStaffID = cleanInput($_POST["staffid"]);
            $tempTreeHeight = cleanInput($_POST["treeheight"]);
            $tempTreeDiameter = cleanInput($_POST["treediameter"]);
            $tempStatus = cleanInput($_POST["treestatus"]);
            $tempUpdateDate = cleanInput($_POST["updatedate"]);

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
                // Process image path
                $tempTreeImage = $_FILES["treeimage"]['name'];
                $tempname = $_FILES['treeimage']['tmp_name'];
                $target_file = "..\\img\\" . $tempTreeImage;
                $filepath = "/img/" . basename($_FILES['treeimage']['name']);

                // Insert to User table with UserType CO.
                $query = "INSERT INTO `TreeUpdate`(`TreeID`, `StaffID`, `TreeImage`, `TreeHeight`, `Diameter`, `Status`, `UpdateDate`)";
                $query .= " VALUES ('$tempTreeID','$tempStaffID','$filepath','$tempTreeHeight','$tempTreeDiameter', '$tempStatus','$tempUpdateDate')";

                $rs = $conn->query($query);
                if (!$rs) {
                    $updateMessage = "* Something Wrong! *";
                } else {
                    // Reset to empty.
                    move_uploaded_file($tempname, $target_file);
                    $tempTreeID = $tempStaffID = $tempTreeHeight = $tempTreeDiameter = $tempTreeImage = $tempStatus = $tempUpdateDate = "";
                    $updateMessage = "* Successfully Updated! *";
                }
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
</head>
<body>
    <header>
        <h1>Staff: Tree Update</h1>
    </header>

    <main>
        <a href="index.php">Return to Home Page</a>
        <span><?php
                echo($updateMessage);
            ?></span>
        <!-- Tree Update Form -->
        <form action="add_update.php?item=<?php echo($treeID); ?>" method="POST" enctype="multipart/form-data">
            <label for="treeid">Tree ID: </label>
            <input type="text" id="treeid" name="treeid" value="<?php echo($treeID); ?>" readonly><br>

            <label for="staffid">Staff ID: </label>
            <input type="text" id="treeid" name="staffid" value="<?php echo($_SESSION['UserID']); ?>" readonly><br>

            <label for="treeheight">Height: </label>
            <input type="number" id="treeheight" name="treeheight" placeholder="Height" step="0.01"><br>
            
            <label for="treediameter">Diameter: </label>
            <input type="number" id="treediameter" name="treediameter" placeholder="Diameter" step="0.01"><br>

            <label for="treestatus">Status: </label>
            <input type="radio" id="green_status" name="treestatus" value="G">
            <label for="green_status">Green</label>
            <input type="radio" id="yellow_status" name="treestatus" value="Y">
            <label for="yellow_status">Yellow</label>
            <input type="radio" id="red_status" name="treestatus" value="R">
            <label for="red_status">Red</label><br>

            <label for="treeimage">Image: </label>
            <input type="file" id="treeimage" name="treeimage" accept="image/*"><br>

            <label for="updatedate">Update Date: </label>
            <input type="date" id="updatedate" name="updatedate" value="<?php echo date('Y-m-d'); ?>" readonly><br>

            <input type="submit" id="submit" name="submit" value="Submit">
        </form>
    </main>
</body>
</html>