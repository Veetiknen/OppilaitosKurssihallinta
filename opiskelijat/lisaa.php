<?php
require '../yhteys.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $etunimi = $_POST["etunimi"] ?? '';
    $sukunimi = $_POST["sukunimi"] ?? '';
    $syntymapaiva = $_POST["syntymapaiva"] ?? '';
    $vuosikurssi = $_POST["vuosikurssi"] ?? '';

    if(!empty($etunimi) && !empty($sukunimi) && !empty($syntymapaiva) && empty($vuosikurssi)) {
        try {
            $sql_lause = "INSERT INTO opiskelijat 
                    (etunimi, sukunimi, syntymäpäivä, vuosikurssi)
                    VALUES (:etunimi, :sukunimi, :syntymapaiva, :vuosikurssi)";
            $kysely = $yhteys->prepare($sql_lause);
            $kysely->bindParam(':etunimi', $etunimi);
            $kysely->bindParam(':sukunimi', $sukunimi);
            $kysely->bindParam(':syntymapaiva', $syntymapaiva);
            $kysely->bindParam(':vuosikurssi', $vuosikurssi);
            $kysely->execute();

            header("Location: lista.php");
            exit;

        } catch (PDOException $e) {
           echo "Virhe lisättäessä: " . $e->getMessage();
        }
    } else {
        echo "Kaikki kentät ovat pakollisia";
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
    <form method="post">
        <label>Etunimi:<br><input type="text" name="etunimi"></label><br><br>
        <label>Sukunimi:<br><input type="text" name="sukunimi"></label><br><br>
        <label>Syntymäpäivä:<br><input type="date" name="syntymapaiva"></label><br><br>
        <label>Vuosikurssi:<br><input type="number" name="vuosikurssi" min="1" max="3"></label><br><br>
        <button type="submit">Lisää</button>
    </form>
    <p><a href="lista.php">Takaisin opiskelijalistaan</a></p>
</body>
</html>
