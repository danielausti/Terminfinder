<?php 
    session_start();
    if(!(isset($_SESSION["poll_id"]))){
        header('Location: ./join.php');
        exit();
    }

    include("mysql_connection.php");
    $pollid = $_SESSION["poll_id"];
    $sql = "SELECT * FROM poll WHERE id_poll = $pollid";
    $result = $connection->query($sql) or die($connection->error);
    $polldata = $result->fetch_assoc();
    $pollname = $polldata["p_name"];
?>

<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Erstellen</title>

        <link rel="stylesheet" type="text/css" href="terminfinder_style.css">
        <link rel="icon" href="kalender.ico">
    </head>
    <body>
        <?php include("header.php"); ?>
        <main class="maxwidth">
            <h1>Beitreten</h1>
            <p>Gib hier deine Daten an, um dem Termin <b><?php echo $pollname; ?></b> beizutreten. Die Daten werden
             am Ende der Umfrage sofort gelöscht.</p>
            <form method="post" action="joinSelectTimes.php" class="standardForm" autocomplete="off">
                <label for="personVorname">Vorname: </label>
                <input id="personVorname" name="personVorname" type="text" placeholder="Vorname" 
                 maxlength="255" minlength="2" required>
                <label for="personNachname">Nachname: </label>
                <input id="personNachname" name="personNachname" type="text" placeholder="Nachname" 
                 maxlength="255" minlength="2" required>
                <label for="personEmail">E-Mail: </label>
                <input id="personEmail" name="personEmail" type="email" placeholder="E-Mail" 
                 maxlength="255" minlength="2" required>
                <h3>Bist du für diesen Termin Obligatorisch oder Optional?</h3>
                <fieldset>
                    <input type="radio" id="personPrioOblig" name="personPrio" value="Obligatorisch" checked required>
                    <label for="personPrioOblig"> <b>Obligatorisch:</b> Ich muss auf jeden Fall dabei sein</label>
                    <br> 
                    <input type="radio" id="personPrioOptio" name="personPrio" value="Optional" required>
                    <label for="personPrioOptio"> <b>Optional:</b> Ich möchte dabei sein, wenn es möglich ist</label>
                </fieldset>
                <div class="flexright">
                    <input type="submit" value="Weiter">
                    <input type="reset" value="Zurücksetzen">
                </div>
            </form>
            </main>
    </body>
</html>

