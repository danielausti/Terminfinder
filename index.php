<?php 
session_start(); 
?>
<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Terminfinder</title>
        <link rel="stylesheet" type="text/css" href="terminfinder_style.css">
        <link rel="icon" href="kalender.ico">
    </head>
    <body>
        <?php include("header.php"); ?>
        <main class="frontpicture maxwidth">
            <div id="frontbuttons">
                <a href="create.php"><button>Umfrage erstellen</button></a>
                <a href="join.php"><button>Umfrage beitreten</button></a>
            </div>
        </main>
    </body>
</html>

