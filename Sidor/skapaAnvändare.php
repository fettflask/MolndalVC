<?php
    include 'Funktioner/funktioner.php';
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../IMG/favicon.png">
    <link rel="stylesheet" href="../Stylesheets/headerStyle.css">
    <link rel="stylesheet" href="../Stylesheets/skapaStyle.css">
    <link rel="stylesheet" href="../Stylesheets/footerStyle.css">
    <title>Registrera</title>
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

<body>
    <?php echoHead(); ?>

    <main>
        <div id="skapaForm">
            <div id="centerForm">
                <h1>Registrering</h1>
                <form method="POST" action="patientLoggedIn.php" onsubmit="return handleSubmit(event)">
                    <?php if(!isset($_SESSION["pnr"])): ?>
                        <div class="inputField">
                            Personnummer:
                            <input type="text" name="pnr" class="input" id="pnr" pattern="[0-9]{8}-[0-9]{4}" required maxlength="13" placeholder="YYYYMMDD-XXXX" title="Format: YYYYMMDD-XXXX">
                        </div>
                    <?php else:?>
                        <div class="inputField">
                            Personnummer:
                        <?php 
                            echo '<input type="text" name="pnr" id="pnr" class="input" value ="'. $_SESSION["pnr"] . '" disabled >';
                        ?>
                        </div>
                    <?php endif ?>
                    
                    <div class="inputField">
                        Förnamn:
                        <input type="text" name="name" class="input" id="name" required pattern="[A-Za-zÅåÄäÖö]+(-[A-Za-zÅåÄäÖö]+)?" title="Endast bokstäver" placeholder="Förnamn">
                    </div>
                    <div class="inputField">
                        Efternamn:
                        <input  type="text" name="lastname" class="input" id="lastname" required pattern="[A-Za-zÅåÄäÖö]+(-[A-Za-zÅåÄäÖö]+)?" title="Endast bokstäver" placeholder="Efternamn">
                    </div>
                    <div id="konSelect">
                        Kön:
                        <select name="sex" id="selector" required title="Välj från listan">
                            <option selected hidden disabled>Välj kön</option>
                                <?php
                                    getGender();  
                                ?>
                            </select>
                        </div>
                    <input type="submit" id="registrera" value='Registrera'>
                </form>
            </div>
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
    </main>

    <footer>

    </footer>

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

        const pnrInput = document.getElementById("pnr");
        pnrInput.addEventListener("input", function () {
            let value = pnrInput.value.replace(/\D/g, '');
            if (value.length > 8) {
                value = value.slice(0, 8) + '-' + value.slice(8, 12);
            }
            pnrInput.value = value;
        });

    </script>
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
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('form');
            form.onsubmit = function (event) {
                event.preventDefault();
                const pnrInput = document.getElementById('pnr');

                if (pnrInput.disabled) {
                    console.log('PNR input is disabled, submitting the form...');
                    pnrInput.disabled = false;
                    form.submit();
                } else {
                    console.log('PNR input is enabled, showing the modal...');
                    showModal('messageBox', form);
                }
            };
        });

        function showModal(modalId, form) {
            const modal = document.getElementById(modalId);
            if (modal) {
                setTimeout(() => {
                    modal.style.display = 'flex';
                }, 750);
            }

            let formToSubmit = form;

            if (formToSubmit) {
                setTimeout(() => {
                    const disPnrInput = document.getElementById('disPnr');
                    if (disPnrInput && disPnrInput.disabled) {
                        disPnrInput.disabled = false;
                    }

                    formToSubmit.submit();
                    formToSubmit = null;
                }, 3000);
            }
        }
    </script>

    <?php echoFooter(); ?>
</body>
</html>