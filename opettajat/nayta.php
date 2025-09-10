<?php
require '../yhteys.php';

if (!isset($_GET['id'])) {
    die("Opettajaa ei valittu");
}

$tunnusnumero = $_GET['id'];

// Hae opettajan tiedot
try {
    $sql_lause = "SELECT * FROM opettajat WHERE tunnusnumero = ?";
    $kysely = $yhteys->prepare($sql_lause);
    $kysely->execute([$tunnusnumero]);
    $opettaja = $kysely->fetch();
    if (!$opettaja) {
        die("Opettajaa ei löytynyt");
    }
} catch (PDOException $e) {
    die("VIRHE: " . $e->getMessage());
}

// Hae opettajan kurssit
try {
    $sql = "SELECT k.nimi, k.alkupäivä, k.loppupäivä, t.nimi AS tila_nimi
            FROM kurssit k
            JOIN tilat t ON k.tila = t.id
            WHERE k.opettaja = ?";
    $kysely = $yhteys->prepare($sql);
    $kysely->execute([$tunnusnumero]);
    $kurssit = $kysely->fetchAll();
} catch (PDOException $e) {
    die("VIRHE: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <title>Opettaja: <?= htmlspecialchars($opettaja['etunimi'] . ' ' . $opettaja['sukunimi']) ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 80%; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
<h2>Opettaja: <?= htmlspecialchars($opettaja['etunimi'] . ' ' . $opettaja['sukunimi']) ?></h2>

<p><strong>Tunnusnumero:</strong> <?= htmlspecialchars($opettaja['tunnusnumero']) ?></p>
<p><strong>Aine:</strong> <?= htmlspecialchars($opettaja['aine']) ?></p>

<h3>Kurssit joita opettaa</h3>

<?php if (count($kurssit) > 0): ?>
<table>
<tr>
    <th>Kurssi</th>
    <th>Alku</th>
    <th>Loppu</th>
    <th>Tila</th>
</tr>
<?php foreach ($kurssit as $k): ?>
<tr>
    <td><?= htmlspecialchars($k['nimi']) ?></td>
    <td><?= htmlspecialchars($k['alkupäivä']) ?></td>
    <td><?= htmlspecialchars($k['loppupäivä']) ?></td>
    <td><?= htmlspecialchars($k['tila_nimi']) ?></td>
</tr>
<?php endforeach; ?>
</table>
<?php else: ?>
<p>Opettajalla ei ole kursseja.</p>
<?php endif; ?>

<p><a href="lista.php">Takaisin opettaja listaan</a></p>
</body>
</html>
