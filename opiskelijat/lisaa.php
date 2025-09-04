<?php
require '../yhteys.php';

$virhe = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $etunimi = trim($_POST["etunimi"] ?? '');
    $sukunimi = trim($_POST["sukunimi"] ?? '');
    $syntymapaiva = trim($_POST["syntymapaiva"] ?? '');
    $vuosikurssi = trim($_POST["vuosikurssi"] ?? '');

    if (empty($etunimi) || empty($sukunimi) || empty($syntymapaiva) || empty($vuosikurssi)) {
        $virhe = "Kaikki kentät ovat pakollisia.";
    } else {
        try {
            $sql = "INSERT INTO opiskelijat 
                    (etunimi, sukunimi, syntymäpäivä, vuosikurssi)
                    VALUES (:etunimi, :sukunimi, :syntymapaiva, :vuosikurssi)";
            $stmt = $yhteys->prepare($sql);
            $stmt->bindParam(':etunimi', $etunimi);
            $stmt->bindParam(':sukunimi', $sukunimi);
            $stmt->bindParam(':syntymapaiva', $syntymapaiva);
            $stmt->bindParam(':vuosikurssi', $vuosikurssi);
            $stmt->execute();

            // Ohjataan takaisin lista.php -sivulle
            header("Location: lista.php");
            exit;

        } catch (PDOException $e) {
            $virhe = "Virhe lisättäessä: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <title>Lisää opiskelija</title>
</head>
<body>
    <h2>Lisää uusi opiskelija</h2>
    <p><a href="lista.php">Takaisin opiskelijalistaan</a></p>

    <?php if ($virhe): ?>
        <p style="color: red;"><?= htmlspecialchars($virhe) ?></p>
    <?php endif; ?>

    <form method="post">
        <label>Etunimi:<br>
            <input type="text" name="etunimi" required>
        </label><br><br>

        <label>Sukunimi:<br>
            <input type="text" name="sukunimi" required>
        </label><br><br>

        <label>Syntymäpäivä:<br>
            <input type="date" name="syntymapaiva" required>
        </label><br><br>

        <label>Vuosikurssi:<br>
            <input type="number" name="vuosikurssi" min="1" max="10" required>
        </label><br><br>

        <button type="submit">Lisää</button>
    </form>
</body>
</html>
