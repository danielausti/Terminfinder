<?php session_start(); ?>
<!DOCTYPE html>
<html>
<?php
	if(!isset($_GET["idURL"])){  // Nichts machen, wenn idURL nicht gesetzt ist
		echo "Gib eine Umfrage ID mit ?idURL=... an"; exit(); 
	} 
    $idURL = filter_var($_GET["idURL"], FILTER_SANITIZE_STRING);


	include("mysql_connection.php");

	if(!isset($_SESSION["user_login"])){ //Überüfen, ob ein Nutzer angemeldet ist
		header("Location: Anmelden.html"); //Nutzer auf die Anmeldeseite schicken, wenn nicht
	}
	$befehl = "select id_user from users where id_user = ".$_SESSION["user_login"];
	$ergebnis = mysqli_query($connection,$befehl);
	$userid = mysqli_fetch_assoc($ergebnis)["id_user"];

	

	$finalerTermin;
	$terminGefunden = true;
	$ausgabetext = "";

	$befehl = "select id_poll, id_user from poll where url_id = '$idURL'";//Eigentlich als assoziatives Array
	$ergebnis = mysqli_query($connection,$befehl);
	if(mysqli_num_rows($ergebnis) == 0){ //Nichts machen, wenn die Umfrage nicht existiert
		echo("Die Umfrage mit der ID $idURL existiert nicht"); exit(); 
	} 
	$idPoll = mysqli_fetch_assoc($ergebnis);
	if($idPoll['id_user'] != $userid){ //Überprüfen, ob der Nutzer der Initiator ist
		echo "Sie sind nicht berechtigt, diese Seite zu sehen"; exit();
	}

    $befehl = "select * from ergebnisse where id_poll = ".$idPoll['id_poll'];
    $ergebnis = mysqli_query($connection,$befehl);
	if(mysqli_num_rows($ergebnis) == 0){ //Nichts machen, wenn es kein Ergebnis gibt
		echo("Diese Umfrage hat noch kein Ergebnis"); exit(); 
	} 
	$termin1 = mysqli_fetch_assoc($ergebnis);
	$termin2 = mysqli_fetch_assoc($ergebnis);
	$termin3 = mysqli_fetch_assoc($ergebnis);
	$termin4 = mysqli_fetch_assoc($ergebnis);
	$termin5 = mysqli_fetch_assoc($ergebnis);

?>
	<head>
	<meta charset="UTF-8">
	<title>Ergebnis</title>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="terminfinder_style.css">
	<link rel="icon" href="kalender.ico">
	</head>
	
	<body>
		<?php include("header.php"); ?>
		<main>
	
	<form action="ergebnisseite.php?idURL=<?php echo $idURL; ?>" method="POST">
		<h2>Wählen Sie Ihren bevorzugten Termin aus!</h2>
		<table>
			<?php if($termin1 != null){ //Überprüfen, ob Termin gefunden wurde ?> 
				<tr>
				<td><?php echo ($termin1['startpunkt'] ."-".$termin1['endpunkt'] ) ?><p>An diesem Termin können <?php echo ($termin1['anzahl_obligatorisch_teilnehmer']) ?> obligatorische Teilnehmer und <?php echo ($termin1['anzahl_optionaler_teilnehmer']) ?> optionale Teilnehmer</p></td>
				<td><input type ="submit" name="termin1" class="button" value="Auswählen" ></td>	
				</tr>
			<?php } if($termin2 != null){ ?>
				<tr>
				<td><?php echo ($termin2['startpunkt'] ."-".$termin2['endpunkt'] ) ?><p>An diesem Termin können <?php echo ($termin2['anzahl_obligatorisch_teilnehmer']) ?> obligatorische Teilnehmer und <?php echo ($termin2['anzahl_optionaler_teilnehmer']) ?> optionale Teilnehmer</p></td>
				<td><input type ="submit" name="termin2" class="button" value="Auswählen" ></td>	
				</tr>
			<?php } if($termin3 != null){ ?>
				<tr>
				<td><?php echo ($termin3['startpunkt'] ."-".$termin3['endpunkt'] )?><p>An diesem Termin können <?php echo ($termin3['anzahl_obligatorisch_teilnehmer']) ?> obligatorische Teilnehmer und <?php echo ($termin3['anzahl_optionaler_teilnehmer']) ?> optionale Teilnehmer</p></td>
				<td><input type ="submit" name="termin3" class="button" value="Auswählen" ></td>	
				</tr>
			<?php } if($termin4 != null){ ?>
				<tr>
				<td><?php echo ($termin4['startpunkt'] ."-".$termin4['endpunkt'] )?><p>An diesem Termin können <?php echo ($termin4['anzahl_obligatorisch_teilnehmer']) ?> obligatorische Teilnehmer und <?php echo ($termin4['anzahl_optionaler_teilnehmer']) ?> optionale Teilnehmer</p></td>
				<td><input type ="submit" name="termin4" class="button" value="Auswählen" ></td>	
				</tr>
			<?php } if($termin5 != null){ ?>
				<tr>
				<td><?php echo ($termin5['startpunkt'] ."-".$termin5['endpunkt'] )?><p>An diesem Termin können <?php echo ($termin5['anzahl_obligatorisch_teilnehmer']) ?> obligatorische Teilnehmer und <?php echo ($termin5['anzahl_optionaler_teilnehmer']) ?> optionale Teilnehmer</p></td>
				<td><input type ="submit" name="termin5" class="button" value="Auswählen" ></td>	
				</tr>
			<?php } ?>
			<tr>
			<td><input type ="submit" name="nichts" class="button" value="Keiner davon!" ></td>	
			</tr>

