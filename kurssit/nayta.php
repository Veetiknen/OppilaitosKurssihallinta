<?php
require '../yhteys.php';

if (!isset($_GET['id'])) {
    die("Kurssin ID puuttuu.");
}
$id = (int)$_GET['id'];

$sql_lause = "
SELECT 
    k.id,
    k.nimi,
    k.kuvaus,
    k.alkupäivä,
    k.loppupäivä,
    CONCAT(o.etunimi, ' ', o.sukunimi) AS opettaja_nimi,
    t.nimi AS tila_nimi,
    GROUP_CONCAT(CONCAT(os.etunimi, ' ', os.sukunimi, ' (', os.vuosikurssi, ')') SEPARATOR ', ') AS opiskelijat
FROM kurssit k
JOIN opettajat o ON k.opettaja = o.tunnusnumero
JOIN tilat t ON k.tila = t.id
LEFT JOIN kurssikirjautumisilla kk ON kk.kurssi = k.id
LEFT JOIN opiskelijat os ON kk.opiskelija = os.opiskelija_numero
WHERE k.id = :id
GROUP BY k.id, k.nimi, k.kuvaus, k.alkupäivä, k.loppupäivä, opettaja_nimi, tila_nimi
";

try {
    $kysely = $yhteys->prepare($sql_lause);
    $kysely->bindParam(':id', $id, PDO::PARAM_INT);
    $kysely->execute();
    $kurssi = $kysely->fetch();
    if (!$kurssi) {
        die("Kurssia ei löytynyt.");
    }
} catch (PDOException $e) {
    die("VIRHE: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <title>Kurssin tiedot</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 600px; }
        .back { margin-bottom: 15px; display: inline-block; }
    </style>
</head>
<body>
<div class="container">
    <a href="lista.php" class="back">&laquo; Takaisin kursseihin</a>
    <h2><?= htmlspecialchars($kurssi['nimi']) ?></h2>
    <p><strong>Kuvaus:</strong> <?= nl2br(htmlspecialchars($kurssi['kuvaus'])) ?></p>
    <p><strong>Alku:</strong> <?= htmlspecialchars($kurssi['alkupäivä']) ?></p>
    <p><strong>Loppu:</strong> <?= htmlspecialchars($kurssi['loppupäivä']) ?></p>
    <p><strong>Opettaja:</strong> <?= htmlspecialchars($kurssi['opettaja_nimi']) ?></p>
    <p><strong>Tila:</strong> <?= htmlspecialchars($kurssi['tila_nimi']) ?></p>
    <p><strong>Opiskelijat:</strong> 
    <?php if (empty($kurssi['opiskelijat'])): ?>
        Ei ilmoittautuneita 
        <a href="lisaa_opiskelija.php?kurssi=<?= $kurssi['id'] ?>">
            <button>Lisää opiskelija</button>
        </a>
    <?php else: ?>
        <?= htmlspecialchars($kurssi['opiskelijat']) ?>
        <br><br>
        <a href="lisaa_opiskelija.php?kurssi=<?= $kurssi['id'] ?>">
            <button>Lisää opiskelija</button>
        </a>
    <?php endif; ?>
</p>
</div>
</body>
</html>
