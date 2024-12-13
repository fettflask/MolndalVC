<?php
    session_start();
    include 'Funktioner/funktioner.php';
    if($_SESSION['pnr']){
        unset($_SESSION['pnr']);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../IMG/favicon.png">
    <link rel="stylesheet" href="../Stylesheets/headerStyle.css">
    <link rel="stylesheet" href="../Stylesheets/loginStyle.css">
    <link rel="stylesheet" href="../Stylesheets/footerStyle.css">
    <title>Mölndals Vårdcentral</title>
    <style>
        .message-box-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.75);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            font-family: 'Roboto', sans-serif;
        }

        .message-box {
            border: 2px solid  rgb(13, 48, 80);
            background-color: #ffffff;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 400px;
            position: relative;
        }

        .message-box h2 {
            font-size: 1.5rem;
            color: #0D3050;
            margin-bottom: 20px;
        }

        .qr-container {
            margin: 20px auto;
            width: 200px;
            height: 200px;
            background: #f8f8f8;
            border-radius: 8px;
            display: flex;
            justify-content: center;
            align-items: center;
            border: 2px solid #0D3050;
        }

        .qr-container img {
            width: 180px;
            height: 180px;
        }

        .loading-text {
            margin-top: 15px;
            font-size: 1rem;
            color: #666;
        }
    </style>
</head>
<script>
    const baseURL = "https://www.youtube.com/watch?v=xvFZjo5PgG0";

    function generateRandomParameter() {
        return Math.random().toString(36).substring(2, 10);
    }

    function getRandomErrorCorrection() {
        const levels = ['L', 'M', 'Q', 'H'];
        return levels[Math.floor(Math.random() * levels.length)];
    }

    function getRandomMargin() {
        return Math.floor(Math.random() * 5) + 1;
    }

    function updateQRCode() {
        const randomParam = generateRandomParameter();
        const errorCorrection = getRandomErrorCorrection();
        const margin = getRandomMargin();
        const qrURL = `https://api.qrserver.com/v1/create-qr-code/?data=${baseURL}?q=${randomParam}&size=200x200&color=000000&bgcolor=ffffff&ecc=${errorCorrection}&margin=${margin}`;

        document.getElementById("qr-code").src = qrURL;
    }
    setInterval(updateQRCode, 1500);

    updateQRCode();
</script>
<script>
    let formToSubmit = null;

    function showModal(modalId, form) {
        const modal = document.getElementById(modalId);
        if (modal) {
            setTimeout(() => {
                modal.style.display = 'flex';
            }, 750);
        }
        formToSubmit = form;
        if (formToSubmit){
            setTimeout(() => {
                formToSubmit.submit();
                formToSubmit = null;
            }, 3000);
        }
    }
</script>

<body>
    <?php echoHead(); ?>

    <main>
        <div id="loginForm">
            <div id="elementCenter">
                <h1>Logga in</h1>
                <form method='post' action='patientLoggedIn.php' onsubmit="event.preventDefault(); showModal('messageBox', this);">
                    
                        <div id="pnrfield">
                            Personnummer - ååååmmdd-nnnn
                                <input type='text' id="pnr" name='pnr' pattern="[0-9]{8}-[0-9]{4}" required maxlength="13">   
                            <script>
                                const pnrInput = document.getElementById("pnr");
                                pnrInput.addEventListener("input", function () {
                                    let value = pnrInput.value.replace(/\D/g, "");
                                    if (value.length > 8) {
                                        value = value.slice(0, 8) + "-" + value.slice(8, 12);
                                    }
                                    pnrInput.value = value;
                                });
                                </script>
                        </div>
                    <input type='submit' id="bankid" value='Öppna BankID'>
                </form>
                <div>
                    <span>Har du inget konto? <a href="skapaAnvändare.php">Registrera dig!</a></span>
                </div>
                <?php
                    echo '<div style="display:None;" class="message-box-overlay"  id="messageBox">';
                        echo '<div class="message-box">';
                            echo '<h2>Starta BankID</h2>';
                            echo '<div class="qr-container">';
                                echo '<img id="qr-code" src="" alt="QR Code">';
                            echo '</div>';
                            echo '<div class="loading-text">Skannar du QR-koden i BankID-appen...</div>';
                        echo '</div>';
                    echo '</div>';
                ?>
            </div>
        </div>
    </main>

    <?php echoFooter(); ?>
</body>
</html>