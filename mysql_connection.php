<?php
    $server="localhost";
    $user = "root";
    $password = "";
    $datenbank = "Terminfinder";
    $port=3308;
    $connection = mysqli_connect($server,$user,$password,$datenbank,$port);

    if (mysqli_connect_errno()) {
        echo ('mysql connection error: ' . mysqli_connect_error());
        exit();
    }
?>