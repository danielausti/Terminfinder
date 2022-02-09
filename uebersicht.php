<?php
    session_start();
    include("mysql_connection.php");

    $poll_id = $_POST["id_poll"];
    $poll_name = $_POST["name"];
    $poll_beschreibung = $_POST["beschreibung"];
    $id_user=$_SESSION["user_login"];


    $befehl = "select vorname, nachname from person join poll on person.id_poll = poll.id_poll where poll.id_poll = $poll_id and id_user = $id_user  ;";
    echo $befehl;
    $ergebnis_name = mysqli_query($connection, $befehl );
    
  

    
     
?>

<!doctype html>
<html lang = "de">
    <head>
        <meta charset= "UTF-8">
        <title>Terminfinder.Hub</title> 
        <link rel="stylesheet" href="uebersicht_style.css">
        <style>
            <?php include("uebersicht_style.css"); ?>
        </style>
    </head>
    <body>
        <header>
            <h2><h2>Terminfinder </h2></h2>
        </header>
        <div id="poll">
            <table>
                <tr>
                    <th>Name</th>
                    <th>Beschreibung</th>
                    <th>Mitglieder</th>
                </tr> 
                <tr>
                    <td><?php  if(!(is_null($poll_name)) ){echo($poll_name);}else{echo("Lehr");}?></td>
                    <td><?php if(isset($poll_beschreibung)){echo($poll_beschreibung);}else{echo("Lehr");} ?></td>
                    <td>
                        <?php

                            do{
                                $polls_name_in_array = mysqli_fetch_row($ergebnis_name);
                                 

                                if(isset($polls_name_in_array[0])){echo($polls_name_in_array[0]);}
                                echo(" ");
                                if(isset($polls_name_in_array[1])){echo($polls_name_in_array[1]);}
                                echo(",");
                                
                                   
                            }while(isset($polls_name_in_array))

                        ?>    
                    </td>
            </table>
        </div>
    </body>
</html>