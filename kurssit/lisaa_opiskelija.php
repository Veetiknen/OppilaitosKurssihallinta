<?php
require '../yhteys.php';
require '../template.php';

renderHeader("Lisää Opiskelija");

if (!isset($_GET['kurssi'])) {
    die("Kurssin ID puuttuu.");
}
$kurssi_id = (int)$_GET['kurssi'];

// Haetaan kurssin nimi
$kurssi = $yhteys->prepare("SELECT nimi FROM kurssit WHERE id = :id");
$kurssi->execute([':id' => $kurssi_id]);
$kurssi = $kurssi->fetch();
if (!$kurssi) {
    die("Kurssia ei löytynyt.");
}

// Haetaan kaikki opiskelijat
$opiskelijat = $yhteys->query("SELECT opiskelija_numero, CONCAT(etunimi, ' ', sukunimi, ' (', vuosikurssi, ')') AS nimi FROM opiskelijat")->fetchAll();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $opiskelija_id = $_POST['opiskelija'] ?? '';

    if (!empty($opiskelija_id)) {
        try {
            $sql_lause = "INSERT INTO kurssikirjautumisilla (opiskelija, kurssi, Kirjautumispäivä) 
                    VALUES (:opiskelija, :kurssi, NOW())";
            $kysely = $yhteys->prepare($sql_lause);
            $kysely->bindParam(':opiskelija', $opiskelija_id);
            $kysely->bindParam(':kurssi', $kurssi_id);     
            $kysely->execute();

            header("Location: nayta.php?id=" . $kurssi_id);
            exit;
        } catch (PDOException $e) {
            echo "Virhe lisättäessä opiskelijaa kurssille: " . $e->getMessage();
        }
    } else {
        echo "Valitse opiskelija.";
    }
}
?>

<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <title>Lisää opiskelija kurssille</title>
</head>
<body>
    <h2>Lisää opiskelija kurssille: <?= htmlspecialchars($kurssi['nimi']) ?></h2>

    <form method="post">
        <label>Opiskelija:<br>
            <select name="opiskelija" required>
                <option value="">-- Valitse opiskelija --</option>
                <?php foreach ($opiskelijat as $o): ?>
                    <option value="<?= $o['opiskelija_numero'] ?>">
                        <?= htmlspecialchars($o['nimi']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label><br><br>

        <button type="submit" class="btn">Lisää kurssille</button>
    </form>

    <p><a href="nayta.php?id=<?= $kurssi_id ?>">&laquo; Takaisin kurssin tietoihin</a></p>
</body>
</html>
<?php renderFooter(); ?>
