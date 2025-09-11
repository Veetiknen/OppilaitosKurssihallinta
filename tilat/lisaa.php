<?php
require '../yhteys.php';

$virhe = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nimi = trim($_POST["nimi"]);
    $kapasiteetti = (int) $_POST["kapasiteetti"];

    if (empty($nimi) || $kapasiteetti <= 0) {
        $virhe = "Täytä kaikki kentät oikein.";
    } else {
        $sql = "INSERT INTO tilat (nimi, kapasiteetti) VALUES (:nimi, :kapasiteetti)";
        try {
            $stmt = $yhteys->prepare($sql);
            $stmt->bindParam(":nimi", $nimi);
            $stmt->bindParam(":kapasiteetti", $kapasiteetti);
            $stmt->execute();
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
        body { font-family: Arial, sans-serif; margin: 20px; }
        input[type="text"], input[type="number"] {
            padding: 6px;
            width: 300px;
            margin-bottom: 10px;
        }
        input[type="submit"] {
            padding: 8px 16px;
            background: #2980b9;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background: #1f6391;
        }
    </style>
</head>
<body>
<h2>Lisää uusi tila</h2>

<?php if (!empty($virhe)) echo "<p style='color:red;'>$virhe</p>"; ?>

<form method="post" action="">
    <label for="nimi">Tilan nimi:</label><br>
    <input type="text" name="nimi" id="nimi" required><br>

    <label for="kapasiteetti">Kapasiteetti:</label><br>
    <input type="number" name="kapasiteetti" id="kapasiteetti" min="1" required><br><br>

    <input type="submit" value="Lisää Tila">
</form>

<p><a href="lista.php">⬅ Takaisin tilalistaan</a></p>
</body>
</html>
