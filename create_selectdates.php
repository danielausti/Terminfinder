<?php
    session_start();
    
    if(!isset($_SESSION["user_login"])){
        header('Location: ./Anmelden.html');
    }
    if(!(isset($_POST["pollname"]) && isset($_POST["polllength"]) && isset($_POST["pollend"]) || 
     isset($_SESSION["pollname"]) && isset($_SESSION["polllength"]) && isset($_SESSION["pollend"]) )){
        header('Location: ./create.php');
    }
    if(isset($_POST["pollname"]) && isset($_POST["polllength"]) && isset($_POST["pollend"])){
        $_SESSION["pollname"] = $_POST["pollname"];
        $_SESSION["polllength"] = intval($_POST["polllength"]);
        $_SESSION["pollend"] = $_POST["pollend"];
    }
    if(isset($_POST["polldescription"])){
         $_SESSION["polldescription"] = $_POST["polldescription"];
    }
?>

<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Tage auswählen</title>

        <link rel="stylesheet" type="text/css" href="terminfinder_style.css">
        <link rel="stylesheet" type="text/css" href="kalenderstyle.css">
        <link rel="icon" href="kalender.ico">
    </head>
    <body>
        <?php include("header.php"); ?>
        <main class="maxwidth">
            <div id="kalenderTitleTage">
                <p><b>Tage: </b>Wähle jetzt alle Tage aus, die für den Termin in Frage kommen.</p>
            </div>
            <form method="post" action="create_selectdefaulttimes.php" autocomplete="off">
                <div id="kalenderBoxTage">
                    <?php include("kalender_template.php"); ?>
                </div>
            <div id="fixedSubmitSpace"></div>
            <section id="fixedSubmit">
                <input type="submit" value="Weiter">
            </section>
            </form>
        </main>
        <script type="text/javascript" src="kalenderscript.js">
        </script>
    </body>
</html>

