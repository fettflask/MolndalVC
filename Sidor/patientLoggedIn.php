<?php
    session_start();
    $_SESSION["timeout"] = 300;
    include 'Funktioner/funktioner.php';

    if (isset($_POST["pnr"])){
        $_SESSION["pnr"] = $_POST["pnr"];
        $_SESSION["timeout"] = 300;
        //Loggar in på webbuser
        curlSetup();

        //Hämtar alla patienters personnummer från ERP
        $patientPnr = curlGetData('api/resource/Patient?filters={"uid":"'. $_POST["pnr"] .'"}&fields=["uid","name"]&limit_page_length=None');

        //Om det angivna personnumret finns i ERP
        if(!empty($patientPnr["data"])){
            $_SESSION["namn"] = $patientPnr["data"]["0"]["name"];
            header("Location: minaSidor.php");
            die();
        }
        //Om pnr Inte finns i ERP
        else{
            header("Location: skapaAnvändare.php");
            die();
        }  
    }
    else{
        header("Location: patientLogin.php");
        die();
    }  
?>