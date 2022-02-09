<?php
    session_start();
    
    if(!isset($_SESSION["user_login"])){
        header('Location: ./Anmelden.html');
    }
    if(!(isset($_POST["weekday"]) || isset($_POST["weekday"]) ||
     isset($_SESSION["weekday"]) || isset($_SESSION["weekend"]) )){
        header('Location: ./create_selectdefaulttimes.php');
        exit();
    }
    if(!(isset($_SESSION["pollname"]) && isset($_SESSION["polllength"]) && isset($_SESSION["pollend"]) &&
        isset($_SESSION["calendar"]) && isset($_SESSION["polldescription"]))){
        header('Location: ./create_selectdefaulttimes.php');
        exit();
    }

    include("zeiträumeUmwandeln.php");

    $_SESSION["weekday"] = isset($_POST["weekday"]) ? $_POST["weekday"] : "";
    $_SESSION["weekend"] = isset($_POST["weekend"]) ? $_POST["weekend"] : "";
    
    function create_tagesauswahl($day, $eventDates, $personDates){
        $createMode = true;
        include("tagesauswahl_template.php");
    }
    $polllength = $_SESSION["polllength"];

    $selectedDates = explode(",",$_SESSION["calendar"]);
    sort($selectedDates);
?>

<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Zeiten auswählen</title>

        <link rel="stylesheet" type="text/css" href="terminfinder_style.css">
        <link rel="stylesheet" type="text/css" href="tagesauswahlStyle.css">
        <link rel="icon" href="kalender.ico">
    </head>
    <body>
        <?php include("header.php"); ?>
        <main class="maxwidth">
            <h1>Zeiten auswählen</h1>
            <p>Währe jetzt für jeden Tag die passenden Zeiten aus. Zeiträume, die kürzer  als die 
                Länge des Termins von <b><?php echo $polllength; ?> <?php if($polllength == 1) echo "Stunde"; else echo "Stunden"; ?></b>
                 sind, werden ignoriert.
            </p>
            <form method="post" action="./createcomplete.php" autocomplete="off">

                <?php 
                foreach($selectedDates as $day){
                    if(in_array(intval(date_format(date_create($day),"w")),[0,6])){
                        $preselected = json_decode("[".$_SESSION["weekend"]."]");
                    } else {
                        $preselected = json_decode("[".$_SESSION["weekday"]."]");
                    }
                    create_tagesauswahl($day, array_fill(0,24,0), $preselected);
                }
                ?>

            <div id="fixedSubmitSpace"></div>
            <section id="fixedSubmit">
                <input type="submit" value="Weiter">
            </section>
            </form>
        </main>
        <script type="text/javascript" src="tagesauswahlScript.js">
        </script>
    </body>
</html>

