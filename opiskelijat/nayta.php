<?php
require '../yhteys.php';
require '../template.php';

if (!isset($_GET['id'])) {
    die("Opiskelijaa ei valittu");
}

$opiskelija_numero = $_GET['id'];

// Hae opiskelijan tiedot
try {
    $sql_lause = "SELECT * FROM opiskelijat WHERE opiskelija_numero = ?";
    $kysely = $yhteys->prepare($sql_lause);
    $kysely->execute([$opiskelija_numero]);
    $opiskelija = $kysely->fetch();
    if (!$opiskelija) {
        die("Opiskelijaa ei löytynyt");
    }
} catch (PDOException $e) {
    die("VIRHE: " . $e->getMessage());
}

// Hae opiskelijan kurssit kirjautumisten perusteella
try {
    $sql = "SELECT k.nimi, ck.Kirjautumispäivä
            FROM kurssit k
            JOIN kurssikirjautumisilla ck ON k.id = ck.kurssi
            WHERE ck.opiskelija = ?";
    $kysely = $yhteys->prepare($sql);
    $kysely->execute([$opiskelija_numero]);
    $kurssit = $kysely->fetchAll();
} catch (PDOException $e) {
    die("VIRHE: " . $e->getMessage());
}

renderHeader("Opiskelija: " . htmlspecialchars($opiskelija['etunimi'] . ' ' . $opiskelija['sukunimi']));
?>

<p><strong>Tunnusnumero:</strong> <?= htmlspecialchars($opiskelija['opiskelija_numero']) ?></p>
<p><strong>Syntymäpäivä:</strong> <?= htmlspecialchars($opiskelija['syntymäpäivä']) ?></p>
<p><strong>Vuosikurssi:</strong> <?= htmlspecialchars($opiskelija['vuosikurssi']) ?></p>

<h3>Ilmoittautuneet kurssit</h3>

<?php if (count($kurssit) > 0): ?>
<table>
<tr>
    <th>Kurssi</th>
    <th>Ilmoittautumispäivä</th>
</tr>
<?php foreach ($kurssit as $k): ?>
<tr>
    <td><?= htmlspecialchars($k['nimi']) ?></td>
    <td><?= htmlspecialchars($k['Kirjautumispäivä']) ?></td>
</tr>
<?php endforeach; ?>
</table>
<?php else: ?>
<p>Opiskelijalla ei ole ilmoittautuneita kursseja.</p>
<?php endif; ?>

<a href="lista.php" class="btn">Takaisin opiskelijalistaan</a>

<?php renderFooter(); ?>
