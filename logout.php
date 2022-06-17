<?php
    if (session_id() == "") {
        session_start();
    }
    session_destroy();
    // Redirect back to System Home Page.
    header("Location: /index.php");
    exit;
?>
