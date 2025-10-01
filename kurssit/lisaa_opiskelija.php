<?php
require '../yhteys.php';
require '../template.php';  

if (!isset($_GET['kurssi'])) {
    die("Kurssin ID puuttuu.");
}
$kurssi_id = (int)$_GET['kurssi'];

$virheviesti = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $opiskelija_id = $_POST['opiskelija'] ?? '';

    if (!empty($opiskelija_id)) {
        try {
            // Tarkistetaan ensin, onko opiskelija jo kurssilla
            $tarkistus = $yhteys->prepare("SELECT COUNT(*) FROM kurssikirjautumisilla WHERE opiskelija = :opiskelija AND kurssi = :kurssi");
            $tarkistus->execute([
                ':opiskelija' => $opiskelija_id,
                ':kurssi' => $kurssi_id
            ]);
            $on_jo = $tarkistus->fetchColumn();

            if ($on_jo > 0) {
                $virheviesti = "Opiskelija on jo ilmoittautunut tälle kurssille.";
            } else {
                // Lisätään opiskelija kurssille
                $sql_lause = "INSERT INTO kurssikirjautumisilla (opiskelija, kurssi, Kirjautumispäivä) 
                              VALUES (:opiskelija, :kurssi, NOW())";
                $kysely = $yhteys->prepare($sql_lause);
                $kysely->bindParam(':opiskelija', $opiskelija_id, PDO::PARAM_INT);
                $kysely->bindParam(':kurssi', $kurssi_id, PDO::PARAM_INT);     
                $kysely->execute();

                header("Location: nayta.php?id=" . $kurssi_id);
                exit;
            }
        } catch (PDOException $e) {
            $virheviesti = "Virhe lisättäessä opiskelijaa kurssille: " . $e->getMessage();
        }
    } else {
        $virheviesti = "Valitse opiskelija.";
    }
}

renderHeader("Lisää opiskelija kurssille");

// Haetaan kurssin tiedot
$kurssi = $yhteys->prepare("SELECT nimi FROM kurssit WHERE id = :id");
$kurssi->execute([':id' => $kurssi_id]);
$kurssi = $kurssi->fetch();
if (!$kurssi) {
    die("Kurssia ei löytynyt.");
}

// Haetaan opiskelijat
$opiskelijat = $yhteys->query("SELECT opiskelija_numero, CONCAT(etunimi, ' ', sukunimi, ' (', vuosikurssi, ')') AS nimi FROM opiskelijat ORDER BY sukunimi")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <title>Lisää opiskelija kurssille</title>
    <style>
        .virhe {
            color: red;
            font-weight: bold;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <h2>Lisää opiskelija kurssille: <?= htmlspecialchars($kurssi['nimi']) ?></h2>

    <?php if (!empty($virheviesti)): ?>
        <p class="virhe"><?= htmlspecialchars($virheviesti) ?></p>
    <?php endif; ?>

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

    <p><a class="btn" href="nayta.php?id=<?= $kurssi_id ?>">&laquo; Takaisin kurssin tietoihin</a></p>
</body>
</html>

<?php renderFooter(); ?>
