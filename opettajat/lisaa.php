<?php
require '../yhteys.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $etunimi = $_POST['etunimi'] ?? '';
    $sukunimi = $_POST['sukunimi'] ?? '';
    $aine = $_POST['aine'] ?? '';

    if (!empty($etunimi) && !empty($sukunimi) && !empty($aine)) {
        try {
            $sql_lause = "INSERT INTO opettajat (etunimi, sukunimi, aine) 
                    VALUES (:etunimi, :sukunimi, :aine)";
            $kysely = $yhteys->prepare($sql_lause);
            $kysely->bindParam(':etunimi', $etunimi);
            $kysely->bindParam(':sukunimi', $sukunimi);
            $kysely->bindParam(':aine', $aine);
            $kysely->execute();

            header("Location: lista.php");
            exit;
        } catch (PDOException $e) {
            echo "Virhe tallennuksessa: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "Kaikki kentät ovat pakollisia.";
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
    <p><label>Etunimi:<br><input type="text" name="etunimi"></label></p>
    <p><label>Sukunimi:<br><input type="text" name="sukunimi"></label></p>
    <p><label>Aine:<br><input type="text" name="aine"></label></p>
    <p><button type="submit">Lisää opettaja</button></p>
</form>
<p><a href="lista.php">Takaisin listaan</a></p>
</body>
</html>
