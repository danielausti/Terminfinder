<?php
    session_start();
    
    include("zeiträumeUmwandeln.php");

    $error = "";
    if(isset($_GET["id"])){

        include("mysql_connection.php");
        $personIdUrl = $_GET["id"];
        $sql = "SELECT * FROM person WHERE url_id = '$personIdUrl'";
        $result = mysqli_query($connection, $sql) or die(mysqli_error($connection));
        if(mysqli_num_rows($result) != 0){

            $persondata = mysqli_fetch_assoc($result);
            $personid = $persondata["id_person"];
            $pollid = $persondata["id_poll"];

            $sql = "SELECT * FROM poll WHERE id_poll = $pollid";
            $result = mysqli_query($connection, $sql) or die(mysqli_error($connection));
            $polldata = mysqli_fetch_assoc($result);
            $pollname = $polldata["p_name"];
            $polllength = $polldata["laenge"];

            $sql = "SELECT * FROM event_dates WHERE id_poll = $pollid";
            $result = mysqli_query($connection, $sql) or die(mysqli_error($connection));
            $eventZeitraeume = array();
            while($row = mysqli_fetch_assoc($result)) {
                $eventZeitraeume[] = array(
                    "startpunkt" => $row["startpunkt"],
                    "endpunkt" => $row["endpunkt"]
                );               
            }
            $eventDates = zeitraumZuZeitblock($eventZeitraeume);

            $sql = "SELECT * FROM person_dates WHERE id_person = $personid";
            $result = mysqli_query($connection, $sql) or die(mysqli_error($connection));
            $personZeitraeume = array();
            while($row = mysqli_fetch_assoc($result)) {
                $personZeitraeume[] = array(
                    "startpunkt" => $row["startpunkt"],
                    "endpunkt" => $row["endpunkt"],
                    "sicherheit" => $row["sicherheit"]
                );               
            }
            $personDates = bloeckeZusammen($eventDates, zeitraumZuZeitblock($personZeitraeume));

        } else {
            $error = "Die ID existiert nicht";
        }
    } else {
        $error = "noid";
    }

    

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
        <main <?php if($error == ""){ ?>class="maxwidth"<?php } ?>>
            <?php if($error == ""){ ?>
                <h1>Zeiten auswählen</h1>
                <p>Wähle für jeden Tag die Zeiten aus, an denen du kannst. Gib auch an, wie sicher du
                    dir bei einem angegebenen Zeitraum bist. Zeiträume die kleiner als die Terminlänge von
                    <b><?php echo $polllength; ?> <?php if($polllength == 1) echo "Stunde"; else echo "Stunden"; ?></b>
                    sind, werden ignoriert.
                </p>
                <form method="post" action="changecomplete.php" autocomplete="off">
                    <input type="hidden" name="url_id" value="<?php echo $personIdUrl; ?>">

                    <?php
                        foreach($eventDates as $day => $values){
                            create_tagesauswahl($day, $values, $personDates[$day]);
                        }
                    ?>

                    <div id="fixedSubmitSpace"></div>
                    <section id="fixedSubmit">
                        <input type="submit" value="Speichern">
                    </section>
                </form>
            <?php } else if ($error == "noid") { ?>
                <h1>Gib eine ID an</h1>
                <p>Um deine Auswahl zu ändern, benötigst du eine ID.</p>
                <form method="get" action="change.php" class="standardForm">
                    <input type="text" name="id">
                    <button type="submit">Öffnen</button>
                </div>
            <?php } else { ?>
                <h1>Fehler</h1>
                <p><?php echo $error; ?></p>
            <?php } ?>
        </main>
        <script type="text/javascript" src="tagesauswahlScript.js">
        </script>
    </body>
</html>

