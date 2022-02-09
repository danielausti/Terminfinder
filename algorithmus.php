<?php
            
            $termineGefunden = false;
            include("mysql_connection.php");
            $aktuellesDatum = date("Y-m-d");
                     

            //geendete Umfragen ermitteln
            $befehl = "select id_poll from poll
                        where end_date = $aktuellesDatum ";
            $ergebnisPolls = mysqli_query($connection, $befehl);
         
            if($ergebnisPolls == false)
            {
                $geendetePolls = null;
            }
            else
            {
                $geendetePolls = mysqli_fetch_assoc($ergebnisPolls);
            }
            echo $geendetePolls;

            while($geendetePolls != null) 
            {
                $idPoll = $geendetePolls['id_poll'];

                //Auslesen des Ergebnis so korrekt?
                //Obligatorische Nutzer ermitteln
                $befehl = "select count(id_person) as anzahl from person where prioritaet = true and id_poll = $idPoll";
                $ergebnisAnzahlObligatorisch = mysqli_query($connection,$befehl);
                $anzahlObligatorisch = mysqli_fetch_assoc($ergebnisAnzahlObligatorisch);
                
                //Schleife zieht jeden Durchlauf einen Teilnehmer (obligatorisch) ab
                while($anzahlObligatorisch > 0)
                {
                    //Schleife zieht jeden Durchlauf eine Sicherheitsstufe ab
                    for($sicherheit=3; $sicherheit>0; $sicherheit--)
                    {
                        
                        $befehl =   "select obligatorische.startpunkt as startpunkt, obligatorische.endpunkt as endpunkt, obligatorische.anzahl as anzahlObligatorische, optionale.anzahl as anzahlOptionale from (select startpunkt , endpunkt, count(startpunkt) as 'anzahl', sicherheit from  person_dates join person join poll on person.id_person = person_dates.id_person and poll.id_poll = person.id_poll
                                    where person.prioritaet = true and poll.id_poll = $idPoll
                                    group by startpunkt
                                    having count(startpunkt) >= ".$anzahlObligatorisch['anzahl']." and sicherheit >= $sicherheit
                                    order by count(person.id_person) desc, AVG(sicherheit) desc) as obligatorische left join (select startpunkt, endpunkt, count(startpunkt) as 'anzahl', sicherheit from person_dates join person join poll on person.id_person = person_dates.id_person and poll.id_poll = person.id_poll
                                    where person.prioritaet = false and poll.id_poll = $idPoll
                                    group by startpunkt
                                    having count(startpunkt) >= 1 and sicherheit >= 1
                                    order by count(person.id_person) desc , AVG(sicherheit) desc) as optionale on obligatorische.startpunkt = optionale.startpunkt 
                                    order by obligatorische.anzahl desc, optionale.anzahl desc limit 5;";
                        
                        //Auslesen des Ergebnis so korrekt?
                        $ergebnis = mysqli_query($connection, $befehl );
                        $anzahlTermine = mysqli_num_rows($ergebnis); // Anzahl Elemente jeder Spalte
                        
                        
                        if($anzahlTermine == 5)
                        {
                            $termineGefunden = true;
                            break;
                        }

                    }
            
                if($termineGefunden == true) 
                {
                    break;
                }

                    $anzahlObligatorisch['anzahl'] --;
                }

                //Befüllen der Ergebnistabelle
                for($i = 0; $i<=4;$i++)
                {
                    $termine = mysqli_fetch_assoc($ergebnis);
                    if($termine['anzahlOptionale'] == null)
                    {
                        $termine['anzahlOptionale'] = 0;
                    }
                    $befehl = "insert into ergebnisse values (null,'".$termine['startpunkt']."','".$termine['endpunkt']."',".$idPoll.",".$termine['anzahlObligatorische'].",".$termine['anzahlOptionale'].")"; // richtige Namen? Übergabe Array?
                    mysqli_query($connection, $befehl);
                }
    
                //Titel der Abstimmung ermitteln
                $befehl = "select p_name from poll where id_poll = $idPoll";
                $ergebnistitel = mysqli_query($connection,$befehl);
                $titel = mysqli_fetch_assoc($ergebnistitel);

                //Initiator informieren
                $befehl = "select email from users join poll on users.id_user = poll.id_user where poll.id_poll = $idPoll";
                $ergebnisEmpfaenger = mysqli_query($connection,$befehl);
                $empfaenger = mysqli_fetch_assoc($ergebnisEmpfaenger);
                $betreff = "Ihre Abstimmung ist beendet";
                $absender = "terminfinder@bkh-informatik.de"; 

                $befehl = "select url_id from poll where id_poll = ".$idPoll;
                $ergebnisurl = mysqli_query($connection,$befehl);
                $idURL = mysqli_fetch_assoc($ergebnisurl);
                $link = "http://bkh-informatik.de/19gia/19ZgoddaYanM/ergebnisseite.php?idURL=".$idURL['url_id'];//Link zur Ergebnisseite mit Übergabe der Poll ID
                $inhalt =  "Ihre Abstimmung mit dem Titel: ".$titel['p_name']." ist beendet.
                            Unter dem folgenden Link können Sie sich die fünf besten Termine ansehen und Ihren Favoriten auswählen.

                            $link
                            "; 
                
                mail($empfaenger['email'], $betreff, $inhalt, $absender);

                $geendetePolls = mysqli_fetch_assoc($ergebnisPolls);
            }

            
         
            

        ?>