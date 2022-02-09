<?php session_start(); ?>
<!doctype  html>
<html>
    <head>

    </head>
    <body>
        <?php
            include("mysql_connection.php");
            
            $email = $_POST["email"];
            $password_user = $_POST['password'];

            $text = "";
            $linkText = "";
            $newLocation = "";
    
            $befehl = "select passwort from users where email = '$email';";
              
            $ergebnis = mysqli_query($connection, $befehl);
            $zuverabeiten = mysqli_fetch_row($ergebnis);
            if(mysqli_num_rows($ergebnis) != 0){
                
                foreach ($zuverabeiten as $row) {
                    $password_abgefagt = $row;
                }

                if (password_verify($password_user, $password_abgefagt)){

                    $befehl = "select id_user from users where email like '$email';";
                    $ergebnis = mysqli_query($connection, $befehl );
                    $userid = mysqli_fetch_row($ergebnis);

                    $_SESSION["user_login"] = $userid[0];
                    $text = "Sie sind angemeldet";
                    $linkText = "Zum Hub";
                    $newLocation = "./hub.php";

                } else {
                    $text = "Das Passwort ist falsch";
                    $linkText = "Zur Anmeldeseite";
                    $newLocation = "./Anmelden.html";
                }


            } else {
                $text =  "Die E-Mail existiert nicht";
                $linkText = "Zur Anmeldeseite";
                $newLocation = "./Anmelden.html";
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