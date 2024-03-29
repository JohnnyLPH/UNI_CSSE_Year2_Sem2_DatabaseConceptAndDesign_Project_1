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

// function getResult($conn, $treeid)
// {
//     $sql = "SELECT TreeID, SpeciesName, Latitude, Longitude, PlantDate, BlockID FROM Tree WHERE TreeID='$treeid'";
//     $result = mysqli_query($conn, $sql);
//     $row = mysqli_fetch_array($result);

//     if ($row) {
//         $location = $row["Latitude"] . ", " . $row["Longitude"];

//         echo ("<tr>
//                     <td> " . $row["TreeID"] . "</td> 
//                     <td> " . $row["SpeciesName"] . "</td>
//                     <td> " . $location . "</td>
//                     <td> " . $row["PlantDate"] . "</td>
//                     <td> " . $row["BlockID"] . "</td>
//                     <td><a href=\"edit_tree.php?treeid=\" " . $row["TreeID"] . ">Update</a></td>
//                 </tr>"
//         );

//         /*
//             echo(
//                 "<tr>
//                     <td> " . $row["TreeID"] . "</td> 
//                     <td> " . $row["SpeciesName"] . "</td>
//                     <td> " . $location . "</td>
//                     <td> " . $row["PlantDate"] . "</td>
//                     <td> " . $row["BlockID"] . "</td>
//                     <td><a href=\"edit_tree.php?treeid=\" " . $row["TreeID"] . ">Update</a></td>
//                     <td><a href=\"delete_tree.php?treeid=\" " . $row["TreeID"] . ">Delete</a></td>
//                 </tr>"
//             );
//             */
//     }
// }

function getResults($conn, $staffId)
{
    $sql = "
        SELECT 
            Tree.TreeID, Tree.SpeciesName, Tree.Latitude, Tree.Longitude, Tree.PlantDate, Tree.BlockID 
        FROM 
            Tree 
        INNER JOIN 
            BLOCK USING (BlockID) 
        INNER JOIN 
            Orchard USING (OrchardID) 
        INNER JOIN 
            Staff USING (CompanyID) 
        WHERE 
            UserID = '$staffId' 
        ORDER BY 
            BlockID, TreeID;
    ";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $i = 0;
        while ($row = $result->fetch_assoc()) {
            $location = $row["Latitude"] . ", " . $row["Longitude"];

            echo ("<tr>
                        <td> " . $i+1 . " </td>
                        <td> " . $row["TreeID"] . "</td> 
                        <td> " . $row["SpeciesName"] . "</td>
                        <td> " . $location . "</td>
                        <td> " . $row["PlantDate"] . "</td>
                        <td> " . $row["BlockID"] . "</td>
                        <td><button onclick='document.location=\"edit_tree.php?treeid=" . $row["TreeID"] . "\"'>Update</button></td>
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

    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <!--<link rel="shortcut icon" href="/favicon.ico">-->
    <link rel="shortcut icon" href="https://icon-library.com/images/tree-icon/tree-icon-23.jpg">
</head>

<body>
    <header>
        <div class="maintheme w3-container">
            <h1>Staff: Update Page</h1>
        </div>  
    </header>
    
    <?php include($_SERVER['DOCUMENT_ROOT'] . "/staff/navigationBar.php"); ?>

    <main>
        <!-- Search Bar -->
        <!-- <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="GET">
            <input type="text" id="search" name="search" placeholder="Search By Tree ID">
            <input type="submit" value="Submit">
        </form><br> -->

        <div class="w3-container w3-theme-d4 w3-animate-opacity">
            <!-- Table -->
            <table class=" w3-center w3-table-all w3-centered w3-hoverable" style="width:100%">
            <br>
                <thead>
                    <tr>
                        <td>No.</td>
                        <td>Tree ID</td>
                        <td>Species Name</td>
                        <td>Location</td>
                        <td>Plant Date</td>
                        <td>Block ID</td>
                        <td>Action</td>
                    </tr>
                </thead>

                <?php
                // if (isset($_GET["search"]) && !empty($_GET["search"]))
                //     getResult($conn, $_GET["search"]);

                // else
                getResults($conn, $_SESSION["UserID"]);

                $conn->close();
                ?>

            </table>
            <br>
        </div>
    </main>
</body>

</html>