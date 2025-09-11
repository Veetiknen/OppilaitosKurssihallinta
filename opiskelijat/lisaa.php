<?php
require '../yhteys.php';
require '../template.php';

renderHeader("Lisää Opiskelija");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $etunimi = $_POST["etunimi"] ?? '';
    $sukunimi = $_POST["sukunimi"] ?? '';
    $syntymapaiva = $_POST["syntymapaiva"] ?? '';
    $vuosikurssi = $_POST["vuosikurssi"] ?? '';

    if(!empty($etunimi) && !empty($sukunimi) && !empty($syntymapaiva) && !empty($vuosikurssi)) {
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
           echo "<p class='error'>Virhe lisättäessä: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    } else {
        echo "<p class='error'>Kaikki kentät ovat pakollisia</p>";
    }
}
?>

<form method="post">
    <label>Etunimi:<br><input type="text" name="etunimi" required></label><br><br>
    <label>Sukunimi:<br><input type="text" name="sukunimi" required></label><br><br>
    <label>Syntymäpäivä:<br><input type="date" name="syntymapaiva" required></label><br><br>
    <label>Vuosikurssi:<br><input type="number" name="vuosikurssi" min="1" max="3" required></label><br><br>
    <button type="submit" class="btn">Lisää</button>
    <a href="lista.php" class="btn">Takaisin opiskelijalistaan</a>
</form>

<?php renderFooter(); ?>
