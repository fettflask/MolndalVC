<?php
    session_start();
    session_destroy();

    if(!isset($_SESSION["namn"])){
        header("Location: patientLogin.php");
        $_SESSION["error"] = "Felaktikt personnummer";
        die();
    }

    echo $_SESSION["namn"];
    echo $_SESSION["pnr"];
?>