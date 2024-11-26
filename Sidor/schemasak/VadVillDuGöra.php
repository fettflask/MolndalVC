<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Huvudsida</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
        }
        button {
            padding: 15px 30px;
            font-size: 16px;
            margin: 10px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            background-color: #007BFF;
            color: white;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Välkommen</h1>
    <p>Välj ett alternativ nedan:</p>
    <form action="FormulärBokatidSTARTHÄR.php" method="get">
        <button type="submit">Gör en ny bokning</button>
    </form>
    <form action="bokningsHantering.php" method="get">
        <button type="submit">Hantera dina bokningar</button>
    </form>
</body>
</html>
