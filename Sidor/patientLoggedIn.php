<?php
    session_start();
    $_SESSION["timeout"] = 300;
    include 'Funktioner/funktioner.php';

    $pdo = new PDO("mysql:dbname=grupp6;host=localhost", "sqllab", "Hare#2022");
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if(isset($_POST["name"])){
        addPatientFull($pdo);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../IMG/favicon.png">
    <title>Mölndals Vårdcentral</title>
    <link rel="stylesheet" href="../Stylesheets/headerStyle.css">

    <script type="text/javascript">
        window.onload = function() {
            document.getElementById('loading').style.display = 'block';
            setTimeout(function() {
                document.getElementById('loading').style.display = 'none';
                document.getElementById('content').style.display = 'block';
                document.getElementById('header').style.display = 'flex';
            }, 2000);
            var shouldRunOnLoad = false;
        };   
    </script>
                            

</head>

<body>
    <?php echoHead(); ?>
   
    <div id="loading" style="display: none; ">
        <img src="../IMG/bankID.png" alt="Öppna BankID">
    </div>
    <div id="content" style="display: none;">
    <?php

        if (isset($_POST["pnr"])){
            $_SESSION["pnr"] = $_POST["pnr"];
            $_SESSION["timeout"] = 300;
            //Loggar in på webbuser
            curlSetup();

            //Hämtar alla patienters personnummer från ERP
            $patientPnr = curlGetData('api/resource/Patient?fields=["uid"]&limit_page_length=None');
            
            //Går igenom de hämtade personnumren och kollar om det inskrivna matchar med något
            $validCheck = false;
            foreach($patientPnr as $row){
                foreach($row as $row2){   
                    if($row2["uid"] == $_POST["pnr"]){
                        $validCheck = true;
                        break;
                    }
                }
            }

            //Om det angivna personnumret finns i ERP
            if($validCheck){
                
                //Går igenom databasen och kollar om det angivna PNR finns i DB
                $pnrIDarabas = false;                
                foreach($pdo ->query("select * from patient;") as $row) {
                    if($row["pnr"] == $_POST["pnr"]){
                        $pnrIDarabas = true;
                        $_SESSION["namn"] = $row["fullNamn"];
                        $_SESSION["pnr"] = $row["pnr"];
                        break;
                    }
                }

                //Om det fanns i databasen
                if($pnrIDarabas){
                    echo '<script>
                            window.setTimeout(function() {
                            window.location = "minaSidor.php";
                            }, 2000);
                        </script>';
                    
                }
                //Om inte i DB
                else{
                    addPatientDB($pdo);
                    echo '<script>
                            window.setTimeout(function() {
                            window.location = "minaSidor.php";
                            }, 2000);
                        </script>';
                }
                
  
            }
            //Om pnr Inte finns i ERP
            else{
                header("Location: skapaAnvändare.php");
            }  
        }
        else{
            
            header("Location: patientLogin.php");
            die();
        }
    
    ?>
    </div>

    <script>
        function capitalizeInput(event) {
            let input = event.target;
            let value = input.value.trim();

            value = value.replace(/[^a-zA-ZåäöÅÄÖ\s-]/g, '');

            input.value = value.replace(/(^|\s|-)([a-zåäö])/gu, function(match, p1, p2) {
                console.log("Matched Letter:", p2);
                switch (p2) {
                    case 'å': return p1 + 'Å';
                    case 'ä': return p1 + 'Ä';
                    case 'ö': return p1 + 'Ö';
                    default: return p1 + p2.toUpperCase();
                }
            });
        }

        document.getElementById('name').addEventListener('input', capitalizeInput);
        document.getElementById('lastname').addEventListener('input', capitalizeInput);
    </script>  
</body>
</html>