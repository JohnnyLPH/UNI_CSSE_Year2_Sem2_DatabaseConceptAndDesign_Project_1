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

    function getResult($conn, $treeid) {
        $sql = "SELECT TreeID, SpeciesName, Latitude, Longitude, PlantDate, BlockID FROM Tree WHERE TreeID='$treeid'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result);

        if($row) {
            $location = $row["Latitude"] . ", " . $row["Longitude"];

            echo(
                "<tr>
                    <td> " . $row["TreeID"] . "</td> 
                    <td> " . $row["SpeciesName"] . "</td>
                    <td> " . $location . "</td>
                    <td> " . $row["PlantDate"] . "</td>
                    <td> " . $row["BlockID"] . "</td>
                    <td><a href=\"edit_tree.php?treeid=\" " . $row["TreeID"] . ">Update</a></td>
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
        }
    }

    function getResults($conn) {
        $sql = "SELECT TreeID, SpeciesName, Latitude, Longitude, PlantDate, BlockID FROM Tree";
        $result = $conn->query($sql);

        if($result->num_rows > 0) {
            $i=0;
            while($row = $result->fetch_assoc()) {
                $location = $row["Latitude"] . ", " . $row["Longitude"];

                echo(
                    "<tr>
                        <td> " . $row["TreeID"] . "</td> 
                        <td> " . $row["SpeciesName"] . "</td>
                        <td> " . $location . "</td>
                        <td> " . $row["PlantDate"] . "</td>
                        <td> " . $row["BlockID"] . "</td>
                        <td><a href=\"edit_tree.php?treeid=\" " . $row["TreeID"] . ">Update</a></td>
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
    <title>Staff: Update Page</title>
</head>
<body>
    <header>
        <h1>Staff: Update Page</h1>
    </header>

    <main>
        <!-- Search Bar -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="GET">
            <input type="text" id="search" name="search" placeholder="Search By Tree ID">
            <input type="submit" value="Submit">
        </form><br>

        <!-- Table -->
        <table>
            <thead>
                <tr>
                    <td>Tree ID</td>
                    <td>Species Name</td>
                    <td>Location</td>
                    <td>Plant Date</td>
                    <td>Block ID</td>
                </tr>
            </thead>

            <?php
                if(isset($_GET["search"]) && !empty($_GET["search"]))
                    getResult($conn, $_GET["search"]);
                
                else
                    getResults($conn);

                $conn->close();
            ?>

        </table>
    </main>
</body>
</html>