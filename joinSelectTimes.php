<?php
    session_start();
    
    if(!(isset($_POST["personVorname"]) && isset($_POST["personNachname"]) && isset($_POST["personEmail"]) && 
     isset($_POST["personPrio"]) || isset($_SESSION["personVorname"]) && isset($_SESSION["personNachname"]) &&
     isset($_SESSION["personEmail"]) && isset($_SESSION["personPrio"]) )){
        header('Location: ./joinSingup.php');
        exit();
    }
    if(isset($_POST["personVorname"]) && isset($_POST["personNachname"]) &&
     isset($_POST["personEmail"]) && isset($_POST["personPrio"])){
        $_SESSION["personVorname"] = $_POST["personVorname"];
        $_SESSION["personNachname"] = $_POST["personNachname"];
        $_SESSION["personEmail"] = $_POST["personEmail"];
        $_SESSION["personPrio"] = ($_POST["personPrio"] == "Obligatorisch") ? true : false;
    }
    if(!(isset($_SESSION["poll_id"]))){
        header('Location: ./joinSingup.php');
        exit();
    }

    include("zeiträumeUmwandeln.php");

    include("mysql_connection.php");
    $pollid = $_SESSION["poll_id"];
    $sql = "SELECT * FROM poll WHERE id_poll = $pollid";
    $result = $connection->query($sql) or die($connection->error);
    $polldata = $result->fetch_assoc();
    $pollname = $polldata["p_name"];
    $polllength = $polldata["laenge"];

    $sql = "SELECT * FROM event_dates WHERE id_poll = $pollid";
    $result = $connection->query($sql) or die($connection->error);
    $eventZeitraeume = array();
    while($row = $result->fetch_assoc()) {
        $eventZeitraeume[] = array(
            "startpunkt" => $row["startpunkt"],
            "endpunkt" => $row["endpunkt"]
        );               
    }
    $eventDates = zeitraumZuZeitblock($eventZeitraeume);

    function create_tagesauswahl($day, $eventDates, $personDates){
        $createMode = false;
        include("tagesauswahl_template.php");
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
        <main>
            <h1>Zeiten auswählen</h1>
            <p>Wähle jetzt für jeden Tag die Zeiten aus, an denen du kannst. Gib auch an, wie sicher du
                dir bei einem angegebenen Zeitraum bist. Zeiträume die kleiner als die Terminlänge von
                <b><?php echo $polllength; ?> <?php if($polllength == 1) echo "Stunde"; else echo "Stunden"; ?></b>
                sind, werden ignoriert.
            </p>
            <form method="post" action="joincomplete.php" autocomplete="off">

            <?php
                foreach($eventDates as $day => $values){
                    create_tagesauswahl($day, $values, $values);
                }
            ?>

            <div id="fixedSubmitSpace"></div>
            <section id="fixedSubmit">
                <input type="submit" value="Speichern">
            </section>
            </form>
        </main>
        <script type="text/javascript" src="tagesauswahlScript.js">
        </script>
    </body>
</html>

