<?php
    session_start();

    if(!isset($_POST["url_id"])){
        header("Location: ./change.php");
    }
    $personIdUrl = $_POST["url_id"];

    include("zeiträumeUmwandeln.php");

    $error = "";
    include("mysql_connection.php");
    
    $sql = "SELECT * FROM person WHERE url_id = '$personIdUrl'";
    $result = mysqli_query($connection, $sql) or die(mysqli_error($connection));
    $persondata = mysqli_fetch_assoc($result);
    $personid = $persondata["id_person"];
    $pollid = $persondata["id_poll"];

    $sql = "SELECT * FROM poll WHERE id_poll = $pollid";
    $result = mysqli_query($connection, $sql) or die(mysqli_error($connection));
    $polldata = mysqli_fetch_assoc($result);
    $pollname = $polldata["p_name"];
    $polllength = $polldata["laenge"];
    $pollend = $polldata["end_date"];

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

    foreach($eventDates as $day => $values){
        if(!(isset($_POST[$day]))){
            header('Location: ./change.php');
            exit();
        }
    } 

    $changelink = getFullLink()."/change.php?id=$personIdUrl";

    $dates = array();
    foreach($eventDates as $day => $values){
        $values = array_map("intval", explode(",",$_POST[$day]));
        unset($_POST[$day]);
        $dates[$day] = $values;
    }
    $personDates = bloeckeZusammen($eventDates, $dates);
    $personZeitraeume = auswahlZuZeitraumGeteilt($personDates,$polllength);

    $sql = "DELETE FROM person_dates WHERE id_person = $personid";
    $result = mysqli_query($connection, $sql) or die(mysqli_error($connection));
    if($result == true){

        foreach($personZeitraeume as $zeitraum){
            $start = $zeitraum["startpunkt"];
            $end = $zeitraum["endpunkt"];
            $sicherheit = $zeitraum["sicherheit"];

            $sql = "INSERT INTO person_dates VALUES (null, '$start', '$end', $sicherheit, $personid)";
            mysqli_query($connection, $sql);
        }
    }
?>

<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Fertig</title>

        <link rel="stylesheet" type="text/css" href="terminfinder_style.css">
        <link rel="icon" href="kalender.ico">
    </head>
    <body>
        <?php include("header.php"); ?>
        <main>
            <?php if($error == "") { ?>
                <h1>Fertig</h1>
                <p>Deine Auswahl im Termin <b><?php echo $pollname; ?></b> wurde geändert.</p>
                <div class="standardForm">
                    <label for="changelink">Unter diesem Link kannst du deine ausgewählten Tage erneut ändern:</label>
                    <input id="changelink" class="nomargin" type="text" value="<?php echo $changelink; ?>" readonly>
                    <button onclick="copyLink(event)" copyfrom="changelink">Link kopieren</button>
                </div>
                <p>Du bekommst am <?php echo date_format(date_modify(date_create($pollend),"+1 day"),"d.m.Y") ?>
                    eine E-Mail, wenn die Umfrage abgeschlossen ist.</p>

            <?php } else { ?>
                <h1>Fehler</h1>
                <p>Die Änderungen konnten nicht übernommen werden</p>
                <p><b>Fehler: </b><?php echo $error; ?></p>
            <?php } ?>
        </main>
        <script>    
            function copyLink(event){
                var copyText = document.getElementById(event.target.getAttribute("copyfrom"));
                copyText.select();
                copyText.setSelectionRange(0, 99999);
                if(navigator.clipboard != null){
                    navigator.clipboard.writeText(copyText.value);
                    event.target.innerHTML = "Kopiert!";
                } else {
                    event.target.innerHTML = "Kein HTTPS, bitte manuell kopieren";
                }
                copyText.blur();
                setTimeout(function() {
                    event.target.innerHTML = "Link kopieren";
                }, 500);
            }
        </script>
    </body>
</html>

<?php
function getFullLink(){
    $completeLink = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") .
     "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    return substr($completeLink, 0, strrpos( $completeLink, '/') );
}
?>