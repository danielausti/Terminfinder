<?php 
    session_start();

    if(!isset($_SESSION["user_login"])){
        header('Location: ./Anmelden.html');
    }

    $dateMin = date_format(date_create("NOW"), "Y-m-d");
    $dateDefault = date_format(date_modify(date_create("NOW"),"+3 days"), "Y-m-d");
    $dateMax = date_format(date_modify(date_create("NOW"),"+1 month"), "Y-m-d");
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
        <main>
            <h1>Umfrage Erstellen</h1>
            <p>Gib hier die Daten für deinen Termin ein.</p>
            <form method="post" action="create_selectdates.php" class="standardForm" autocomplete="off">
                <label for="pollname">Name des Termins: </label>
                <input id="pollname" name="pollname" type="text" placeholder="Name des Termins" maxlength="255" minlength="2" required>
                <label for="polldescription">Beschreibung: </label>
                <textarea id="polldescription" name="polldescription" type="text" placeholder="Beschreibung" maxlength="400"></textarea>
                <label for="polllength">Länge des Termins (in Stunden): </label>
                <input id="polllength" name="polllength" type="number" placeholder="Länge in Stunden" required
                 min="1" max="2000">
                <label for="pollend">Bis wann (um 23:59 Uhr) soll die Umfrage laufen? </label>
                <input id="pollend" name="pollend" type="date" value="<?php echo $dateDefault ?>" required
                 min="<?php echo $dateMin ?>" max="<?php echo $dateMax ?>">
                <div class="flexright">
                    <input type="submit" value="Weiter">
                    <input type="reset" value="Zurücksetzen">
                </div>
            </form>
            </main>
    </body>
</html>

