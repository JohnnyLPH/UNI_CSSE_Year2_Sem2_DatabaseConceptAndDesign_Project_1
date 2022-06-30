<?php
    // Staff Home Page.
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dbConnection.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/loginAuthenticate.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dataManagement.php");


    $tempLoginCheck = checkLogin($conn);

    // Not logged in as Staff.
    if ($tempLoginCheck != 2) {
        header("Location: /index.php");
        exit;
    }

    // Tree is not available for updating.
    if (
        !isset($_GET['treeid']) ||
        !is_numeric($_GET['treeid']) ||
        $_GET['treeid'] < 1 ||
        count(getAllTree($conn, getAllStaff($conn, 0, $_SESSION['UserID'])[0]["CompanyID"], 0, 0, $_GET["treeid"])) < 1
    ) {
        header("Location: /Staff/update.php");
        exit;
    }

    $treeID = $_GET['treeid'];

    function treeStatus($status) {
        if($status == 'Y')
            return "Yellow";

        else if($status == 'R')
            return "Red";

        else if($status == 'G')
            return "Green";
    }

    function getTreeUpdates($conn, $treeID) {
        $sql = "
            SELECT 
                UpdateID, StaffID, TreeHeight, Diameter, Status, UpdateDate
            FROM 
                TreeUpdate
            INNER JOIN 
                Tree USING(TreeID)
            WHERE 
                TreeID = '$treeID';
        ";

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $i = 0;
            while ($row = $result->fetch_assoc()) {

                echo (
                    "<tr>
                            <td> " . $i+1 . " </td>
                            <td> " . $row["UpdateID"] . "</td> 
                            <td> " . $row["StaffID"] . "</td>
                            <td> " . $row["TreeHeight"] . "</td>
                            <td> " . $row["Diameter"] . "</td>
                            <td> " . treeStatus($row["Status"]) . "</td>
                            <td> " . $row["UpdateDate"] . "</td>
                        </tr>"
                );
                /*
                    echo(
                        "<tr>
                            <td> " . $row["TreeID"] . "</td> 
                            <td> " . $row["SpeciesName"] . "</td>
                            <td> " . $location . "</td>
                            <td> " . $row["PlantDate"] . "</td>
                            <td> " . $row["BlockID"] . "</td>
                            <td><a href=\"edit_tree.php?treeid=\" " . $row["TreeID"] . ">Update</a></td>
                            <td><a href=\"delete_tree.php?treeid=\" " . $row["TreeID"] . ">Delete</a></td>
                        </tr>"
                    );
                    */
                $i++;
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
    <title>Staff: Tree Page</title>

    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <!--<link rel="shortcut icon" href="/favicon.ico">-->
    <link rel="shortcut icon" href="https://icon-library.com/images/tree-icon/tree-icon-23.jpg">
</head>

<body>
    <header>
        <div class="maintheme w3-container">
            <h1>Staff: Tree Page</h1>
        </div>          
    </header>

    <?php include($_SERVER['DOCUMENT_ROOT'] . "/staff/navigationBar.php"); ?>

    <main>
        <div class="w3-row">
            <div class="w3-container w3-theme-d4 w3-animate-opacity">           
                <div class="wrapper w3-center" style="align-items:center">
                    <div  class="card" style="width:50%">                        
                        <h2 class="w3-center" style="color:black">Tree ID: <?php echo($treeID); ?></h2>
                        <div class="fadeIn first">
                            <!--placeholder image-->
                            <img src="/img/defaults/treeIcon.jpg" id="icon" alt="tree" />
                        </div>
                        <br>
                        <!-- Table -->
                        <table class=" w3-center w3-table-all w3-centered w3-hoverable" style="width:100%">
                            <thead>
                                <tr>
                                    <td>No.</td>
                                    <td>Update ID</td>
                                    <td>Staff ID</td>
                                    <td>Height</td>
                                    <td>Diameter</td>
                                    <td>Status</td>
                                    <td>Update Date</td>
                                </tr>
                            </thead>

                            <?php
                            // if (isset($_GET["search"]) && !empty($_GET["search"]))
                            //     getResult($conn, $_GET["search"]);

                            // else
                            getTreeUpdates($conn, $treeID);

                            $conn->close();
                            ?>
                        </table>
                        <br>
                        <div class="w3-center">
                            <button onclick="document.location='add_update.php?item=<?php echo($treeID); ?>'">Add Update</button>
                            <button onclick="document.location='/Staff/update.php'">Back</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>