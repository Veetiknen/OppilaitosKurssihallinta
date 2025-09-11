<?php
require '../yhteys.php';

$virhe = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nimi = trim($_POST["nimi"]);
    $kapasiteetti = (int) $_POST["kapasiteetti"];

    if (empty($nimi) || $kapasiteetti <= 0) {
        $virhe = "Täytä kaikki kentät oikein.";
    } else {
        $sql_lause = "INSERT INTO tilat (nimi, kapasiteetti) VALUES (:nimi, :kapasiteetti)";
        try {
            $kysely = $yhteys->prepare($sql_lause);
            $kysely->bindParam(":nimi", $nimi);
            $kysely->bindParam(":kapasiteetti", $kapasiteetti);
            $kysely->execute();
            header("Location: lista.php");
            exit;
        } catch (PDOException $e) {
            die("Virhe tallennettaessa: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <title>Lisää Tila</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f4f6f8;
            color: #333;
        }

        a {
            text-decoration: none;
            color: #0070c0;
        }

        a:hover {
            text-decoration: underline;
        }

        nav {
            background-color: #dde6f1;
            width: 220px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 60px;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }

        nav ul {
            list-style: none;
            padding: 0;
        }

        nav li {
            padding: 10px 20px;
        }

        nav li:hover {
            background-color: #c7d9f0;
        }

        main {
            margin-left: 240px;
            padding: 20px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            background: white;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }

        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #e2e9f7;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .btn {
            background-color: #0070c0;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #005a8f;
        }

        .warning {
            color: red;
            font-weight: bold;
        }

        form input,
        form select {
            padding: 5px;
            margin-bottom: 10px;
            width: 300px;
        }
    </style>
</head>
<body>
<h2>Lisää uusi tila</h2>

<?php if (!empty($virhe)) echo "<p class='warning'>$virhe</p>"; ?>

<form method="post" action="">
    <label for="nimi">Tilan nimi:</label><br>
    <input type="text" name="nimi" id="nimi" required><br>

    <label for="kapasiteetti">Kapasiteetti:</label><br>
    <input type="number" name="kapasiteetti" id="kapasiteetti" min="1" required><br><br>

    <input type="submit" class="btn" value="Lisää Tila">
</form>

<p><a href="lista.php">⬅ Takaisin tilalistaan</a></p>
</body>
</html>
