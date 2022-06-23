<?php
    // Staff Home Page.
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dbConnection.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/loginAuthenticate.php");

    $tempLoginCheck = checkLogin($conn);

    // Not logged in as Staff.
    if ($tempLoginCheck != 2) {
        header("Location: /index.php");
        exit;
    }

    if(!(isset($_GET['treeid']))) {
        header("Location: update.php");
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
</head>
<body>
    <header>
        <h1>Staff: Tree Page</h1>
    </header>

    <main>
        <a href="index.php">Return to Home Page</a>
        <h2>Tree ID: <?php echo($treeID); ?></h2>
        <a href="add_update.php?item=<?php echo($treeID); ?>">Add Update</a>
        <!-- Table -->
        <table>
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
    </main>
</body>
</html>