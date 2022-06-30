<?php
    // Admin Manage Admin Page.
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

    $allAdmin = NULL;
    // Admin is not available for deleting.
    if (
        !isset($queryString["AdminID"]) ||
        !is_numeric($queryString["AdminID"]) ||
        $queryString["AdminID"] < 1 ||
        count($allAdmin = getAllAdmin($conn, $queryString["AdminID"])) < 1
    ) {
        header("Location: /Admin/manageAdmin.php");
        exit;
    }

    $adminID = $queryString["AdminID"];
    $result = $allAdmin[0];

    $tempPass = $tempAdminPass = "";
    $deleteMsg = "";
    $passDeleting = false;

    // Delete attempt.
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $tempPass = (isset($_POST["Password"])) ? cleanInput($_POST["Password"]): "";
        $tempAdminPass = (isset($_POST["AdminPassword"])) ? cleanInput($_POST["AdminPassword"]): "";

        if (
            empty($tempPass) ||
            empty($tempAdminPass)
        ) {
            $deleteMsg = "* Fill in BOTH Passwords to Delete! *";
            $passDeleting = false;
        }
        else {
            // Set to true at first.
            $passDeleting = true;

            // Check Password with PasswordHash in session and check Admin Password (admin to be edited).
            if (
                !password_verify($tempPass, $_SESSION["PasswordHash"]) ||
                !password_verify($tempAdminPass, $result["PasswordHash"])
            ) {
                $deleteMsg = "* Invalid Password! *";
                $passDeleting = false;
            }

            // Delete from DB.
            if ($passDeleting) {
                if (!deleteAdmin($conn, $adminID)) {
                    $deleteMsg = "* Fail to delete Admin from database! *";
                    $passDeleting = false;
                }
                else {
                    $deleteMsg = "* Admin is successfully deleted! *";
                }
            }
        }
    }

    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Admin: Manage Admin Page</title>
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
                <h4 style="font-size: 36px">Admin: Manage Admin Page</h4>
            </div>
        </header>

        <?php include($_SERVER['DOCUMENT_ROOT'] . "/Admin/navigationBar.php"); ?>

        <main>
            <div class="wrapper fadeInDown">
                <div id="formHeader">
                    <h1>Delete Admin ID <?php
                        echo($adminID);
                    ?>:</h1>
                </div>
                <div id="formContentW2">
                    <img class="fadeIn first" src="/img/defaults/adminIcon.png" id="icon" alt="Admin Icon" />

                    <form method="post" action="/Admin/deleteAdmin.php?AdminID=<?php
                        echo($adminID);
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
                                                Your Admin Password:
                                            </label><br>
                                            <input id="Password" type="password" name="Password" placeholder="Enter Password to Delete" required>
                                        </div>
                                    </td>
                                </tr>

                                <tr class="fadeIn second">
                                    <!-- AdminPassword -->
                                    <td colspan="2">
                                        <div>
                                            <label for="AdminPassword">
                                                Current Password (for Admin ID <?php
                                                    echo($adminID);
                                                ?>):
                                            </label><br>
                                            <input id="AdminPassword" type="password" name="AdminPassword" placeholder="Enter Password to Delete" required>
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
                            <h2><a class="underlineHover" href="/Admin/viewEachAdmin.php?AdminID=<?php
                                echo($adminID);
                            ?>">Back to View Admin</a><h2><br>
                        <?php else: ?>
                            <h2><a class="underlineHover" href="/Admin/manageAdmin.php">Back to Manage Admin</a><h2><br>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
