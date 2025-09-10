<?php
require '../yhteys.php';

if (!isset($_GET['id'])) {
    die("Tilan ID puuttuu.");
}

$tila_id = (int)$_GET['id'];

// Hae tilan tiedot
try {
    $sql_lause = "SELECT * FROM tilat WHERE id = :id";
    $kysely = $yhteys->prepare($sql_lause);
    $kysely->bindParam(':id', $tila_id, PDO::PARAM_INT);
    $kysely->execute();
    $tila = $kysely->fetch();
    if (!$tila) {
        die("Tila ei löytynyt.");
    }
} catch (PDOException $e) {
    die("VIRHE: " . $e->getMessage());
}

// Hae kurssit jotka pidetään tässä tilassa
try {
    $sql = "SELECT k.id, k.nimi, k.alkupäivä, k.loppupäivä,
                   CONCAT(o.etunimi, ' ', o.sukunimi) AS opettaja,
                   COUNT(ck.opiskelija) AS osallistujia
            FROM kurssit k
            JOIN opettajat o ON k.opettaja = o.tunnusnumero
            LEFT JOIN kurssikirjautumisilla ck ON k.id = ck.kurssi
            WHERE k.tila = :tila_id
            GROUP BY k.id, k.nimi, k.alkupäivä, k.loppupäivä, o.etunimi, o.sukunimi";
    $kysely = $yhteys->prepare($sql);
    $kysely->bindParam(':tila_id', $tila_id, PDO::PARAM_INT);
    $kysely->execute();
    $kurssit = $kysely->fetchAll();
} catch (PDOException $e) {
    die("VIRHE: " . $e->getMessage());
}

// Tarkistetaan onko ylityksiä
$onkoYlityksia = false;
foreach ($kurssit as $k) {
    if ($k['osallistujia'] > $tila['kapasiteetti']) {
        $onkoYlityksia = true;
        break;
    }
}
?>

<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <title>Tila: <?= htmlspecialchars($tila['nimi']) ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 90%; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .warning { color: red; font-weight: bold; }
    </style>
</head>
<body>
<h2>Tila: <?= htmlspecialchars($tila['nimi']) ?></h2>

<p><strong>ID:</strong> <?= htmlspecialchars($tila['id']) ?></p>
<p><strong>Kapasiteetti:</strong> <?= htmlspecialchars($tila['kapasiteetti']) ?></p>

<h3>Kurssit tässä tilassa</h3>

<?php if (count($kurssit) > 0): ?>
<table>
<tr>
    <th>Nimi</th>
    <th>Opettaja</th>
    <th>Alkupäivä</th>
    <th>Loppupäivä</th>
    <th>Osallistujat</th>
    <?php if ($onkoYlityksia): ?>
        <th>Huomio</th>
    <?php endif; ?>
</tr>
<?php foreach ($kurssit as $k): ?>
<tr>
    <td><?= htmlspecialchars($k['nimi']) ?></td>
    <td><?= htmlspecialchars($k['opettaja']) ?></td>
    <td><?= htmlspecialchars($k['alkupäivä']) ?></td>
    <td><?= htmlspecialchars($k['loppupäivä']) ?></td>
    <td><?= htmlspecialchars($k['osallistujia']) ?></td>
    <?php if ($onkoYlityksia): ?>
        <td>
            <?php if ($k['osallistujia'] > $tila['kapasiteetti']): ?>
                <span class="warning">Ylittää kapasiteetin!</span>
            <?php endif; ?>
        </td>
    <?php endif; ?>
</tr>
<?php endforeach; ?>
</table>
<?php else: ?>
<p>Ei kursseja tässä tilassa.</p>
<?php endif; ?>

<p><a href="lista.php">Takaisin tila listaan</a></p>
</body>
</html>
