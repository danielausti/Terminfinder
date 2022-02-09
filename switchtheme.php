<?php
    session_start();
    $themebefore = boolval($_GET["themebefore"]);
    $urlbefore = $_GET["urlbefore"];
    $theme = !$themebefore;
    $_SESSION["theme"] = intval($theme);
    header("Location: $urlbefore");
?>