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
            if(isset($_POST["sex"])){
                addPatient();
                $_SESSION["namn"] = $_POST["name"] ." " . $_POST["lastname"];
                echo $_POST["pnr"];
                header("Location: minaSidor.php");
                die();
            }
            header("Location: skapaAnvändare.php");
            die();
        }  
    }
    else{
        //Här dör loginen för patient som reggar sig genom att först försöka logga in.
        header("Location: patientLogin.php");
        die();
    }  
?>