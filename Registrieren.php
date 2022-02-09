<?php session_start(); ?>
<!doctype  html>
<html>
    <head>
    <title>Registrieren</title>
    <meta charset="utf-8">
    </head>
    <body>
        <?php
            include("mysql_connection.php");

            $password_user = $_POST['password'];
            $passwordHash = password_hash($password_user,PASSWORD_DEFAULT);
            $vorname = $_POST["vorname"];
            $nachname = $_POST["nachname"];
            $email = $_POST["email"];

            $text = "";
            $linkText = "";
            $newLocation = "";
            
            
            
            if($_POST['password']==$_POST['passwordWDH'] ){

                $befehl = "insert into users values (null,'$vorname','$nachname','$email','$passwordHash');";
                //echo($befehl);

                // $befehl = "insert into users values (null,'Martin',\"Mustermann\",'mustermann@gmail.com','1234')";
                
                $ergebnis = mysqli_query($connection, $befehl );

                if(!$ergebnis){
                    $text = mysqli_error($connection);
                    $linkText = "Zur Registrierungsseite";
                    $newLocation = "./Registrieren.html";
                } else {

                    $befehl = "select id_user from users where email like '$email';";
                    $ergebnis = mysqli_query($connection, $befehl );
                    $userid = mysqli_fetch_row($ergebnis);

                    $_SESSION["user_login"] = $userid[0];
                    $text = "Sie wurden registriert";
                    $linkText = "Zum Hub";
                    $newLocation = "./hub.php";
                }
                /*
                if($connection -> connect_error){
                    die(connection -> connect_error);
                }*/

            } else {
                $text = "Die Passwörter stimmen nicht überein";
                $linkText = "Zur Registrierungsseite";
                $newLocation = "./Registrieren.html";
            }

           
            

        ?>
        <p><?php echo $text; ?></p>
        <a href="<?php echo $newLocation; ?>"><?php echo $linkText; ?></a>
        <script>
            window.onload = function(){
                window.location.href = "<?php echo $newLocation; ?>";
            }
        </script>
    </body>
</html>