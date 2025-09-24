<?php
require '../yhteys.php';
require '../template.php';

if (!isset($_GET['opiskelija'])) {
    die("Opiskelijaa ei valittu");
}

$opiskelija_id = (int)$_GET['opiskelija'];

// Haetaan opiskelijan tiedot
$sql_lause = "SELECT etunimi, sukunimi, vuosikurssi FROM opiskelijat WHERE opiskelija_numero = ?";
$kysely = $yhteys->prepare($sql_lause);
$kysely->execute([$opiskelija_id]);
$opiskelija = $kysely->fetch();
if (!$opiskelija) {
    die("Opiskelijaa ei löytynyt");
}

// --- Haetaan opiskelijan kurssit (mihin hän on kirjautunut) ---
$sql_lause = "SELECT k.id, k.nimi 
              FROM kurssikirjautumisilla kk
              JOIN kurssit k ON kk.kurssi = k.id
              WHERE kk.opiskelija=? 
              ORDER BY k.nimi";
$kysely = $yhteys->prepare($sql_lause);
$kysely->execute([$opiskelija_id]);
$kurssit = $kysely->fetchAll(PDO::FETCH_ASSOC);

// --- Haetaan opiskelijan kurssien sessiot ---
$sql_lause = "SELECT s.id, s.viikonpaiva, s.aloitus, s.lopetus, k.nimi AS kurssi_nimi
        FROM kurssisessiot s
        JOIN kurssit k ON s.kurssi_id = k.id
        JOIN kurssikirjautumisilla kk ON kk.kurssi = k.id
        WHERE kk.opiskelija=?
        ORDER BY FIELD(s.viikonpaiva,'ma','ti','ke','to','pe'), s.aloitus";
$kysely = $yhteys->prepare($sql_lause);
$kysely->execute([$opiskelija_id]);
$sessiot = $kysely->fetchAll(PDO::FETCH_ASSOC);

renderHeader("Opiskelijan sessiot – " . htmlspecialchars($opiskelija['etunimi']." ".$opiskelija['sukunimi']));
?>

<p><strong>Nimi:</strong> <?= htmlspecialchars($opiskelija['etunimi']." ".$opiskelija['sukunimi']) ?><br>
<strong>Vuosikurssi:</strong> <?= htmlspecialchars($opiskelija['vuosikurssi']) ?></p>

<h3>Opiskelijan kurssien sessiot</h3>
<table style="border-collapse: collapse; width: 100%; margin-bottom:20px;">
    <tr>
        <th style="border:1px solid #ddd;padding:5px;">Kurssi</th>
        <th style="border:1px solid #ddd;padding:5px;">Päivä</th>
        <th style="border:1px solid #ddd;padding:5px;">Aika</th>
    </tr>
    <?php foreach($sessiot as $s): ?>
    <tr>
        <td style="border:1px solid #ddd;padding:5px;"><?= htmlspecialchars($s['kurssi_nimi']) ?></td>
        <td style="border:1px solid #ddd;padding:5px;"><?= htmlspecialchars($s['viikonpaiva']) ?></td>
        <td style="border:1px solid #ddd;padding:5px;"><?= $s['aloitus'] ?>:00 - <?= $s['lopetus'] ?>:00</td>
    </tr>
    <?php endforeach; ?> 
    <?php if (empty($sessiot)): ?>
    <tr>
        <td colspan="3" style="border:1px solid #ddd;padding:5px; text-align:center; color:#666;">
            Ei sessioita tällä hetkellä
        </td>
    </tr>
    <?php endif; ?>
</table>

<a href="viikkonakyma.php?opiskelija=<?= $opiskelija_id ?>" 
   class="btn" style="margin-top:20px; display:inline-block;">Takaisin viikkonäkymään</a>

<?php renderFooter(); ?>
