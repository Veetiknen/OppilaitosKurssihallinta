<?php
require '../yhteys.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $etunimi = $_POST['etunimi'] ?? '';
    $sukunimi = $_POST['sukunimi'] ?? '';
    $aine = $_POST['aine'] ?? '';

    if (!empty($etunimi) && !empty($sukunimi) && !empty($aine)) {
        try {
            $sql = "INSERT INTO opettajat (etunimi, sukunimi, aine) VALUES (?, ?, ?)";
            $stmt = $yhteys->prepare($sql);
            $stmt->execute([$etunimi, $sukunimi, $aine]);

            header("Location: lista.php");
            exit;
        } catch (PDOException $e) {
            echo "<p style='color: red;'>Virhe tallennuksessa: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color: red;'>Kaikki kentät ovat pakollisia.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <title>Lisää opettaja</title>
</head>
<body>
<h2>Lisää opettaja</h2>
<form method="post" action="">
    <p><label>Etunimi:<br><input type="text" name="etunimi" required></label></p>
    <p><label>Sukunimi:<br><input type="text" name="sukunimi" required></label></p>
    <p><label>Aine:<br><input type="text" name="aine" required></label></p>
    <p><button type="submit">Lisää opettaja</button></p>
</form>
<p><a href="lista.php">Takaisin listaan</a></p>
</body>
</html>
