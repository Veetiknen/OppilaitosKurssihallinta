<?php
require '../yhteys.php';
require '../template.php';

renderHeader("Lisää Opettaja");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $etunimi = $_POST["etunimi"] ?? '';
    $sukunimi = $_POST["sukunimi"] ?? '';
    $aine = $_POST["aine"] ?? '';

    if(!empty($etunimi) && !empty($sukunimi) && !empty($aine)) {
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
           echo "<p class='warning'>Virhe lisättäessä: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    } else {
        echo "<p class='warning'>Kaikki kentät ovat pakollisia</p>";
    }
}
?>

<form method="post">
    <label>Etunimi:<br><input type="text" name="etunimi" required></label><br><br>
    <label>Sukunimi:<br><input type="text" name="sukunimi" required></label><br><br>
    <label>Aine:<br><input type="text" name="aine" required></label><br><br>
    <button type="submit" class="btn">Lisää</button>
    <a href="lista.php" class="btn">Takaisin opettajalistaan</a>
</form>

<?php renderFooter(); ?>
