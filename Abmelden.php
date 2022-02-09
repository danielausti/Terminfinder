<?php
    session_start();
    if(isset($_SESSION["user_login"])){
        unset($_SESSION["user_login"]);
    }
    header("Location: index.php")
?>