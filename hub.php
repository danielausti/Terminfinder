<?php
    session_start();

    include("mysql_connection.php");

        
    $user = $_SESSION["user_login"];      
?>

<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset= "UTF-8">
        <title>Terminfinder.Hub</title> 
        <!--<link rel="stylesheet" href="hub_style.css">-->
        <style><?php include("hub_style.css"); ?></style>
    </head>
    <body>
        
        <header>
            <h2>Terminfinder</h2>
            <div id ="neue_poll" >
                <form action="create.php" method="post">
                    <button type="submit">Neue Abstimmung erzeugen</button>
                </form>
                
                
            </div>
        </header>

        <main>
        
            <div id="bestehende_polls">
                <?php                
                    $befehl = "select id_poll, p_name, beschreibung, id_user from poll where id_user=$user;";
                    $ergebnis = mysqli_query($connection, $befehl );
                  
                    
                    for($i=0; $i<5; $i++){
                            
                        if($i==0){
                            $polls_in_array_1 = mysqli_fetch_row($ergebnis);
                        }elseif($i==1){
                            $polls_in_array_2 = mysqli_fetch_row($ergebnis);
                        }elseif($i==2){
                            $polls_in_array_3 = mysqli_fetch_row($ergebnis);
                        }elseif($i==3){
                            $polls_in_array_4 = mysqli_fetch_row($ergebnis);
                        }elseif($i==4){
                            $polls_in_array_5 = mysqli_fetch_row($ergebnis);
                        }
                         
                        
                        
                    }

                    
                   
                ?>
                <table>
                    <tr>
                        <th><h3>Abstimmungen</h3></th>
                        <th><h3>Beschreibungen</h3></th>
                    </tr>
                    <tr>
                        <td>
                            <form action="uebersicht.php" method="post">
                                <input type="hidden" name="id_poll" value="<?php echo($polls_in_array_1[0]); ?>">
                                <input type="hidden" name="name" value="<?php echo($polls_in_array_1[1]); ?>">
                                <input type="hidden" name="beschreibung" value="<?php echo($polls_in_array_1[2]); ?>">
                                <button type="submit" ><?php if(isset($polls_in_array_1[1])){echo($polls_in_array_1[1]);}else{echo("Freies Feld");} ?></button>    
                            </form>
                            
                        </td>   
                        <td><?php if(isset($polls_in_array_1[2])){echo($polls_in_array_1[2]);}else{echo("Freies Feld");} ?></td>
                    </tr>
                    <tr>
                        <td>
                            <form action="uebersicht.php" method="post">
                                <input type="hidden" name="id_poll" value="<?php echo($polls_in_array_2[0]); ?>">
                                <input type="hidden" name="name" value="<?php echo($polls_in_array_2[1]); ?>">
                                <input type="hidden" name="beschreibung" value="<?php echo($polls_in_array_2[2]); ?>">
                                <button type="submit" ><?php if(isset($polls_in_array_2[1])){echo($polls_in_array_2[1]);}else{echo("Freies Feld");} ?></button>    
                            </form> 
                        </td>
                        <td><?php if(isset($polls_in_array_2[2])){echo($polls_in_array_2[2]);}else{echo("Freies Feld");} ?></td>
                    </tr>
                    <tr>
                        <td>
                            <form action="uebersicht.php" method="post">
                                <input type="hidden" name="id_poll" value="<?php echo($polls_in_array_3[0]); ?>">
                                <input type="hidden" name="name" value="<?php echo($polls_in_array_3[1]); ?>">
                                <input type="hidden" name="beschreibung" value="<?php echo($polls_in_array_3[2]); ?>">
                                <button type="submit" ><?php if(isset($polls_in_array_3[1])){echo($polls_in_array_3[1]);}else{echo("Freies Feld");} ?></button>    
                            </form>
                        </td>
                        <td><?php if(isset($polls_in_array_3[2])){echo($polls_in_array_3[2]);}else{echo("Freies Feld");} ?></td>
                    </tr>
                    <tr>
                        <td>
                            <form action="uebersicht.php" method="post">
                                <input type="hidden" name="id_poll" value="<?php echo($polls_in_array_4[0]); ?>">
                                <input type="hidden" name="name" value="<?php echo($polls_in_array_4[1]); ?>">
                                <input type="hidden" name="beschreibung" value="<?php echo($polls_in_array_4[2]); ?>">
                                <button type="submit" ><?php if(isset($polls_in_array_4[1])){echo($polls_in_array_4[1]);}else{echo("Freies Feld");} ?></button>    
                            </form> 
                        </td>
                        <td><?php if(isset($polls_in_array_4[2])){echo($polls_in_array_4[2]);}else{echo("Freies Feld");} ?></td>
                    </tr>
                    <tr>
                        <td>
                            <form action="uebersicht.php" method="post">
                                <input type="hidden" name="id_poll" value="<?php echo($polls_in_array_5[0]); ?>">
                                <input type="hidden" name="name" value="<?php echo($polls_in_array_5[1]); ?>">
                                <input type="hidden" name="beschreibung" value="<?php echo($polls_in_array_5[2]); ?>">
                                <button type="submit" ><?php if(isset($polls_in_array_5[1])){echo($polls_in_array_5[1]);}else{echo("Freies Feld");} ?></button>    
                            </form>
                        </td>
                        <td><?php if(isset($polls_in_array_5[2])){echo($polls_in_array_5[2]);}else{echo("Freies Feld");} ?></td>
                    </tr>
                </table>
            </div>
        </main>

    </body>

</html>