<?php 
    session_start();
    
    $error = "";
    if(isset($_GET["id"])){
        $url_id = $_GET["id"];

        include("mysql_connection.php");

        if($url_id != ""){
            $sql = "SELECT * FROM poll WHERE url_id = '$url_id'";
            $result = mysqli_query($connection, $sql) or die(mysqli_error($connection));
            if(!(mysqli_num_rows($result) == 0 || $result == false)){
                $polldata = mysqli_fetch_assoc($result);

                $pollid = $polldata["id_poll"];
                $pollname = $polldata["p_name"];
                $polldescription = $polldata["beschreibung"];
                $polllength = intval($polldata["laenge"]);
                $userid = $polldata["id_user"];

                $pollend = date_create($polldata["end_date"]." 23:59:59");
                $interval = date_diff($pollend, date_create("now"));
                $pollendTime = $interval->format('%a Tagen, %h Stunden, %i Minuten');
                if(intval($interval->format('%a'))==1){
                    $pollendTime = str_replace("Tagen", "Tag", $pollendTime);
                }

                $sql = "SELECT * FROM users WHERE id_user = $userid";
                $result = mysqli_query($connection, $sql) or die(mysqli_error($connection));
                $userdata = mysqli_fetch_assoc($result);

                $userVorname = $userdata["vorname"];
                $userNachname = $userdata["nachname"];
                $userEmail = $userdata["email"];

                $_SESSION["poll_id"] = $pollid;
            } else {
                $error = "Die ID ist nicht gültig";
            }
        } else {
            $error = "noid";
        }
    } else {
        $error = "noid";
    }
?>

<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Beitreten</title>

        <link rel="stylesheet" type="text/css" href="terminfinder_style.css">
        <link rel="icon" href="kalender.ico">
    </head>
    <body>
        <?php include("header.php"); ?>
        <main>
            <?php if($error == ""){ ?>
            <h1>Terminumfrage beitreten</h1>
            <p>Du wurdest zu folgender Umfrage eingeladen:</p>
            
            <form method="" action="join_singup.php" class="standardForm" autocomplete="off">
                <div class="joinDiv">
                    <h2><b>Name: </b><?php echo $pollname; ?></h2>
                    <h3>Beschreibung:</h3>
                    <p class="linebreak"><?php echo $polldescription; ?></p>
                    <h2><b>Ersteller: </b><?php echo "$userVorname $userNachname ($userEmail)"; ?></h2>
                    <p>Der Termin soll <b><?php echo $polllength; ?> 
                        <?php if($polllength == 1) echo "Stunde"; else echo "Stunden"; ?></b> dauern.</p>
                    <p>Die Umfrage endet in <b><?php echo $pollendTime; ?></b></p>

                </div>
               
                <div class="flexright">
                    <input type="submit" value="Beitreten">
                </div>
            </form>



            <?php } else if ($error == "noid") { ?>
                <h1>Gib eine ID an</h1>
                <p>Um einem Termin beizutreten, benötigst du eine Termin ID.</p>
                <form method="get" action="join.php" class="standardForm">
                    <input id="insertPollId" type="text" name="id">
                    <button type="submit">Öffnen</button>
                </div>
            <?php } else { ?>
                <h1>Fehler</h1>
                <p><?php echo $error; ?></p>
            <?php } ?>
        </main>
        <script>
                function openPollWithID(){
                    let id = document.getElementById("insertPollId").value;
                    if(id != ""){
                        let url = window.location.href.split('?')[0];;
                        window.location.href = url + "?id=" + id;
                    }
                }
            </script>
    </body>
</html>

