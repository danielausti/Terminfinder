<?php
    session_start();
    
    if(!isset($_SESSION["user_login"])){
        header('Location: ./Anmelden.html');
    }

    if(!(isset($_SESSION["pollname"]) && isset($_SESSION["polllength"]) && isset($_SESSION["pollend"]) &&
        isset($_SESSION["calendar"]) && isset($_SESSION["polldescription"]))){
        header('Location: ./create_selecttime.php');
        exit();
    }

    $selectedDates = explode(",",$_SESSION["calendar"]);

    foreach($selectedDates as $day){
        if(!(isset($_POST[$day]))){
            header('Location: ./create_selecttime.php');
            exit();
        }
    }

    include("zeiträumeUmwandeln.php");

    $error = "";
    include("mysql_connection.php");
        
    $sql = "SELECT * FROM users WHERE id_user = ".$_SESSION["user_login"];
    $result = mysqli_query($connection, $sql) or die(mysqli_error($connection));
    $row = mysqli_fetch_assoc($result);
    $userid = ($result != false) ? intval($row["id_user"]) : null;
    $usermail = $row["email"];
    $userVorname = $row["vorname"];

    $pollname = $_SESSION["pollname"];
    $polldescription = $_SESSION["polldescription"];
    $polllength = intval($_SESSION["polllength"]);
    $pollend = date_format(date_create($_SESSION["pollend"]), "Y-m-d");
    $urlid = createUrlId(10);
    $sql = "INSERT INTO poll VALUES (null, '$pollname', '$polldescription', $polllength, '$pollend', '$urlid', $userid)";
    mysqli_query($connection, $sql) or die(mysqli_error($connection));

    $sql = "SELECT LAST_INSERT_ID() id;";
    $result = mysqli_query($connection, $sql);
    $pollid = intval(mysqli_fetch_assoc($result)["id"]);

    $fullLink = getFullLink();
    $polllink = $fullLink."/join.php?id=$urlid";
    $fullDashboardLink = $fullLink."/hub.php";


    unset($_SESSION["pollname"]);
    unset($_SESSION["polldescription"]);
    unset($_SESSION["polllength"]);
    unset($_SESSION["pollend"]);
    unset($_SESSION["calendar"]);
    unset($_SESSION["weekday"]);
    unset($_SESSION["weekend"]);

    $dates = array();
    foreach($selectedDates as $day){
        $values = array_map("convertCreated",
            array_map("intval", explode(",",$_POST[$day])));
        unset($_POST[$day]);
        $dates[$day] = $values;
    }
    $zeitraeume = zeitblockZuZeitraum($dates);
    foreach($zeitraeume as $zeitraum){
        $start = $zeitraum["startpunkt"];
        $end = $zeitraum["endpunkt"];

        $difference = date_diff(date_create($end),date_create($start));
        $hours = $difference->h + ($difference->days*24);
        if($hours >= $polllength){
            $sql = "INSERT INTO event_dates VALUES (null, '$start', '$end', $pollid)";
            mysqli_query($connection, $sql);
        }
    }

    //Mail mit den Daten schicken
    $to = $usermail;
    $subject = 'Deine Umfrage wurde erstellt';
    $absender = "terminfinder@bkh-informatik.de"; 

    $headers = "From: " . $absender . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    $message = "
    <p>Hallo $userVorname,</p>"
    ."<p>Deine Umfrage mit dem Titel <b>$pollname</b> wurde erstellt.</p>"
    ."<p>Mit diesem Link können die Teilnehmer beitreten: <a href='$polllink'>$polllink</a>.</p>"
    ."<p>In deinem <a href='$fullDashboardLink'>Dashboard</a> kannst du jederzeit den Status deiner Umfrage ansehen.</p>"
    ."<p>Du erhälst am <b>".date_format(date_create($pollend),"d.m.Y")."</b> eine E-Mail, wenn die Umfrage abgeschlossen ist.</p>"
    ."<p>Vielen Dank dass du den Terminfinder benutzt.</p>";

    mail($to, $subject, $message, $headers);
    
?>

<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Umfrage erstellt</title>

        <link rel="stylesheet" type="text/css" href="terminfinder_style.css">
        <link rel="icon" href="kalender.ico">
    </head>
    <body>
        <?php include("header.php"); ?>
        <main>
            <?php if($error == "") { ?>
                <h1>Fertig</h1>
                <p>Deine Umfrage <b><?php echo $pollname; ?></b> wurde erstellt.</p>
                <div class="standardForm">
                    <label for="polllink">Hier ist der Link für die Teilnehmer:</label>
                    <input id="polllink" class="nomargin" type="text" value="<?php echo $polllink ?>" readonly>
                    <button onclick="copyLink(event)" copyfrom="polllink">Link kopieren</button>
                </div>
                <p>Im <a href="hub.php">Dashboard</a> kannst du jederzeit sehen, 
                    wer schon abgestimmt hat.<br></p>
                <p>Du bekommst am <?php echo date_format(date_create($pollend),"d.m.Y") ?>
                    eine E-Mail, wenn die Umfrage abgeschlossen ist.</p>
            <?php } else { ?>
                <h1>Fehler</h1>
                <p>Die Umfrage konnte nicht erstellt werden</p>
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