<?php
    // Login Page for Company, Staff, and Client.
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dbConnection.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/loginAuthenticate.php");

    $tempLoginCheck = checkLogin($conn);
    // Already logged in.
    if ($tempLoginCheck == 1) {
        header("Location: /Company/index.php");
        exit;
    }
    else if ($tempLoginCheck == 2) {
        header("Location: /Staff/index.php");
        exit;
    }
    else if ($tempLoginCheck == 3) {
        header("Location: /Client/index.php");
        exit;
    }
    else if ($tempLoginCheck == 4) {
        header("Location: /Admin/index.php");
        exit;
    }

    $queryString = array();

    if (isset($_SERVER['QUERY_STRING'])) {
        parse_str($_SERVER['QUERY_STRING'], $queryString);
    }

    // No User Type is chosen.
    if (
        !isset($queryString["UserType"]) ||
        (
            $queryString["UserType"] != "CO" &&
            $queryString["UserType"] != "ST" &&
            $queryString["UserType"] != "CL" &&
            $queryString["UserType"] != "AD"
        )
    ) {
        header("Location: /index.php");
        exit;
    }

    $loginMsg = "";

    // Login attempt, validate credentials.
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (
            !isset($_POST["Username"]) || empty($_POST["Username"]) ||
            !isset($_POST["Password"]) || empty($_POST["Password"])
        ) {
            $loginMsg = "* Fill in Username & Password! *";
        }
        else {
            $tempName = $_POST["Username"];
            $tempNameEscaped = $conn->real_escape_string($tempName);
            $tempPass = $_POST["Password"];
            // Check login credentials.
            $query = "SELECT UserID, Username, PasswordHash, UserType FROM User WHERE Username='$tempNameEscaped';";

            $rs = $conn->query($query);
            if ($rs) {
                if ($user = mysqli_fetch_assoc($rs)) {
                    // Username, Password, and UserType match.
                    if (
                        $user["Username"] == $tempName &&
                        password_verify($tempPass, $user["PasswordHash"]) &&
                        $user["UserType"] == $queryString["UserType"]
                    ) {
                        // Store login time in session.
                        $currentDate = date("Y-m-d H:i:s");
                        $_SESSION["lastActive"] = strtotime($currentDate);

                        // Store UserID, Username, PasswordHash, and UserType in session.
                        $_SESSION["UserID"] = $user["UserID"];
                        $_SESSION["Username"] = $user["Username"];
                        $_SESSION["PasswordHash"] = $user["PasswordHash"];
                        $_SESSION["UserType"] = $user["UserType"];

                        // Redirect to respective index page based on UserType.
                        if ($queryString["UserType"] == "CO") {
                            header("Location: /Company/index.php");
                        }
                        else if ($queryString["UserType"] == "ST") {
                            header("Location: /Staff/index.php");
                        }
                        else if ($queryString["UserType"] == "CL") {
                            header("Location: /Client/index.php");
                        }
                        else if ($queryString["UserType"] == "AD") {
                            header("Location: /Admin/index.php");
                        }
                        exit;
                    }
                }
            }

            $loginMsg = "* Invalid Login Credentials! *";
        }
    }

    $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php
            if (isset($queryString["UserType"])) {
                if ($queryString["UserType"] == "CO") {
                    echo("Company: ");
                }
                else if ($queryString["UserType"] == "ST") {
                    echo("Staff: ");
                }
                else if ($queryString["UserType"] == "CL") {
                    echo("Client: ");
                }
                else if ($queryString["UserType"] == "AD") {
                    echo("Admin: ");
                }
            }
        ?>Login Page</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">
        
        <link rel="stylesheet" href="/css/form.css">
        <!--<link rel="shortcut icon" href="/favicon.ico">-->
        <link rel="shortcut icon" href="https://icon-library.com/images/tree-icon/tree-icon-23.jpg">
    </head>

    <body>
        <header>

        </header>

        <main>
        <div class="wrapper fadeInDown">
            <div id="formContent">

                <form method="post" action="/login.php?UserType=<?php
                    echo($queryString["UserType"]);
                ?>">

                    <h1><?php
                        if (isset($queryString["UserType"])) {
                            if ($queryString["UserType"] == "CO") {
                                echo("Company ");
                            }
                            else if ($queryString["UserType"] == "ST") {
                                echo("Staff ");
                            }
                            else if ($queryString["UserType"] == "CL") {
                                echo("Client ");
                            }
                            else if ($queryString["UserType"] == "AD") {
                                echo("Admin ");
                            }
                        }
                    ?>Login</h1>

                    <div class="fadeIn first">
                        <img src="/img/defaults/TealUserIcon.png" id="icon" alt="User Icon" />
                    </div>
                    <br>
                    <div>
                        <label for="Username">
                            Username:
                        </label><br>
                        <input id="Username" type="text" name="Username" placeholder="Username"  class="fadeIn second" required>
                    </div>
                    <br>
                    <div>
                        <label for="Password">
                            Password:
                        </label><br>
                        <input id="Password" type="password" name="Password" placeholder="Password" class="fadeIn third" required>
                    </div>
                    
                    <span class="fadeIn first error-message"><?php
                        echo($loginMsg);
                    ?></span>

                    <br>
                    <br>
                    <div>
                        <input type="submit" class="fadeIn fourth" value="Login"></input>
                    </div>
                </form>

                <br>

                <div id="formFooter">
                    <h2><a class="underlineHover" href="/index.php">Switch UserType</a></h2>
                </div>
                
            </div>
        </div>
        </main>

        <footer>
            
        </footer>
    </body>
</html>
