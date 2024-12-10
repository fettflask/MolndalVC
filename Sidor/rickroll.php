<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Not a magnum dong</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            background-color: #f4f4f4;
            font-family: Arial, sans-serif;
        }

        h1 {
            margin-bottom: 20px;
            color: #333333;
        }

        .qr-container {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 250px;
            height: 250px;
            background-color: #ffffff;
            border: 5px solid #000000;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            animation: pulse-border 2s ease-in-out infinite;
        }

        img {
            width: 200px; /* Fixed width */
            height: 200px; /* Fixed height */
            animation: pulse-qr 1.5s ease-in-out infinite;
        }

        /* Pulsating QR animation */

    </style>
</head>
<body>

    <div class="qr-container">
        <img id="qr-code" src="" alt="QR Code">
    </div>
    <script>
        // Base URL for Rick Roll video
        const baseURL = "https://www.youtube.com/watch?v=xvFZjo5PgG0";

        // Function to generate a random query parameter
        function generateRandomParameter() {
            return Math.random().toString(36).substring(2, 10);
        }

        // Function to generate a random error correction level (L, M, Q, H)
        function getRandomErrorCorrection() {
            const levels = ['L', 'M', 'Q', 'H'];
            return levels[Math.floor(Math.random() * levels.length)];
        }

        // Function to generate a random margin size
        function getRandomMargin() {
            return Math.floor(Math.random() * 5) + 1; // Random margin between 1 and 5
        }

        // Function to update the QR code
        function updateQRCode() {
            const randomParam = generateRandomParameter();
            const errorCorrection = getRandomErrorCorrection();
            const margin = getRandomMargin();
            const qrURL = `https://api.qrserver.com/v1/create-qr-code/?data=${baseURL}?q=${randomParam}&size=200x200&color=000000&bgcolor=ffffff&ecc=${errorCorrection}&margin=${margin}`;

            // Update the QR code image source
            document.getElementById("qr-code").src = qrURL;
        }

        // Update QR code every 2 seconds
        setInterval(updateQRCode, 1500);

        // Initialize the first QR code
        updateQRCode();
    </script>
</body>
</html>
