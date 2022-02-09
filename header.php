<?php
    if(!isset($connection)){
        include("mysql_connection.php");
    }
    $darktheme = isset($_SESSION["theme"]) ? boolval($_SESSION["theme"]) : false;
    $themeEmoji = $darktheme ? "🌙" : "☀️";
    $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") .
     "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    
    if(isset($_SESSION["user_login"])){
        $login = true;
        $sql = "SELECT * FROM users WHERE id_user = ".$_SESSION["user_login"];
        $result = mysqli_query($connection, $sql);
        if($result != false){
            $row = mysqli_fetch_assoc($result);
            
            $userid = intval($row["id_user"]);
            $username = $row["vorname"]." ".$row["nachname"];
            $email = $row["email"];

        } else {
            $login = false;
        }
    } else {
        $login = false;
    }
?>
<?php if($darktheme) { ?>
<style>
    :root{
        --color: white;
        --background: #303030;
        --input-background: #202020;
        --slider-background: #206060;
        --color-calendar: 255,255,255;
        --gray-background: #656565;
        --calendar-select: green;
        --calendar-gray1: #404040;
        --calendar-gray2: #606060;
        --frontimage: url(terminfinder_dark.png);
    }
</style>
<?php } ?>
<header>
    <div id="headerRight">
        <?php if($login) { ?>
            <button type="button" id="headerLoginButton" onclick="toggleUserBox()">
                <p><?php echo $username; ?></p>
            </button>
            <div id="headerUserBox">
                <h4><?php echo $username."<br>".$email; ?></h4>
                <a href="hub.php"><p>Zum Dashboard</p></a>
                <a href="create.php"><p>Neue Umfrage erstellen</p></a>
                <a href="Abmelden.php"><p>Abmelden</p></a>
            </div>
        <?php } else { ?>
            <a href="Anmelden.html">
                <button type="button"><p>Anmelden</p></button>
            </a>
        <?php } ?>
        <form method="get" action="switchtheme.php">
            <input type="hidden" name="themebefore" value="<?php echo intval($darktheme); ?>">
            <input type="hidden" name="urlbefore" value="<?php echo $url; ?>">
            <button type="Submit"><p> <?php echo $themeEmoji; ?> Theme wechseln</p></button>
        </form>
    </div>
    <div id="headerTitle">
        <a href="index.php"><h2>Terminfinder</h2></a>
    </div>
    <script>
        function toggleUserBox() {
            document.getElementById("headerUserBox").classList.toggle("show");
        }

        document.body.onclick = function(event) {
            if (!document.getElementById('headerRight').contains(event.target)){
                var dropdown = document.getElementById("headerUserBox");
                if(dropdown != null){
                    if (dropdown.classList.contains('show')) {
                        dropdown.classList.remove('show');
                    }
                }
            }
        } 
    </script>
</header>