<?php
    // Admin Manage Orchard Page.
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dbConnection.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/loginAuthenticate.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/inputValidation.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dataManagement.php");

    $tempLoginCheck = checkLogin($conn);
    // Not logged in as Admin.
    if ($tempLoginCheck != 4) {
        header("Location: /index.php");
        exit;
    }

    $queryString = array();

    if (isset($_SERVER['QUERY_STRING'])) {
        parse_str($_SERVER['QUERY_STRING'], $queryString);
    }

    $allOrchard = NULL;
    // Orchard is not available for deleting.
    if (
        !isset($queryString["OrchardID"]) ||
        !is_numeric($queryString["OrchardID"]) ||
        $queryString["OrchardID"] < 1 ||
        count($allOrchard = getAllOrchard($conn, 0, $queryString["OrchardID"])) < 1
    ) {
        header("Location: /Admin/manageOrchard.php");
        exit;
    }

    $orchardID = $queryString["OrchardID"];
    $result = $allOrchard[0];

    $tempPass = "";
    $deleteMsg = "";
    $passDeleting = false;

    // Delete attempt.
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $tempPass = (isset($_POST["Password"])) ? cleanInput($_POST["Password"]): "";

        if (empty($tempPass)) {
            $deleteMsg = "* Fill in Password to Delete! *";
            $passDeleting = false;
        }
        else {
            // Set to true at first.
            $passDeleting = true;

            // Check Password with PasswordHash in session.
            if (!password_verify($tempPass, $_SESSION["PasswordHash"])) {
                $deleteMsg = "* Invalid Password! *";
                $passDeleting = false;
            }

            // Delete from DB.
            if ($passDeleting) {
                if (!deleteOrchard($conn, $orchardID)) {
                    $deleteMsg = "* Fail to delete Orchard from database! *";
                    $passDeleting = false;
                }
                else {
                    $deleteMsg = "* Orchard is successfully deleted! *";
                }
            }
        }
    }

    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Admin: Manage Orchard Page</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">

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
                <h4 style="font-size: 36px">Admin: Manage Orchard Page</h4>
            </div>
        </header>

        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Admin/navigationBar.php"); ?>

        <main>
            <div class="wrapper fadeInDown">
                <div id="formHeader">
                    <h1>Delete Orchard ID <?php
                        echo($orchardID);
                    ?>:</h1>
                </div>
                <div id="formContentW2">
                    <img class="fadeIn first" src="/img/defaults/orchardIcon.png" id="icon" alt="Orchard Icon" />

                    <form method="post" action="/Admin/deleteOrchard.php?OrchardID=<?php
                        echo($orchardID);
                    ?>">
                        <table>
                            <tr>
                                <td colspan="2">
                                    <span class="<?php
                                        echo(($passDeleting) ? "success": "error");
                                    ?>-message"><?php
                                        echo($deleteMsg);
                                    ?></span>
                                </td>
                            </tr>

                            <?php if(!$passDeleting): ?>
                                <tr class="fadeIn second">
                                    <!-- Admin Password -->
                                    <td colspan="2">
                                        <div>
                                            <label for="Password">
                                                Admin Password:
                                            </label><br>
                                            <input id="Password" type="password" name="Password" placeholder="Enter Password to Delete" required>
                                        </div>
                                    </td>
                                </tr>

                                <tr class="fadeIn third">
                                    <td colspan="2">
                                        <div>
                                            <br>
                                            <input type="submit" value="Confirm Delete">
                                        </div>
                                    </td>
                                </tr>

                                <tr class="fadeIn forth">
                                    <td colspan="2">
                                        <span class="error-message">
                                            * WARNING: This action is Not Reversible! Data in related tables will also be deleted! *
                                        </span>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </table>
                    </form>
                    <br>
                    <div id="formFooter">
                        <?php if(!$passDeleting): ?>
                            <h2><a class="underlineHover" href="/Admin/viewEachOrchard.php?OrchardID=<?php
                                echo($orchardID);
                            ?>">Back to View Orchard</a><h2><br>
                        <?php else: ?>
                            <h2><a class="underlineHover" href="/Admin/manageOrchard.php">Back to Manage Orchard</a><h2><br>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
