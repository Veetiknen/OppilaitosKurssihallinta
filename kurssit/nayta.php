<?php
require '../yhteys.php';
require '../template.php';

if (!isset($_GET['id'])) {
    die("Kurssia ei valittu");
}

$kurssi_id = (int)$_GET['id'];

// Jos on lähetetty opettajan vaihto
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['uusi_opettaja'])) {
    $uusi_opettaja = (int)$_POST['uusi_opettaja'];
    if ($uusi_opettaja > 0) {
        try {
            $paivitys = $yhteys->prepare("UPDATE kurssit SET opettaja = :opettaja WHERE id = :id");
            $paivitys->bindParam(':opettaja', $uusi_opettaja, PDO::PARAM_INT);
            $paivitys->bindParam(':id', $kurssi_id, PDO::PARAM_INT);
            $paivitys->execute();
            header("Location: nayta.php?id=" . $kurssi_id);
            exit;
        } catch (PDOException $e) {
            die("VIRHE opettajaa vaihtaessa: " . $e->getMessage());
        }
    }
}

// Haetaan kurssin tiedot
try {
    $sql_lause = "
        SELECT 
            k.id,
            k.nimi,
            k.kuvaus,
            k.alkupäivä,
            k.loppupäivä,
            k.opettaja AS opettaja_id,
            CONCAT(o.etunimi, ' ', o.sukunimi) AS opettaja_nimi,
            t.nimi AS tila_nimi
        FROM kurssit k
        LEFT JOIN opettajat o ON k.opettaja = o.tunnusnumero
        JOIN tilat t ON k.tila = t.id
        WHERE k.id = :id
    ";
    $kysely = $yhteys->prepare($sql_lause);
    $kysely->bindParam(':id', $kurssi_id, PDO::PARAM_INT);
    $kysely->execute();
    $kurssi = $kysely->fetch();
    if (!$kurssi) {
        die("Kurssia ei löytynyt.");
    }
} catch (PDOException $e) {
    die("VIRHE: " . $e->getMessage());
}

// Haetaan kurssin opiskelijat
try {
    $sql_opiskelijat = "
        SELECT os.etunimi, os.sukunimi, os.vuosikurssi, kk.Kirjautumispäivä
        FROM kurssikirjautumisilla kk
        JOIN opiskelijat os ON kk.opiskelija = os.opiskelija_numero
        WHERE kk.kurssi = :id
    ";
    $stmt = $yhteys->prepare($sql_opiskelijat);
    $stmt->bindParam(':id', $kurssi_id, PDO::PARAM_INT);
    $stmt->execute();
    $opiskelijat = $stmt->fetchAll();
} catch (PDOException $e) {
    die("VIRHE: " . $e->getMessage());
}

// Haetaan kaikki opettajat valikkoa varten
try {
    $opettaja_stmt = $yhteys->query("SELECT tunnusnumero, CONCAT(etunimi,' ',sukunimi) AS nimi FROM opettajat ORDER BY sukunimi");
    $kaikki_opettajat = $opettaja_stmt->fetchAll();
} catch (PDOException $e) {
    $kaikki_opettajat = [];
}

renderHeader("Kurssi: " . htmlspecialchars($kurssi['nimi']));
?>

<p><strong>Kuvaus:</strong> <?= nl2br(htmlspecialchars($kurssi['kuvaus'])) ?></p>
<p><strong>Alku:</strong> <?= htmlspecialchars($kurssi['alkupäivä']) ?></p>
<p><strong>Loppu:</strong> <?= htmlspecialchars($kurssi['loppupäivä']) ?></p>
<p><strong>Tila:</strong> <?= htmlspecialchars($kurssi['tila_nimi']) ?></p>

<?php if (empty($kurssi['opettaja_id']) || $kurssi['opettaja_id'] == 0): ?>
    <p><strong>Opettaja:</strong> <span class="warning">Tuntematon opettaja</span></p>

    <form method="post" style="margin-top: 15px;">
        <label>Valitse opettaja:<br>
            <select name="uusi_opettaja" required>
                <option value="">-- Valitse opettaja --</option>
                <?php foreach ($kaikki_opettajat as $op): ?>
                    <option value="<?= $op['tunnusnumero'] ?>"><?= htmlspecialchars($op['nimi']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit" class="btn">Tallenna opettaja</button>
    </form>
<?php else: ?>
    <p><strong>Opettaja:</strong> <?= htmlspecialchars($kurssi['opettaja_nimi']) ?></p>
<?php endif; ?>

<h3>Ilmoittautuneet opiskelijat</h3>

<?php if (count($opiskelijat) > 0): ?>
<table>
<tr>
    <th>Etunimi</th>
    <th>Sukunimi</th>
    <th>Vuosikurssi</th>
    <th>Ilmoittautumispäivä</th>
</tr>
<?php foreach ($opiskelijat as $o): ?>
<tr>
    <td><?= htmlspecialchars($o['etunimi']) ?></td>
    <td><?= htmlspecialchars($o['sukunimi']) ?></td>
    <td><?= htmlspecialchars($o['vuosikurssi']) ?></td>
    <td><?= htmlspecialchars($o['Kirjautumispäivä']) ?></td>
</tr>
<?php endforeach; ?>
</table>
<?php else: ?>
<p>Kurssilla ei ole vielä opiskelijoita.</p>
<?php endif; ?>

<a href="lisaa_opiskelija.php?kurssi=<?= $kurssi['id'] ?>" class="btn">➕ Lisää opiskelija</a>
<a href="lista.php" class="btn">&laquo; Takaisin kurssilistaan</a>

<?php renderFooter(); ?>
