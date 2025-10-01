<?php
require '../yhteys.php';
require '../template.php';

$virhe = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nimi = trim($_POST["nimi"] ?? '');
    $kapasiteetti = (int)($_POST["kapasiteetti"] ?? 0);

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
            echo "<p class='warning'>Virhe tallennettaessa: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
}
renderHeader("Lisää Tila");
?>

<?php if (!empty($virhe)) echo "<p class='warning'>$virhe</p>"; ?>

<form method="post">
    <label>Tilan nimi:<br>
        <input type="text" name="nimi" required>
    </label><br><br>

    <label>Kapasiteetti:<br>
        <input type="number" name="kapasiteetti" min="1" required>
    </label><br><br>

    <button type="submit" class="btn">Lisää Tila</button>
    <a href="lista.php" class="btn">Takaisin tilalistaan</a>
</form>

<?php renderFooter(); ?>
