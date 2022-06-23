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
    </main>
</body>
</html>