<?php

		$terminAusgewaehlt = true;
		if(array_key_exists('termin1', $_POST)) {
            $finalerTermin = $termin1;
        }
        else if(array_key_exists('termin2', $_POST)) {
            $finalerTermin = $termin2;
        }
		else if(array_key_exists('termin3', $_POST)) {
            $finalerTermin = $termin3;
        }
		else if(array_key_exists('termin4', $_POST)) {
            $finalerTermin = $termin4;
        }
		else if(array_key_exists('termin5', $_POST)) {
            $finalerTermin = $termin5;
        }
		else if(array_key_exists('nichts', $_POST)) {
            $terminGefunden = false;
        } else {
			$terminAusgewaehlt = false; //Wenn keine Variable gefunden wurde, hat der Nutzer noch nicht ausgewählt
		}
		
		
		if($terminAusgewaehlt == true){
			//Titel der Abstimmung ermitteln
				$befehl = "select p_name from poll where id_poll = ".$idPoll['id_poll'];
				$ergebnisTitel = mysqli_query($connection,$befehl);
				$titel = mysqli_fetch_assoc($ergebnisTitel);


			//Ausgabetext und E-Mail Inhalt genäß Auswahl erstellen	
			if($terminGefunden == true)
			{
				$ausgabetext = "Die Teilnehmer wurden über Ihre Auswahl informiert. Sie können diese Seite nun schließen.";
				$inhalt =  "Die Abstimmung mit dem Titel: ".$titel['p_name']." ist beendet.
									Der folgende Termin wurde vom Initiator ausgewählt:
									".$finalerTermin['startpunkt']." - ".$finalerTermin['endpunkt']; 
			}
			else
			{
				$ausgabetext = "Es tut uns Leid, dass keiner der Termine geeignet ist. Die Teilnehmer wurden darüber informiert. Sie können diese Seite nun schließen.";
				$inhalt =  "Die Abstimmung mit dem Titel: ".$titel['p_name']." ist beendet.
									Es konnte leider kein geeigneter Termin gefunden werden"; 
			}

			
				$befehl = "select email from person where id_poll = ".$idPoll['id_poll'];
				$ergebnisMail = mysqli_query($connection,$befehl);
				$empfaenger = mysqli_fetch_assoc($ergebnisMail)["email"];

				while($empfaenger != null)
				{
					
						//Teilnehmer informieren
						$betreff = "Abstimmung beendet";
						$absender = "From: terminfinder@bkh-informatik.de"; 
						
						
						mail($empfaenger, $betreff, $inhalt, $absender);

						$empfaenger = mysqli_fetch_assoc($ergebnisMail);
				}

				$befehl = "delete from poll where id_poll = ".$idPoll['id_poll'];
				mysqli_query($connection,$befehl);
		}


?>

			<tr>
			<td><?php echo $ausgabetext ?></td>	
			</tr>
		</table>

	</form>
	</main>
	</body>

</html>

