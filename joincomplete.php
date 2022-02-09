<?php
    session_start();
    
    if(!( isset($_SESSION["personVorname"]) && isset($_SESSION["personNachname"]) &&
     isset($_SESSION["personEmail"]) && isset($_SESSION["personPrio"]) && isset($_SESSION["poll_id"]))){
        header('Location: ./join_selecttimes.php');
        exit();
    }

    include("zeiträumeUmwandeln.php");

    $error = "";
    include("mysql_connection.php");

    $pollid = $_SESSION["poll_id"];
    $sql = "SELECT * FROM poll WHERE id_poll = $pollid";
    $result = mysqli_query($connection, $sql) or die(mysqli_error($connection));
    $polldata = mysqli_fetch_assoc($result);
    $pollname = $polldata["p_name"];
    $polllength = $polldata["laenge"];
    $pollend = $polldata["end_date"];

    $personVorname = $_SESSION["personVorname"];
    $personNachname = $_SESSION["personNachname"];
    $personEmail = $_SESSION["personEmail"];
    $personPrio = intval($_SESSION["personPrio"]);

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
            header('Location: ./join_selecttimes.php');
            exit();
        }
    }

    $urlid = createUrlId(10);
    $sql = "INSERT INTO person VALUES (null, '$personVorname', '$personNachname', '$personEmail', $personPrio, '$urlid', $pollid)";
    mysqli_query($connection, $sql) or die(mysqli_error($connection));
    
    $sql = "SELECT LAST_INSERT_ID() id;";
    $result = mysqli_query($connection, $sql);
    $personid = intval(mysqli_fetch_assoc($result)["id"]);

    $changelink = getFullLink()."/change.php?id=$urlid";

    $dates = array();
    foreach($eventDates as $day => $values){
        $values = array_map("intval", explode(",",$_POST[$day]));
        unset($_POST[$day]);
        $dates[$day] = $values;
    }
    $personDates = bloeckeZusammen($eventDates, $dates);
    $personZeitraeume = auswahlZuZeitraumGeteilt($personDates,$polllength);

    foreach($personZeitraeume as $zeitraum){
        $start = $zeitraum["startpunkt"];
        $end = $zeitraum["endpunkt"];
        $sicherheit = $zeitraum["sicherheit"];

        $sql = "INSERT INTO person_dates VALUES (null, '$start', '$end', $sicherheit, $personid)";
        mysqli_query($connection, $sql);
    }

    unset($_SESSION["personVorname"]);
    unset($_SESSION["personNachname"]);
    unset($_SESSION["personEmail"]);
    unset($_SESSION["personPrio"]);
    unset($_SESSION["poll_id"]);


    //Mail mit den Daten schicken
    $to = $personEmail;
    $subject = 'Du bist einer Umfrage beigetreten';
    $absender = "terminfinder@bkh-informatik.de"; 

    $headers = "From: " . $absender . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    $message = "
    <p>Hallo $personVorname,</p>"
    ."<p>Du bist der Umfrage mit dem Titel <b>$pollname</b> erfolgreich beigetreten.</p>"
    ."<p>Unter diesem Link kannst du deine ausgewählten Tage ändern: <a href='$changelink'>$changelink</a>.</p>"
    ."<p>Du erhälst am <b>".date_format(date_create($pollend),"d.m.Y")."</b> eine E-Mail, wenn die Umfrage abgeschlossen ist.</p>"
    ."<p>Vielen Dank dass du den Terminfinder benutzt.</p>";

    mail($to, $subject, $message, $headers);


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
                    <p>Du bist jetzt im Termin <b><?php echo $pollname; ?></b> eingetragen.</p>
                    <div class="standardForm">
                        <label for="changelink">Unter diesem Link kannst du deine ausgewählten Tage ändern:</label>
                        <input id="changelink" class="nomargin" type="text" value="<?php echo $changelink; ?>" readonly>
                        <button onclick="copyLink(event)" copyfrom="changelink">Link kopieren</button>
                    </div>
                    <p>Du bekommst am <?php echo date_format(date_modify(date_create($pollend),"+1 day"),"d.m.Y") ?>
                        eine E-Mail, wenn die Umfrage abgeschlossen ist.</p>

            <?php } else { ?>
                <h1>Fehler</h1>
                <p>Du konnntest der Umfrage nicht beitreten</p>
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
function createUrlId($length){
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $id = array(); 
    $charsLen = strlen($chars) - 1; 
    for ($i = 0; $i < $length; $i++) {
        $n = rand(0, $charsLen);
        $id[] = $chars[$n];
    }
    return implode($id);        
}
function getFullLink(){
    $completeLink = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") .
     "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    return substr($completeLink, 0, strrpos( $completeLink, '/') );
}
?>