<?php
    session_start();
    
    if(!isset($_SESSION["user_login"])){
        header('Location: ./Anmelden.html');
    }
    if(!(isset($_POST["calendar"]) || isset($_SESSION["calendar"]))){
        header('Location: ./create_selectdates.php');
    }
    if(!(isset($_SESSION["pollname"]) && isset($_SESSION["polllength"]) &&
        isset($_SESSION["polldescription"]) && isset($_SESSION["pollend"]))){
        header('Location: ./create_selectdates.php');
        exit();
    }

    include("zeiträumeUmwandeln.php");

    if(isset($_POST["calendar"]))
        if($_POST["calendar"] == ""){
            header('Location: ./create_selectdates.php');
        }
        $_SESSION["calendar"] = $_POST["calendar"];

    function create_tagesauswahl($day, $eventDates, $personDates){
        $createMode = true;
        include("tagesauswahl_template.php");
    }

    $selectedDates = explode(",",$_SESSION["calendar"]);
    $showWeekday = false;
    $showWeekend = false;
    foreach($selectedDates as $day){
        if(in_array(intval(date_format(date_create($day),"w")),[0,6])){
            $showWeekend = true;
            if($showWeekday){break;}
        } else {
            $showWeekday = true;
            if($showWeekend){break;}
        }
    }

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
            <p>Wenn du möchtest kannst du Standard Zeiten für die Wochentage festlegen.</p>
            <form method="post" action="./create_selecttime.php" autocomplete="off">
                
                <?php 
                
                if($showWeekday)
                    create_tagesauswahl("2000-01-01", array_fill(0,24,0), array_fill(0,24,0));
                if($showWeekend)
                    create_tagesauswahl("2000-01-02", array_fill(0,24,0), array_fill(0,24,0));
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

